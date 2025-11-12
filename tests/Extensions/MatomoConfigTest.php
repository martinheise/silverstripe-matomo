<?php

namespace Mhe\Matomo\Tests\Extensions;

use Page;
use Mhe\Matomo\Extensions\MatomoConfig;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\SiteConfig\SiteConfig;

/**
 * Class MatomoConfigTest
 * @package Mhe\Matomo\Tests\Extensions
 *
 * tests for Matomo SiteConfig extension
 */
class MatomoConfigTest extends SapphireTest
{
    protected static $fixture_file = '../fixtures.yml';

    protected function setUp(): void
    {
        parent::setUp();
        $this->logOut();
    }


    /**
     * Getter method for MatomoURL cleans URL by default
     */
    public function testMatomoURL(): void
    {
        $siteconfig = $this->objFromFixture(SiteConfig::class, 'default');

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
    public function testUseMatomo(): void
    {
        $siteconfig = $this->objFromFixture(SiteConfig::class, 'default');
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
    public function testUseMatomoLoggedIn(): void
    {
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

    /**
     * Create an Opt-Out via the standard iframe method
     * either by global config, or by dedicated shortcode argument
     */
    public function testOptOutShortcodeIframe(): void
    {
        Config::modify()->set(MatomoConfig::class, 'optout', array('method' => 'iframe'));
        $page = $this->objFromFixture(Page::class, 'optout');
        $content = $page->obj('Content')->RAW();
        $this->assertContainsOptOutIframe($content);

        $page = $this->objFromFixture(Page::class, 'optout-iframe');
        $content = $page->obj('Content')->RAW();
        $this->assertContainsOptOutIframe($content);

        Config::modify()->set(MatomoConfig::class, 'optout', array('method' => 'script'));
        $page = $this->objFromFixture(Page::class, 'optout-iframe');
        $content = $page->obj('Content')->RAW();
        $this->assertContainsOptOutIframe($content);
    }

    private function assertContainsOptOutIframe($content): void
    {
        $this->assertStringContainsString('<iframe src="https://matomo.example.com/index.php?module=CoreAdminHome&amp;action=optOut&amp;language=en" class="matomo-optout-iframe"></iframe>', $content);
    }

    /**
     * Create an Opt-Out via a specific script code, without iframe
     * either by global config, or by dedicated shortcode argument
     */
    public function testOptOutShortcodeScript(): void
    {
        Config::modify()->set(MatomoConfig::class, 'optout', array('method' => 'script'));
        $page = $this->objFromFixture(Page::class, 'optout');
        $content = $page->obj('Content')->RAW();
        $this->assertContainsOptOutScript($content);

        $page = $this->objFromFixture(Page::class, 'optout-script');
        $content = $page->obj('Content')->RAW();
        $this->assertContainsOptOutScript($content);

        Config::modify()->set(MatomoConfig::class, 'optout', array('method' => 'iframe'));

        $page = $this->objFromFixture(Page::class, 'optout-script');
        $content = $page->obj('Content')->RAW();
        $this->assertContainsOptOutScript($content);
    }

    private function assertContainsOptOutScript($content): void
    {
        $this->assertStringContainsString('<div class="matomo-optout-form">', $content);
        $this->assertStringContainsString('<script>', $content);
        $this->assertStringContainsString('_paq.push([\'forgetUserOptOut\'])', $content);
    }
}
