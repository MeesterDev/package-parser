<?php

namespace MeesterDev\PackageLicenseParser\Exceptions;

use Exception;

class UnknownPackageFileFormatException extends Exception {
    public function __construct(string $filename) {
        parent::__construct("Cannot determine package format for file $filename.");
    }
}