<?php

namespace MeesterDev\Tests;

use MeesterDev\PackageParser\Parsers\AbstractParser;
use MeesterDev\PackageParser\Parsers\ParserFactory;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase {
    protected static function createParser(string $path): AbstractParser {
        $parser = ParserFactory::createForFilePath(
            __DIR__ . DIRECTORY_SEPARATOR . 'packagefiles' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path)
        );

        $parser->ignoreLicenses(['CC0', 'CC0-1.0', '0BSD', 'Unlicense', 'WTFPL']);
        $parser->alsoIgnoreLicenses(['proprietary', 'UNLICENSED']);

        return $parser;
    }
}