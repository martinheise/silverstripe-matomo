<?php
/**
 * Copyright (c) 2019, Martin Heise <info@martinheise.de>
 * All rights reserved.
 *
 * @author     Martin Heise <info@martinheise.de>
 * @created    09.12.2019
 */

namespace Mhe\Matomo\Extensions;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Permission;

/**
 * Extension for SiteConfig
 *
 * @property boolean MatomoActive
 * @property string MatomoURL
 * @property integer MatomoSiteID
 */
class MatomoConfig extends DataExtension {

	private static $db = [
		'MatomoActive' => 'Boolean',
		'MatomoURL' => 'Varchar(255)',
		'MatomoSiteID' => 'Int'
	];

	private static $defaults = [
		'MatomoActive' => false
	];

	public static $optout_shortcode_name = 'matomo_optout';

	/**
	 * configure the additional CMS fields
	 * @param FieldList $fields
	 */
	public function updateCMSFields(FieldList $fields) {
		$fields->addFieldToTab('Root.Matomo', CheckboxField::create('MatomoActive', $this->owner->fieldLabel('MatomoActive')));
		$fields->addFieldToTab('Root.Matomo', TextField::create('MatomoURL', $this->owner->fieldLabel('MatomoURL')));
		$fields->addFieldToTab('Root.Matomo', NumericField::create('MatomoSiteID', $this->owner->fieldLabel('MatomoSiteID')));
	}

	public function UseMatomo() {
		// exclude logged in CMS users from tracking
		if (Permission::check('CMS_ACCESS_CMSMain')) return false;
		return ($this->owner->MatomoActive && !empty($this->owner->MatomoURL));
	}

	public function MatomoURL($cleaned = true) {
		if (!$cleaned) return $this->owner->MatomoURL;
		$url = $this->owner->MatomoURL;
		return preg_replace('!^https?://|^/+|/+$!', '', $url);
	}

	/*public static function OptOutShortcodeHandler($arguments,$title = null,$parser = null, $tag = null, $extra = null) {
		$text = _t('MatomoConfig.OPTOUT_Linktext', 'opt out');
		if (isset($arguments['text'])) $text = $arguments['text'];
		$functionname = Config::inst()->forClass('MatomoConfig')->get('optout_function');
		return '<a href="javascript:' . $functionname . '()">' . $text . '</a>';
	}*/
}