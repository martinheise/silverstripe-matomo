<?php
/**
 * Copyright (c) 2019, Martin Heise <info@martinheise.de>
 * All rights reserved.
 *
 * @author     Martin Heise <info@martinheise.de>
 * @created    09.12.2019
 */

namespace Mhe\Matomo\Extensions;

use SilverStripe\Core\Extension;

class MatomoPageControllerExtension extends Extension {

	private static $allowed_actions = [];

	public function UseMatomo() {
		return $this->owner->SiteConfig()->UseMatomo();
	}

	public function MatomoURL() {
		return $this->owner->SiteConfig()->MatomoURL();
	}

	public function MatomoSiteID() {
		return $this->owner->SiteConfig()->MatomoSiteID;
	}

	public function MatomoTrackingCodeHead() {
		return $this->owner->renderWith('MatomoTrackingCodeHead');
	}

	public function MatomoTrackingCodeBody() {
		return $this->owner->renderWith('MatomoTrackingCodeBody');
	}

}