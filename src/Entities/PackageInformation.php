<?php

namespace MeesterDev\PackageParser\Entities;

use MeesterDev\FileWrapper\File;

class PackageInformation {
    public const SOURCE_NPM              = 'npm';
    public const SOURCE_COMPOSER         = 'composer';
    public const SOURCE_MANUALLY_DEFINED = 'manual';

    public ?string $name                = null;
    public ?string $source              = self::SOURCE_MANUALLY_DEFINED; // npm, composer
    public ?string $licenseType         = null;
    public ?string $description         = null;
    public ?File   $licenseFileLocation = null;
    public ?string $homepage            = null;
    public ?File   $packageFileLocation = null;

    public function hasLicense(): bool {
        return !$this->doesntHaveLicense();
    }

    public function doesntHaveLicense(): bool {
        return $this->licenseFileLocation === null && $this->licenseType === null;
    }

    public function __toString(): string {
        return "$this->name ($this->source" . ($this->source === static::SOURCE_NPM ? ', ' . $this->packageFileLocation->relativePath() : '') . ')';
    }
}
