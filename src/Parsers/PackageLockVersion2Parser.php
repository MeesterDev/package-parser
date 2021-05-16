<?php

namespace MeesterDev\PackageLicenseParser\Parsers;

use MeesterDev\FileWrapper\File;
use MeesterDev\PackageLicenseParser\Entities\PackageInformation;

class PackageLockVersion2Parser extends AbstractParser {
    public function parse(): array {
        $packageFolders = $this->getPackageList();

        $packages = [];
        foreach ($packageFolders as $packageFolderName) {
            $packageFolder = $this->mainPackageFile->go(str_replace('/', DIRECTORY_SEPARATOR, $packageFolderName));
            if ($packageInformation = $this->parsePackage($packageFolder)) {
                $packages[] = $packageInformation;
            }
        }

        return $packages;
    }

    private function parsePackage(File $packageFolder): ?PackageInformation {
        if (!$packageFolder->isDir()) {
            // package is not actually installed
            return null;
        }

        $result                      = new PackageInformation();
        $result->source              = PackageInformation::SOURCE_NPM;
        $result->packageFileLocation = $packageFolder;

        $packageJsonFile = $result->packageFileLocation->go('package.json');
        if ($packageJsonFile->isFile()) {
            $dependencyJson = $packageJsonFile->readJson();
            if ($dependencyJson) {
                if (isset($dependencyJson->license) && is_object($dependencyJson->license)) {
                    $license = $dependencyJson->license->type ?? null;
                }
                else {
                    $license = $dependencyJson->license ?? null;
                }
                $result->name        = $dependencyJson->name;
                $result->description = $dependencyJson->description ?? null;
                $result->licenseType = $license;
                $result->homepage    = $dependencyJson->homepage ?? null;
            }
        }
        else {
            $result->name = $packageFolder->getFilename();
        }

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

    private function getPackageList(): array {
        return array_filter(array_keys(get_object_vars($this->mainPackageFileContents->packages)));
    }
}