<?php

namespace MeesterDev\PackageParser\Parsers;

use MeesterDev\FileWrapper\File;
use MeesterDev\PackageParser\Entities\PackageInformation;
use MeesterDev\PackageParser\Licenses\Licenses;
use MeesterDev\PackageParser\Licenses\Loader as LicenseLoader;
use stdClass;

abstract class AbstractParser {
    protected array    $skipLicenses = Licenses::PUBLIC_DOMAIN_LICENSES;
    protected stdClass $mainPackageFileContents;
    protected File     $mainPackageFile;
    public array       $skippedPackages;
    public array       $failedPackages;

    public function __construct(stdClass $mainPackageFileContents, File $mainPackageFile) {
        $this->mainPackageFileContents = $mainPackageFileContents;
        $this->mainPackageFile         = $mainPackageFile;
        $this->skippedPackages         = [];
        $this->failedPackages          = [];
    }

    public function includePublicDomainLicenses(): self {
        $this->skipLicenses = [];

        return $this;
    }

    protected static function getLicenseFilePath(File $dependencyDirectory): ?File {
        return LicenseLoader::attemptToLoad($dependencyDirectory);
    }

    protected function shouldSkipPackage(PackageInformation $package): bool {
        return in_array($package->licenseType, $this->skipLicenses) || in_array($package->licenseType, Licenses::IGNORED_LICENSES);
    }

    protected function licenseLoaded(PackageInformation $result): bool {
        return $result->hasLicense();
    }

    /**
     * @return PackageInformation[]
     */
    abstract function parse(): array;
}