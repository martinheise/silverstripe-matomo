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

	private static $allowed_actions = [];

	private static $auto_add_tracking_head = true;

	public function onAfterInit() {
		// automatically add tracking code to page head, if not configured otherwise
		$auto_add_tracking_head = Config::inst()->get(self::class, 'auto_add_tracking_head');
		if ($auto_add_tracking_head) Requirements::insertHeadTags($this->MatomoTrackingCodeHead());
	}

	public function MatomoTrackingCodeHead() {
		return $this->owner->renderWith('MatomoTrackingCodeHead');
	}

	public function MatomoTrackingCodeBody() {
		return $this->owner->renderWith('MatomoTrackingCodeBody');
	}

}