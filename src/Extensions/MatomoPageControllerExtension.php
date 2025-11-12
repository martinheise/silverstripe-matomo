<?php

namespace Mhe\Matomo\Extensions;

use PageController;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\Requirements;

/**
 * Extension for PageController to enable output of the tracking code
 * @extends Extension<PageController>
 */
class MatomoPageControllerExtension extends Extension
{
    public function onAfterInit(): void
    {
        // automatically add tracking code to page head, if not configured otherwise
        $auto_add_tracking_head = Config::inst()->get(MatomoConfig::class, 'auto_add_tracking_head');
        if ($auto_add_tracking_head) {
            Requirements::insertHeadTags($this->MatomoTrackingCodeHead());
        }
    }

    /**
     * Tracking code for page head – usually inserted automatically, @see MatomoPageControllerExtension::onAfterInit()
     * @return mixed
     */
    public function MatomoTrackingCodeHead(): DBHTMLText
    {
        return $this->owner->renderWith('MatomoTrackingCodeHead');
    }

    /**
     * optional Tracking code for page body, e.g. tracking image – empty by default
     * @return DBHTMLText
     */
    public function MatomoTrackingCodeBody(): DBHTMLText
    {
        return $this->owner->renderWith('MatomoTrackingCodeBody');
    }

    /**
     * OptOut code for usage inside templates
     * @param array $arguments
     * @return DBHTMLText
     */
    public function MatomoOptOut(array $arguments = []): DBHTMLText
    {
        return MatomoConfig::optout_shortcode_handler($arguments);
    }
}
