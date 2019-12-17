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
use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

class MatomoPageControllerExtension extends Extension {

	public function onAfterInit() {
		// automatically add tracking code to page head, if not configured otherwise
		$auto_add_tracking_head = Config::inst()->get(MatomoConfig::class, 'auto_add_tracking_head');
		if ($auto_add_tracking_head) Requirements::insertHeadTags($this->MatomoTrackingCodeHead());
	}

	/**
	 * Tracking code for page head – usually inserted automatically, @see MatomoPageControllerExtension::onAfterInit()
	 * @return mixed
	 */
	public function MatomoTrackingCodeHead() {
		return $this->owner->renderWith('MatomoTrackingCodeHead');
	}

	/**
	 * optional Tracking code for page body, e.g. tracking image – empty by default
	 * @return mixed
	 */
	public function MatomoTrackingCodeBody() {
		return $this->owner->renderWith('MatomoTrackingCodeBody');
	}

	/**
	 * OptOut code for usage inside templates
	 * @return \SilverStripe\ORM\FieldType\DBHTMLText
	 */
	public function MatomoOptOut($arguments = []) {
		return MatomoConfig::optout_shortcode_handler($arguments);
	}

}