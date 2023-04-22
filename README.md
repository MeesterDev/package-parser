# Composer/NPM package information parser

Parses composer.json/composer.lock and package.json/package-lock.json files for retrieving information about licenses from those packages.

Packages can be skipped based on their license and will be added to a list of "failed packages" if no license information is available. No validity checks are done on the license itself (e.g. whether it appears on the SPDX License List).

## Example

```php
<?php

use MeesterDev\PackageParser\Parsers\ParserFactory;

$parser = ParserFactory::createForFilePath('composer.json');
$parser->ignoreLicenses(['proprietary', 'UNLICENSED']);
$packages = $parser->parse();

var_dump($packages, $factory->skippedPackages, $factory->failedPackages); // probably a pretty long dump
```