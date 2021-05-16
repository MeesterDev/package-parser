<?php

use MeesterDev\FileWrapper\File;
use MeesterDev\PackageLicenseParser\Entities\PackageInformation;
use MeesterDev\PackageLicenseParser\Parsers\AbstractParser;
use MeesterDev\PackageLicenseParser\Parsers\ParserFactory;
use PHPUnit\Framework\TestCase;

class ComposerLockTest extends TestCase {
    private static function createParser(string $path): AbstractParser {
        return ParserFactory::createForFilePath(
            __DIR__ . DIRECTORY_SEPARATOR . 'packagefiles' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path)
        );
    }

    public function testComposerLockWithoutDependencies(): void {
        $parser = static::createParser('empty-project/composer.lock');

        $packages = $parser->parse();
        $this->assertCount(1, $packages);
        $this->assertEquals('composer', $packages[0]->name);
    }

    public function testPublicPackagesIgnored(): void {
        $parser = static::createParser('public-domain-project/composer.lock');

        $packages = $parser->parse();
        $this->assertCount(1, $packages);
        $this->assertEquals('composer', $packages[0]->name);
    }

    public function testPublicPackagesIncluded(): void {
        $parser = static::createParser('public-domain-project/composer.lock');
        $parser->includePublicDomainLicenses();

        $packages = $parser->parse();

        $this->assertPackageNamesEquals(['composer', 'symfony/polyfill-mbstring'], $packages);

        /** @var PackageInformation[] $packages */
        $packages = array_column($packages, null, 'name');

        $this->assertEquals('MIT', $packages['composer']->licenseType);
        $this->assertEquals('0BSD', $packages['symfony/polyfill-mbstring']->licenseType);
        $this->assertEquals(PackageInformation::SOURCE_COMPOSER, $packages['composer']->source);
        $this->assertEquals(PackageInformation::SOURCE_COMPOSER, $packages['symfony/polyfill-mbstring']->source);
        $this->assertInstanceOf(File::class, $packages['symfony/polyfill-mbstring']->licenseFileLocation);

        $this->assertRelativePathEquals($packages['composer'], ['packagefiles', 'public-domain-project', 'vendor', 'composer']);
        $this->assertRelativePathEquals(
            $packages['symfony/polyfill-mbstring'],
            ['packagefiles', 'public-domain-project', 'vendor', 'symfony', 'polyfill-mbstring']
        );
    }

    public function testLicenseFileParsingCorrect(): void {
        $parser   = static::createParser('standard-project/composer.lock');
        $packages = $parser->parse();

        $this->assertPackageNamesEquals(['composer', 'psr/log', 'symfony/polyfill-mbstring'], $packages);

        /** @var PackageInformation[] $packages */
        $packages = array_column($packages, null, 'name');

        $this->assertEquals('MIT', $packages['composer']->licenseType);
        $this->assertEquals('MIT', $packages['psr/log']->licenseType);
        $this->assertEquals('MIT', $packages['symfony/polyfill-mbstring']->licenseType);

        $this->assertRelativePathEquals($packages['composer'], ['packagefiles', 'standard-project', 'vendor', 'composer']);
        $this->assertRelativePathEquals($packages['psr/log'], ['packagefiles', 'standard-project', 'vendor', 'psr', 'log']);
        $this->assertRelativePathEquals(
            $packages['symfony/polyfill-mbstring'],
            ['packagefiles', 'standard-project', 'vendor', 'symfony', 'polyfill-mbstring']
        );

        $this->assertInstanceOf(File::class, $packages['composer']->licenseFileLocation);
        $this->assertInstanceOf(File::class, $packages['psr/log']->licenseFileLocation);
        $this->assertInstanceOf(File::class, $packages['symfony/polyfill-mbstring']->licenseFileLocation);

        $this->assertEquals('https://getcomposer.org/', $packages['composer']->homepage);
        $this->assertEquals('https://github.com/php-fig/log', $packages['psr/log']->homepage);
        $this->assertEquals('https://symfony.com', $packages['symfony/polyfill-mbstring']->homepage);

        $this->assertEquals('A Dependency Manager for PHP.', $packages['composer']->description);
        $this->assertEquals('Common interface for logging libraries', $packages['psr/log']->description);
        $this->assertEquals('Symfony polyfill for the Mbstring extension', $packages['symfony/polyfill-mbstring']->description);

        $this->assertEquals(PackageInformation::SOURCE_COMPOSER, $packages['composer']->source);
        $this->assertEquals(PackageInformation::SOURCE_COMPOSER, $packages['psr/log']->source);
        $this->assertEquals(PackageInformation::SOURCE_COMPOSER, $packages['symfony/polyfill-mbstring']->source);
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