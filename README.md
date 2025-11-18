| ITEM  | DESCRIPTION  |
|---|---|
| Plugin GitHub:  | <https://github.com/tormyvancool/custom-global-variables>  |
| Description:  | Easily create custom variables that can be accessed globally in WordPress and PHP with optional comments per variable.  |
| Version: | 2.0.2  |
| Stable tag:  | 2.0.2  |
| Author:  | Tormy Van Cool (I took over from the original author: akirak)  |
| Author URI:  | <https://www.newtarget.com>  |
| License:  | GPL2 or later  |
| Text Domain:  | custom-global-variables  |
| Requires PHP:  | 7.0  |
| Tested up to:  | 6.8  |
| Donate link:  | <https://www.paypal.com/donate?hosted_button_id=LZ6LLD2B7PGG2>  |

✅ What’s new in this fork
This version includes minimal but necessary improvements to ensure the plugin remains functional and maintainable:
<br/><br/>
- Enhanced admin interface with proportional layout and improved field readability
- Support for inline comments and semantic annotations in variable definitions
- Refactored internal logic for compatibility with future WordPress versions
- Editorial and structural cleanup of the plugin’s documentation
<br/><br/>
All changes are fully compliant with the original GPLv2 or later license.
<br/><br/>
## **📦 Original Description**
Custom Global Variables lets you define and manage your own variables such as:
- phone numbers,
- social media links,
- or HTML snippets
<br/>
and access them globally in WordPress and PHP. It avoids database calls for faster performance and cleaner code.
<br/><br/>
## **🔗 Original Plugin**
You can find the original plugin on WordPress.org: https://wordpress.org/plugins/custom-global-variables/ but it's version 1.2.1
<br>

## **🧱 Comparison Table**

|   | Access   | Description  | Output (just example)   |
|---|---|---|---|
| WP Shortcode Legacy  | `[cgv days]`   | Raw Value  | `8`  |
| WP Shortcode  | `[cgv_comment days]`  | Associated Comment  | `"Interval days..."`  |
| PHP Array Legacy  | `$GLOBALS['cgv']['variable_name']`   | Raw Value  | `8`  |
| PHP Array  | `$GLOBALS['cgv_meta']['variable_name']`  | Associated Comment  | `"Interval days..."`  |
| PHP Object   | `$CGV->variable_name`   | Semantic value   | `8`  |
| PHP Object  | `$CGV_META->variable_name`   | Associated Comment  | `"Interval days..."`  |

<br/><br/>
👀 Check the changelog for the improvements on 2.0.2