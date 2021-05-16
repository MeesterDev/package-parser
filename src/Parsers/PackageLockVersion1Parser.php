<?php

namespace MeesterDev\PackageParser\Parsers;

use MeesterDev\FileWrapper\File;
use MeesterDev\PackageParser\Entities\PackageInformation;
use stdClass;

class PackageLockVersion1Parser extends AbstractParser {
    public function parse(): array {
        $packageDirectory = $this->mainPackageFile->go('.');
        $moduleDirectory  = $packageDirectory->go('node_modules');

        $packages = [];

        if (isset($this->mainPackageFileContents->dependencies)) {
            foreach ($this->mainPackageFileContents->dependencies as $name => $dependency) {
                $this->parsePackage($name, $dependency, $moduleDirectory, $packages);
            }
        }

        return $packages;
    }

    private function parsePackage(string $name, stdClass $package, File $vendorDirectory, array &$packages): void {
        $result                      = new PackageInformation();
        $result->name                = $name;
        $result->source              = PackageInformation::SOURCE_NPM;
        $result->packageFileLocation = $vendorDirectory->go($name);

        if (!$result->packageFileLocation->isDir()) {
            // package is not actually installed
            return;
        }

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
                $result->description = $dependencyJson->description ?? null;
                $result->licenseType = $license;
                $result->homepage    = $dependencyJson->homepage ?? null;
            }
        }

        if ($package->dependencies ?? false) {
            foreach ($package->dependencies as $name => $dependency) {
                $this->parsePackage($name, $dependency, $result->packageFileLocation->go('node_modules'), $packages);
            }
        }

        if ($this->shouldSkipPackage($result)) {
            $this->skippedPackages[] = $result;

            return;
        }

        $result->licenseFileLocation = static::getLicenseFilePath($result->packageFileLocation);

        if (!$this->licenseLoaded($result)) {
            $this->failedPackages[] = $result;

            return;
        }

        $packages[] = $result;
    }
}