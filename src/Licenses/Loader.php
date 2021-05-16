<?php

namespace MeesterDev\PackageLicenseParser\Licenses;

use MeesterDev\FileWrapper\File;

class Loader {
    public static function attemptToLoad(File $directory): ?File {
        return static::attemptToLoadFromBaseName($directory, 'LICENSE')
            ?? static::attemptToLoadFromBaseName($directory, 'license')
            ?? static::attemptToLoadFromBaseName($directory, 'License')
            ?? static::attemptToLoadFromBaseName($directory, 'LICENCE')
            ?? static::attemptToLoadFromBaseName($directory, 'licence')
            ?? static::attemptToLoadFromBaseName($directory, 'Licence');
    }

    private static function attemptToLoadFromBaseName(File $directory, string $filename): ?File {
        $file = $directory->go($filename);
        if ($file->isFile()) {
            return $file;
        }

        foreach ($directory->glob("$filename.*") as $file) {
            return $file;
        }

        return null;
    }
}