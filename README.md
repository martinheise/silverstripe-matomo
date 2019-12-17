# SilverStripe Matomo module

Add support for Matomo analytics tool to a Silverstripe installation. 

- The specific setup is done via SiteConfig
- logged in backend users are excluded from tracking (configurable)
- insert opt-out code into template or simply into page content via shortcode 
- opt-out supports inlined form/JavaScript (default) or Mataomo-standard iframe

## Configuration

### Options in CMS Settings (SiteConfig, Tab *Matomo*):

- Activate tracking
- Tracking URL (URL of Matomo installation)
- Site ID
 
### Developer configuration (YAML)
- *MatomoConfig.track_cms_users*: Track CMS users (with permission CMS_ACCESS_CMSMain) (default: *false*)
- *MatomoConfig.auto_add_tracking_head*: add the tracking code to HTML head part – no modifications of the page template(s) needed (default: *true*)
- *MatomoConfig.optout*: configuration of opt-out code, array with sub properties:
    - *method*, either 'script' (default) or 'iframe'
    
### Customizable templates
- MatomoOptOutIframe
- MatomoOptOutScript
- MatomoTrackingCodeBody
- MatomoTrackingCodeHead