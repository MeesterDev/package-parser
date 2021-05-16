<?php

namespace MeesterDev\PackageLicenseParser\Licenses;

/**
 * Note that we only have constants for supported licenses. Unsupported licenses will still work, as long as the package has a LICENSE file.
 */
abstract class Licenses {
    public const APACHE_2_0                       = 'Apache-2.0';
    public const BSD_2_CLAUSE                     = 'BSD-2-Clause';
    public const CREATIVE_COMMONS_BY_3_0_UNPORTED = 'CC-BY-3.0';
    public const GPL_2_0_ONLY                     = 'GPL-2.0-only';
    public const GPL_3_0_ONLY                     = 'GPL-3.0-only';
    public const ISC                              = 'ISC';
    public const MIT                              = 'MIT';

    public const CREATIVE_COMMONS_ZERO     = 'CC0';
    public const CREATIVE_COMMONS_ZERO_1_0 = 'CC0-1.0';
    public const BSD_ZERO_CLAUSE           = '0BSD';
    public const UNLICENSE                 = 'Unlicense';
    public const WHAT_THE_F_YOU_WANT       = 'WTFPL';

    public const LICENSES_WITH_TEXT_AVAILABLE = [
        self::APACHE_2_0,
        self::BSD_2_CLAUSE,
        self::CREATIVE_COMMONS_BY_3_0_UNPORTED,
        self::GPL_2_0_ONLY,
        self::GPL_3_0_ONLY,
        self::ISC,
        self::MIT,
    ];

    public const IGNORED_LICENSES       = [
        'proprietary',
        'UNLICENSED',
    ];
    public const PUBLIC_DOMAIN_LICENSES = [
        self::CREATIVE_COMMONS_ZERO,
        self::CREATIVE_COMMONS_ZERO_1_0,
        self::BSD_ZERO_CLAUSE,
        self::UNLICENSE,
        self::WHAT_THE_F_YOU_WANT,
    ];
}