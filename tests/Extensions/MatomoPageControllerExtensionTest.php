<?php

namespace Mhe\Matomo\Tests\Extensions;

use Mhe\Matomo\Extensions\MatomoConfig;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\FunctionalTest;
use SimpleXMLElement;

/**
 * Class MatomoPageControllerExtensionTest
 * @package Mhe\Matomo\Tests\Extensions
 *
 * tests for Matomo PageController extension
 */
class MatomoPageControllerExtensionTest extends FunctionalTest
{
    protected static $fixture_file = '../fixtures.yml';

    protected function setUp(): void
    {
        parent::setUp();
        $page = $this->objFromFixture('Page', 'home');
        $page->publishRecursive();
        $this->logOut();
    }

    public function findTrackingCode(): ?SimpleXMLElement
    {
        $items = $this->cssParser()->getBySelector('head script');
        foreach ($items as $item) {
            if (strpos($item, 'g.src=u+\'matomo.js')) {
                return $item;
            }
        }
        return null;
    }

    public function assertAnyMatchBySelector($selector, $expectedMatch, $message = null): void
    {
        $items = $this->cssParser()->getBySelector($selector);
        $found = false;
        foreach ($items as $item) {
            if (strpos($item, $expectedMatch)) {
                $found = true;
            }
        }
        $this->assertTrue($found, $message);
    }

    /**
     * Matomo tracking code is output ot page header – if not configured otherwise
     */
    public function testTrackingCodeFound(): void
    {
        $this->get('/');
        $this->assertNotNull($this->findTrackingCode());

        Config::modify()->set(MatomoConfig::class, 'auto_add_tracking_head', false);
        $this->get('/');
        $this->assertEmpty($this->findTrackingCode());
    }

    /**
     * Tracking Settings are included in tracking code
     */
    public function testTrackingCodeContainsProperties(): void
    {
        $this->get('/');
        $code = $this->findTrackingCode();
        $this->assertStringContainsString('u="//matomo.example.com/"', $code->asXML());
        $this->assertStringContainsString("['setSiteId', '1']", $code->asXML());
    }
}
