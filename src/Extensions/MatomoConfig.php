<?php

namespace Mhe\Matomo\Extensions;

use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\Permission;
use SilverStripe\SiteConfig\SiteConfig;

/**
 * Extension for SiteConfig
 *
 * @property boolean MatomoActive
 * @property string MatomoURL
 * @property integer MatomoSiteID
 *
 * @extends Extension<SiteConfig>
 */
class MatomoConfig extends Extension
{
    /**
     * Also track logged in CMS users
     * @config
     */
    private static bool $track_cms_users = false;

    /**
     * automatically add the tracking code to HTML head part without template changes
     * @config
     */
    private static bool $auto_add_tracking_head = true;

    /**
     * Configure the output of the OptOut:
     * - method: either 'script' (default) or 'iframe'
     * @config
     */
    private static array $optout = [
        'method' => 'script'
    ];

    private static array $db = [
        'MatomoActive' => 'Boolean',
        'MatomoURL' => 'Varchar(255)',
        'MatomoSiteID' => 'Int'
    ];

    private static array $defaults = [
        'MatomoActive' => false
    ];

    /**
     * configure the additional CMS fields
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields): void
    {
        $fields->addFieldToTab('Root.Matomo', CheckboxField::create('MatomoActive', $this->owner->fieldLabel('MatomoActive')));
        $fields->addFieldToTab('Root.Matomo', TextField::create('MatomoURL', $this->owner->fieldLabel('MatomoURL')));
        $fields->addFieldToTab('Root.Matomo', NumericField::create('MatomoSiteID', $this->owner->fieldLabel('MatomoSiteID')));
    }

    /**
     * True if Matome is correctly setup and current user should be tracked
     * @return bool
     */
    public function UseMatomo(): bool
    {
        // exclude logged in CMS users from tracking
        $track_cms_users = Config::inst()->get(self::class, 'track_cms_users');
        if (!$track_cms_users && Permission::check('CMS_ACCESS_CMSMain')) {
            return false;
        }
        return ($this->owner->MatomoActive && !empty($this->owner->MatomoURL) && !empty($this->owner->MatomoSiteID));
    }

    /**
     * get the normalized Tracking URL, without trailing or leading slashes
     * @param bool $cleaned
     * @return ?string
     */
    public function MatomoURL(bool $cleaned = true): ?string
    {
        if (!$cleaned) {
            return $this->owner->MatomoURL;
        }
        $url = $this->owner->MatomoURL;
        return preg_replace('!^https?://|^/+|/+$!', '', $url);
    }

    /**
     * get the correct Url for the OptOut iframe
     * @param array $urlargs add URL params, e.g. for style adjustments
     * @return string
     */
    public function MatomoOptOutUrl(array $urlargs = []): string
    {
        $baseurl = $this->MatomoURL(true);
        if (empty($baseurl)) {
            return '';
        }
        $urlargs['module'] = 'CoreAdminHome';
        $urlargs['action'] = 'optOut';
        $urlargs['language'] = i18n::getData()->langFromLocale(i18n::get_locale());
        // ToDo: okay to always use https?
        return 'https://' . $baseurl . '/index.php?' . http_build_query($urlargs);
    }

    /**
     * shortcode handler to output OptOut code
     *
     * @param $arguments
     * @param null $title
     * @param null $parser
     * @param null $tag
     * @param null $extra
     * @return DBHTMLText
     */
    public static function optout_shortcode_handler($arguments, $title = null, $parser = null, $tag = null, $extra = null): DBHTMLText
    {
        $config = Config::inst()->get(self::class, 'optout');
        $siteconfig = SiteConfig::current_site_config();
        $arguments = array_filter(
            $arguments,
            function ($key) {
                return in_array($key, ['method']);
            },
            ARRAY_FILTER_USE_KEY
        );
        if (empty($arguments['method'])) {
            $arguments['method'] = $config['method'];
        }
        $template = 'MatomoOptOutScript';
        if ($arguments['method'] == 'iframe') {
            // ToDo: add arguments – both for iframe (e.g.) and for the generated Url
            $template = 'MatomoOptOutIframe';
            $arguments['OptoutIframeUrl'] = $siteconfig->MatomoOptOutUrl();
        }
        return $siteconfig->customise($arguments)->renderWith($template);
    }
}
