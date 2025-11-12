<?php

use Mhe\Matomo\Extensions\MatomoConfig;
use SilverStripe\View\Parsers\ShortcodeParser;

ShortcodeParser::get('default')
    ->register('matomo_optout', [MatomoConfig::class, 'optout_shortcode_handler']);
