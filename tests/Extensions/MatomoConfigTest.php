<?php
/**
 * Copyright (c) 2019, Martin Heise <info@martinheise.de>
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

/**
 * Class MatomoConfigTest
 * @package Mhe\Matomo\Tests\Extensions
 *
 * tests for Matome SiteConfig extension
 */
class MatomoConfigTest extends SapphireTest
{

	/**
	 * Getter method for MatomoURL cleans URL by default
	 */
	public function testMatomoURL() {
		$siteconfig = SiteConfig::create();

		$siteconfig->MatomoURL = 'matomo.url.com';
		$this->assertEquals('matomo.url.com', $siteconfig->MatomoURL());
		$this->assertEquals('matomo.url.com', $siteconfig->MatomoURL(false));

		$siteconfig->MatomoURL = 'http://matomo.url.com/';
		$this->assertEquals('matomo.url.com', $siteconfig->MatomoURL());
		$this->assertEquals('http://matomo.url.com/', $siteconfig->MatomoURL(false));

		$siteconfig->MatomoURL = '//matomo.url.com/';
		$this->assertEquals('matomo.url.com', $siteconfig->MatomoURL());
		$this->assertEquals('//matomo.url.com/', $siteconfig->MatomoURL(false));
	}

	/**
	 * Matomo is only used when all required properties are set
	 */
	public function testUseMatomo() {
		$siteconfig = SiteConfig::create(['MatomoActive' => true, 'MatomoURL' => 'matomo.url.com', 'MatomoSiteID' => 1]);
		$this->assertTrue($siteconfig->UseMatomo());

		$siteconfig = SiteConfig::create(['MatomoActive' => true, 'MatomoURL' => 'matomo.url.com', 'MatomoSiteID' => null]);
		$this->logInWithPermission('CMS_ACCESS_CMSMain');
		$this->assertFalse($siteconfig->UseMatomo());

		$siteconfig = SiteConfig::create(['MatomoActive' => false, 'MatomoURL' => 'matomo.url.com', 'MatomoSiteID' => 1]);
		$this->logInWithPermission('CMS_ACCESS_CMSMain');
		$this->assertFalse($siteconfig->UseMatomo());

		$siteconfig = SiteConfig::create(['MatomoActive' => true, 'MatomoURL' => '', 'MatomoSiteID' => 1]);
		$this->logInWithPermission('CMS_ACCESS_CMSMain');
		$this->assertFalse($siteconfig->UseMatomo());
	}

	/**
	 * Matomo is not used for logged in backend users, except when configured
	 */
	public function testUseMatomoLoggedIn() {
		$siteconfig = SiteConfig::create(['MatomoActive' => true, 'MatomoURL' => 'matomo.url.com', 'MatomoSiteID' => 1]);
		$this->logInWithPermission('CMS_ACCESS_CMSMain');
		$this->assertFalse($siteconfig->UseMatomo());
		$this->logOut();
		$this->assertTrue($siteconfig->UseMatomo());

		Config::modify()->set(MatomoConfig::class, 'track_cms_users', true);
		$this->logInWithPermission('CMS_ACCESS_CMSMain');
		$this->assertTrue($siteconfig->UseMatomo());
		$this->logOut();
		$this->assertTrue($siteconfig->UseMatomo());
	}
}