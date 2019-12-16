<?php
/**
 * Copyright (c) 2019, Martin Heise <info@martinheise.de>
 * All rights reserved.
 *
 * @author     Martin Heise <info@martinheise.de>
 * @created    09.12.2019
 */

namespace Mhe\Matomo\Extensions;

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Permission;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\ArrayData;

/**
 * Extension for SiteConfig
 *
 * @property boolean MatomoActive
 * @property string MatomoURL
 * @property integer MatomoSiteID
 */
class MatomoConfig extends DataExtension {

	/**
	 * @config
	 */
	private static $track_cms_users = false;

	/**
	 * @config
	 */
	private static $auto_add_tracking_head = true;

	/**
	 * @config
	 */
	private static $optout = [
		'method' => 'script'
	];

	private static $db = [
		'MatomoActive' => 'Boolean',
		'MatomoURL' => 'Varchar(255)',
		'MatomoSiteID' => 'Int'
	];

	private static $defaults = [
		'MatomoActive' => false
	];

	/**
	 * configure the additional CMS fields
	 * @param FieldList $fields
	 */
	public function updateCMSFields(FieldList $fields) {
		$fields->addFieldToTab('Root.Matomo', CheckboxField::create('MatomoActive', $this->owner->fieldLabel('MatomoActive')));
		$fields->addFieldToTab('Root.Matomo', TextField::create('MatomoURL', $this->owner->fieldLabel('MatomoURL')));
		$fields->addFieldToTab('Root.Matomo', NumericField::create('MatomoSiteID', $this->owner->fieldLabel('MatomoSiteID')));
	}

	/**
	 * True if Matome is correctly setup and current user should be tracked
	 * @return bool
	 */
	public function UseMatomo() {
		// exclude logged in CMS users from tracking
		$track_cms_users = Config::inst()->get(self::class, 'track_cms_users');
		if (!$track_cms_users && Permission::check('CMS_ACCESS_CMSMain')) return false;
		return ($this->owner->MatomoActive && !empty($this->owner->MatomoURL) && !empty($this->owner->MatomoSiteID));
	}

	/**
	 * get the normalized Tracking URL, without trailing or leading slashes
	 * @param bool $cleaned
	 * @return mixed|string|string[]|null
	 */
	public function MatomoURL($cleaned = true) {
		if (!$cleaned) return $this->owner->MatomoURL;
		$url = $this->owner->MatomoURL;
		return preg_replace('!^https?://|^/+|/+$!', '', $url);
	}

	/**
	 * get the correct Url for the OptOut iframe
	 * @param array $urlargs add URL params, e.g. for style adjustments
	 * @return string
	 */
	public function MatomoOptOutUrl($urlargs = []) {
		$baseurl = $this->MatomoURL(true);
		if (empty($baseurl)) return '';
		$urlargs['module'] = 'CoreAdminHome';
		$urlargs['action'] = 'optOut';
		$urlargs['language'] =i18n::getData()->langFromLocale(i18n::get_locale());
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
	 * @return \SilverStripe\ORM\FieldType\DBHTMLText
	 */
	public static function optout_shortcode_handler($arguments, $title = null, $parser = null, $tag = null, $extra = null) {
		$config = Config::inst()->get(self::class, 'optout');
		$siteconfig = SiteConfig::current_site_config();
		if (empty($arguments['method'])) $arguments['method'] = $config['method'];

		$template = 'MatomoOptOutScript';
		if ($arguments['method'] == 'iframe') {
			// ToDo: add arguments – both for iframe (e.g.) and for the generated Url
			$template = 'MatomoOptOutIframe';
		}
		$data = new ArrayData([
			'OptoutIframeUrl' => $siteconfig->MatomoOptOutUrl()
		]);
		return $data->renderWith($template);
	}
}