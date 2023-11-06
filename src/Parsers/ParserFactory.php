<?php

namespace MeesterDev\PackageParser\Parsers;

use MeesterDev\FileWrapper\Exception\NotReadableException;
use MeesterDev\FileWrapper\File;
use MeesterDev\PackageParser\Exceptions\UnknownPackageFileFormatException;
use JsonException;

abstract class ParserFactory {
    /**
     * @param File $file
     *
     * @return AbstractParser
     *
     * @throws UnknownPackageFileFormatException
     * @throws JsonException
     * @throws NotReadableException
     */
    public static function createForFile(File $file): AbstractParser {
        if (!$file->isFile()) {
            throw new UnknownPackageFileFormatException($file->path);
        }

        $contentObject = $file->readJson();
        if ($contentObject === null) {
            throw new UnknownPackageFileFormatException($file->path);
        }

        if (isset($contentObject->_readme, $contentObject->packages)) {
            return new ComposerLockParser($contentObject, $file);
        }

        if (isset($contentObject->lockfileVersion) && $contentObject->lockfileVersion === 1) {
            return new PackageLockVersion1Parser($contentObject, $file);
        }

        if (isset($contentObject->lockfileVersion, $contentObject->packages)) {
            if ($contentObject->lockfileVersion === 2) {
                return new PackageLockVersion2Parser($contentObject, $file);
            }
            elseif ($contentObject->lockfileVersion === 3) {
                return new PackageLockVersion3Parser($contentObject, $file);
            }
        }

        throw new UnknownPackageFileFormatException($file->path);
    }

    /**
     * @param string $file
     *
     * @return AbstractParser
     *
     * @throws JsonException
     * @throws NotReadableException
     * @throws UnknownPackageFileFormatException
     */
    public static function createForFilePath(string $file): AbstractParser {
        return static::createForFile(new File($file));
    }
}