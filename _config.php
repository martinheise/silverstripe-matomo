<?php
/**
 * Copyright (c) 2019, Martin Heise <info@martinheise.de>
 * All rights reserved.
 *
 * @author     Martin Heise <info@martinheise.de>
 * @created    09.12.2019
 */

use Mhe\Matomo\Extensions\MatomoConfig;
use SilverStripe\View\Parsers\ShortcodeParser;

ShortcodeParser::get('default')
	->register('matomo_optout', [MatomoConfig::class, 'optout_shortcode_handler']);
