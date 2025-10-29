### **Custom Global Variables (Community-Maintained Fork) v2.0.1**
This is a community-maintained fork of the original Custom Global Variables plugin, which has not been updated in over two years.
<br/><br/><br/><br/>
✅ What’s new in this fork
This version includes minimal but necessary improvements to ensure the plugin remains functional and maintainable:

- Enhanced admin interface with proportional layout and improved field readability
- Support for inline comments and semantic annotations in variable definitions
- Refactored internal logic for compatibility with future WordPress versions
- Editorial and structural cleanup of the plugin’s documentation

All changes are fully compliant with the original GPLv2 or later license.
<br/><br/><br/><br/>
## **📦 Original Description**
Custom Global Variables lets you define and manage your own variables such as:
- phone numbers,
- social media links,
- or HTML snippets

and access them globally in WordPress and PHP. It avoids database calls for faster performance and cleaner code.
<br/><br/><br/><br/>
## **🔗 Original Plugin**
You can find the original plugin on WordPress.org: https://wordpress.org/plugins/custom-global-variables/ but it's version 1.2.1
<br/><br/><br/><br/>
## 🧱 **Comparison Table**

| Access                                                       | Description        | Output (just example)      |
|--------------------------------------------------------------|--------------------|----------------------------|
| WP Shortcode Legacy | `[cgv days]`                           | Raw Value          | `8`                        |             
| WP Shortcode        | `[cgv_comment days]`                   | Associated Comment | `"Interval days..."`       |
| PHP Array Legacy    | `$GLOBALS['cgv']['variable_name']`     | Raw Value          | `8`                        |
| PHP Array           | `$GLOBALS['cgv_meta']['variable_name']`| Associated Comment | `"Interval days..."`       |
| PHP Object          | `$CGV->variable_name`                  | Semantic value     | `8`                        |
| PHP Object          | `$CGV_META->variable_name`             | Associated Comment | `"Interval days..."`       |

