<?php

namespace MeesterDev\PackageParser\Parsers;

use MeesterDev\FileWrapper\File;
use MeesterDev\PackageParser\Entities\PackageInformation;
use MeesterDev\PackageParser\Licenses\Loader as LicenseLoader;
use stdClass;

abstract class AbstractParser {
    /** @var string[] */
    protected array    $skipLicenses = [];
    protected stdClass $mainPackageFileContents;
    protected File     $mainPackageFile;
    /** @var PackageInformation[] */
    public array       $skippedPackages;
    /** @var PackageInformation[] */
    public array $failedPackages;

    public function __construct(stdClass $mainPackageFileContents, File $mainPackageFile) {
        $this->mainPackageFileContents = $mainPackageFileContents;
        $this->mainPackageFile         = $mainPackageFile;
        $this->skippedPackages         = [];
        $this->failedPackages          = [];
    }

    /**
     * @param string[] $names
     *
     * @return void
     */
    public function ignoreLicenses(array $names): void {
        $this->skipLicenses = $names;
    }

    /**
     * @param string[] $names
     *
     * @return void
     */
    public function alsoIgnoreLicenses(array $names): void {
        foreach ($names as $name) {
            $this->skipLicenses[] = $name;
        }
    }

    protected static function getLicenseFilePath(File $dependencyDirectory): ?File {
        return LicenseLoader::attemptToLoad($dependencyDirectory);
    }

    protected function shouldSkipPackage(PackageInformation $package): bool {
        return in_array($package->licenseType, $this->skipLicenses);
    }

    protected function licenseLoaded(PackageInformation $result): bool {
        return $result->hasLicense();
    }

    /**
     * @return PackageInformation[]
     */
    abstract function parse(): array;
}