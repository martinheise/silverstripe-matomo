<?php
/**
 * Copyright (c) 2017, Martin Heise <info@martinheise.de>
 * All rights reserved.
 *
 * @author     Martin Heise <info@martinheise.de>
 * @created    12.12.19
 */

namespace Mhe\Matomo\Tests\Extensions;


use Mhe\Matomo\Extensions\MatomoConfig;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\SiteConfig\SiteConfig;

class MatomoConfigTest extends SapphireTest
{

	public function testMatomoURL() {
		$siteconfig = SiteConfig::create();

		$siteconfig->MatomoURL = 'matomo.url.com';
		$this->assertEquals('matomo.url.com', $siteconfig->MatomoURL(true));

		//Config::modify()->set(MatomoConfig::class, 'aaaaa', 'asdasdadasd');
	}
}