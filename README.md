# SilverStripe Matomo module

Add support for Matomo analytics tool to a Silverstripe installation. 

- The specific setup is done via SiteConfig
- logged in backend users are excluded from tracking (configurable)

## Configuration

### Options in CMS Settings (SiteConfig, Tab *Matomo*):

- Activate tracking
- Tracking URL (Matomo installation)
- Site ID
 
### Developer configuration (YAML):
- MatomoConfig.track_cms_users: Track CMS users (with permission CMS_ACCESS_CMSMain) (default: *false*)
- MatomoPageControllerExtension.auto_add_tracking_head: add the tracking code to HTML head part – no modifications of the page template(s) needed (default: *true*)