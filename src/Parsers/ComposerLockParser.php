<?php

namespace MeesterDev\PackageParser\Parsers;

use MeesterDev\FileWrapper\File;
use MeesterDev\PackageParser\Entities\PackageInformation;
use stdClass;

class ComposerLockParser extends AbstractParser {
    public function parse(): array {
        $jsonContent = $this->mainPackageFile->go('composer.json')->readJson();
        $vendorPath  = $this->mainPackageFile->go($jsonContent->config->{'vendor-dir'} ?? 'vendor');

        $result = array_filter(
            array_map(
                function (stdClass $package) use ($vendorPath) {
                    return $this->parsePackage($package, $vendorPath);
                },
                $this->mainPackageFileContents->packages
            )
        );

        // dirty hack to include the actual license of composer
        $composer = $vendorPath->go('composer');
        if ($composer->isDir()) {
            $package                      = new PackageInformation();
            $package->name                = 'composer';
            $package->description         = 'A Dependency Manager for PHP.';
            $package->homepage            = 'https://getcomposer.org/';
            $package->source              = PackageInformation::SOURCE_COMPOSER;
            $package->licenseType         = 'MIT';
            $package->packageFileLocation = $vendorPath->go('composer');

            $packageOrNull = $this->handlePackageAddition($package);
            if ($packageOrNull !== null) {
                $result[] = $packageOrNull;
            }
        }

        return $result;
    }

    private function parsePackage(stdClass $package, File $vendorDirectory): ?PackageInformation {
        if ($package->type === 'metapackage') {
            return null;
        }

        $result                      = new PackageInformation();
        $result->name                = $package->name;
        $result->description         = $package->description;
        $result->homepage            = $package->homepage ?? null;
        $result->source              = PackageInformation::SOURCE_COMPOSER;
        $result->packageFileLocation = $vendorDirectory->go(str_replace('/', DIRECTORY_SEPARATOR, $result->name));
        $result->licenseType         = $package->license[0] ?? null;

        return $this->handlePackageAddition($result);
    }

    /**
     * @param PackageInformation $result
     *
     * @return PackageInformation|null
     */
    private function handlePackageAddition(PackageInformation $result): ?PackageInformation {
        if ($this->shouldSkipPackage($result)) {
            $this->skippedPackages[] = $result;

            return null;
        }

        $result->licenseFileLocation = static::getLicenseFilePath($result->packageFileLocation);

        if (!$this->licenseLoaded($result)) {
            $this->failedPackages[] = $result;

            return null;
        }

        return $result;
    }
}