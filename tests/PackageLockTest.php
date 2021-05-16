<?php

use MeesterDev\FileWrapper\File;
use MeesterDev\PackageLicenseParser\Entities\PackageInformation;
use MeesterDev\PackageLicenseParser\Parsers\AbstractParser;
use MeesterDev\PackageLicenseParser\Parsers\ParserFactory;
use PHPUnit\Framework\TestCase;

class PackageLockTest extends TestCase {
    private const PACKAGE_LOCK_VERSIONS = [1, 2];

    private static function createParser(string $path): AbstractParser {
        return ParserFactory::createForFilePath(
            __DIR__ . DIRECTORY_SEPARATOR . 'packagefiles' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path)
        );
    }

    public function testPackageLockWithoutDependencies(): void {
        foreach (static::PACKAGE_LOCK_VERSIONS as $packageLockVersion) {
            $parser = static::createParser("empty-project-npm-$packageLockVersion/package-lock.json");

            $packages = $parser->parse();
            $this->assertCount(0, $packages);
        }
    }

    public function testPublicPackagesIgnored(): void {
        foreach (static::PACKAGE_LOCK_VERSIONS as $packageLockVersion) {
            $parser = static::createParser("public-domain-project-npm-$packageLockVersion/package-lock.json");

            $packages = $parser->parse();
            $this->assertCount(0, $packages);
        }
    }

    public function testPublicPackagesIncluded(): void {
        foreach (static::PACKAGE_LOCK_VERSIONS as $packageLockVersion) {
            $parser = static::createParser("public-domain-project-npm-$packageLockVersion/package-lock.json");
            $parser->includePublicDomainLicenses();

            $packages = $parser->parse();

            $this->assertPackageNamesEquals(['quattro-stagioni'], $packages);

            /** @var PackageInformation[] $packages */
            $packages = array_column($packages, null, 'name');

            $this->assertEquals('CC0', $packages['quattro-stagioni']->licenseType);
            $this->assertEquals(PackageInformation::SOURCE_NPM, $packages['quattro-stagioni']->source);
            $this->assertInstanceOf(File::class, $packages['quattro-stagioni']->licenseFileLocation);

            $this->assertRelativePathEquals(
                $packages['quattro-stagioni'],
                ['packagefiles', "public-domain-project-npm-$packageLockVersion", 'node_modules', 'quattro-stagioni']
            );
        }
    }

    public function testLicenseFileParsingCorrect(): void {
        foreach (static::PACKAGE_LOCK_VERSIONS as $packageLockVersion) {
            $parser   = static::createParser("standard-project-npm-$packageLockVersion/package-lock.json");
            $packages = $parser->parse();

            $this->assertPackageNamesEquals(['axios', 'follow-redirects'], $packages);

            /** @var PackageInformation[] $packages */
            $packages = array_column($packages, null, 'name');

            $this->assertEquals('MIT', $packages['axios']->licenseType);
            $this->assertEquals('MIT', $packages['follow-redirects']->licenseType);

            $this->assertRelativePathEquals(
                $packages['axios'],
                ['packagefiles', "standard-project-npm-$packageLockVersion", 'node_modules', 'axios']
            );
            $this->assertRelativePathEquals(
                $packages['follow-redirects'],
                ['packagefiles', "standard-project-npm-$packageLockVersion", 'node_modules', 'follow-redirects']
            );

            $this->assertInstanceOf(File::class, $packages['axios']->licenseFileLocation);
            $this->assertInstanceOf(File::class, $packages['follow-redirects']->licenseFileLocation);

            $this->assertEquals('https://github.com/axios/axios', $packages['axios']->homepage);
            $this->assertEquals('https://github.com/follow-redirects/follow-redirects', $packages['follow-redirects']->homepage);

            $this->assertEquals('Promise based HTTP client for the browser and node.js', $packages['axios']->description);
            $this->assertEquals('HTTP and HTTPS modules that follow redirects.', $packages['follow-redirects']->description);

            $this->assertEquals(PackageInformation::SOURCE_NPM, $packages['axios']->source);
            $this->assertEquals(PackageInformation::SOURCE_NPM, $packages['follow-redirects']->source);
        }
    }

    /**
     * @param string[] $names
     * @param string[] $packages
     */
    private function assertPackageNamesEquals(array $names, array $packages) {
        $this->assertCount(count($names), $packages);

        $packageNames = array_column($packages, 'name');
        sort($names);
        sort($packageNames);
        $this->assertEquals($names, $packageNames);
    }

    /**
     * @param PackageInformation $package
     * @param string[]           $pathParts
     */
    private function assertRelativePathEquals(PackageInformation $package, array $pathParts) {
        $this->assertEquals(implode(DIRECTORY_SEPARATOR, ['.', ...$pathParts]), $package->packageFileLocation->relativePath());
    }
}