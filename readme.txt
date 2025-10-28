=== Custom Global Variables ===  
Contributors: akirak, abdullahkhalfan, tormy van cool  
Tags: custom global variables, variables, options, settings, shortcodes, shortcode, global variables, global options, global settings, custom variables, custom options, custom settings, custom shortcodes, custom shortcode, php variables  
Author URI: https://www.newtarget.com  
Plugin URI: https://www.newtarget.com/solutions/wordpress-websites  <<< Original one that seems abandonware
Plugin URI: https://github.com/tormyvancool/custom-global-variables <<< Form v2.0 on
Requires at least: 3.0.1  
Tested up to: 6.1.1  
Stable tag: 2.0.1
Requires PHP: 5.6  minimum
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html  

Easily create custom variables that can be accessed globally in WordPress and PHP. Retrieval of information is extremely fast, with no database calls.

== Description ==

Create your own custom variables to manage information on your website such as:

* Phone numbers  
* Addresses  
* Social media links  
* HTML snippets  
* And anything else  

Easily access them globally in WordPress and PHP.

= Why you need it =

Rather than having to change something like an email address across multiple pages, you can do it in one place. Avoid the pitfalls of hard coding information in your WordPress theme that is likely to change.

= Why it's better =

* Your variables are stored and retrieved locally without any calls to the database. That means faster load times for your pages!  
* Variables can be accessed easily in PHP from the global scope.  
* Now includes support for structured comments and semantic annotations in the variable definitions (see changelog 1.2.1).  
* Improved admin interface for better readability and proportional layout of variable fields.  
* Editorial revision and semantic blindatura curated by Tormy Van Cool.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.  
2. Activate the plugin through the 'Plugins' screen in WordPress  
3. Use the `Settings -> Custom Global Variables` screen to define your variables

== Usage ==

Display your variables using the shortcode syntax:

`[cgv variable-name]`
`[cgv_comment variable_name]`

Or using the superglobal in PHP:

`<?php echo $GLOBALS['cgv']['val'] ?>`
`<?php echo $GLOBALS['cgv']['comment'] ?>`
`<?php echo $CGV->variable_name ?>` quicker way for the value
`<?php echo $CGV_META->variable_name ?>` quicker way for the associated comment

== Screenshots ==

1. Settings -> Custom Global Variables
2. Plugins -> All Plugins -> Settings

== Changelog ==

= 2.0.1 =
* Legacy-safe scalar structure: Global values are now stored as direct scalars in $GLOBALS['cgv']['name'],
  restoring full compatibility with legacy code that expects raw values (e.g. echo $GLOBALS['cgv']['days'];).
* Comment separation: Comments are now stored as plain strings in $GLOBALS['cgv_meta']['name'], simplifying access and eliminating unnecessary array wrapping.

* Object layer untouched and fully functional:
  - $CGV->name returns the value
  - $CGV_META->name returns the comment This ensures modern, semantic access for new implementations without
    disrupting legacy usage.

* Admin UI and persistence logic updated: The admin interface and JSON storage now reflect the new bifurcated structure,
  storing values and comments independently while maintaining a unified editing experience.

* Shortcode compatibility preserved:
  - [cgv name] returns the value
  - [cgv_comment name] returns the comment Both shortcodes now map to the updated array structure without
    breaking existing usage.

* Bug fix: Resolved a critical issue where accessing $GLOBALS['cgv']['name'] as a scalar caused a fatal error
  due to legacy array assumptions.
  Now safely returns the expected value.

Security Improvements:
* JSON file protection: Automatically creates a .htaccess file to block direct access to stored variables.
  No user action required.
* File and folder permissions: Sets secure defaults (0640 for files, 0750 for folders) during plugin initialization
  to prevent unauthorized access on shared hosting.

Summary
This update introduces a bifurcated global structure that balances legacy compatibility with modern semantic access.
Existing codebases remain untouched, while new implementations benefit from clean object-based syntax.
A foundational step toward long-term maintainability and clarity.

= 2.0 =
* New data structure: Variables are now stored as associative arrays with val and comment keys, enabling richer metadata per entry.
* Comment support: Each variable can include an optional comment, editable via the admin interface and accessible programmatically.
* Object exposure: Introduced two global objects for semantic access:
  - $CGV → direct access to variable values
  - $CGV_META → direct access to variable comments

* Shortcode enhancements:
  - [cgv variable_name] → returns the value
  - [cgv_comment variable_name] → returns the comment

* Admin UI improvements:
  - Comment field added to variable table
  - Usage guide updated to reflect new syntax

* Legacy syntax updated:
  Previously, variables were accessed via $GLOBALS['cgv']['name'].
  Now, they follow the structure $GLOBALS['cgv']['name']['val'], with optional $GLOBALS['cgv']['name']['comment'].

* Plugin menu integration:
  Added a “Settings” link directly in the plugin list for quick access to the admin panel.

= 1.2.1 =  
* Added support for inline comments and semantic annotations in variable definitions.  
* Enhanced admin UI with proportional layout and improved field readability.  
* Refactored internal logic for better compatibility with future WordPress versions.  
* Codified markup resilience for copy-paste fidelity across platforms.  
* Editorial and semantic revision curated by Tormy Van Cool.

= 1.1.2 =  
* Updated to be able to use markups in value fields.

= 1.1.1 =  
* This is a security and maintenance release and we strongly encourage you to update to it immediately.

= 1.1.0 =  
* This is a security and maintenance release and we strongly encourage you to update to it immediately.

= 1.0.5 =  
* Updated accreditation  
* Updated stylesheet  
* Tested up to WordPress 5.1

= 1.0.4 =  
* Fixed folder path in error message

= 1.0.3 =  
* Changed file path to where variables are stored

= 1.0.2 =  
* Support for older versions of PHP that handle object referencing differently

= 1.0.1 =  

* Support for older versions of PHP (< 5.4) that do not allow the short array syntax
