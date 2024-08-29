=== Icon Widget ===
Contributors: seothemes
Tags: icon, widget, fontawesome
Donate link: https://seothemes.com
Requires at least: 5.0
Tested up to: 6.5
Requires PHP: 5.6
Stable tag: trunk
License: GPL-2.0-or-later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Display an icon, title and description with a widget or a shortcode.

== Description ==

Icon Widget creates a new WordPress widget that displays an icon, title and description. Select the size, color and text-alignment with easy to use dropdown options.

= Icon Fonts Included =

* Font Awesome 4 & 5
* Line Awesome
* Ionicons
* Streamline Icons
* ET Line Icons
*More coming soon. We are open to suggestions.*

= Shortcode Usage =

Since 1.0.6 you can now use the `icon_widget` shortcode. Below is a list of available attributes with their default values:

* classes  =>  'icon-widget'
* title    =>  'Icon Widget'
* content  =>  'Add a short description.'
* icon     =>  'fa-star'
* weight   =>  '600'
* size     =>  '2x'
* align    =>  'center'
* color    =>  '#333333'
* bg       =>  ''
* padding  =>  ''
* radius   =>  ''
* heading  =>  'h4'
* break    =>  '`<br>`'

Here is an example of the icon widget shortcode using all available parameters:

`[icon_widget classes="icon-widget" title="Icon Widget" content="Add a short description" icon="fa-star" weight="600" size="2x" align="center" color="#fff" bg="#333" padding="30" radius="30" heading="h4" break="<br>"]`

== Installation ==

1. Go to Plugins > Add New.
2. Type in the name of the WordPress Plugin or descriptive keyword, author, or tag in Search Plugins box or click a tag link below the screen.
3. Find the WordPress Plugin you wish to install.
4. Click Details for more information about the Plugin and instructions you may wish to print or save to help setup the Plugin.
5. Click Install Now to install the WordPress Plugin.
6. The resulting installation screen will list the installation as successful or note any problems during the install.
7. If successful, click Activate Plugin to activate it, or Return to Plugin Installer for further actions.

== Frequently Asked Questions ==

= Can you add an Icon block? =
Yes, we are working on it! In the meantime, the `[icon_widget]` shortcode can be used in the Shortcode Block.

= How do I change the icon font? =
The icon font can be changed from **Settings > Icon Widget**.

= Can you add another icon font? =
Yes, we will add additional icon fonts upon request.

== Changelog ==

= 1.4.0 - 10 April 2024 =

* Fix: Default icon font fallback if no option set
* Fix: Sanitize widget heading
* Fix: Escape all translatable strings
* Fix: Required file checks before loading
* Add: Composer json file

= 1.3.0 - 3 January 2023 =

* Fix: Escape all shortcode attributes

= 1.2.6 =
* Added font weight setting
* Refactor loops in admin widget view

= 1.2.5 =
* Fixed escaping quotes issue in widget content.

= 1.2.4 =
* Fixed default font family in admin widget view.
* Fixed quotes issue in widget.

= 1.2.3 =
* Fixed quotation marks bug.

= 1.2.2 =
* Fixed broken settings link.

= 1.2.1 =
* Added link setting.
* Refactor widget to use shortcode.

= 1.2.0 =
* Added Font Awesome 5
* Refactored entire plugin.

= 1.1.0 =
* Added ET Line icons font.
* Refactor some reusable code.

= 1.0.9 =
* Added background color, padding and border radius options.

= 1.0.8 =
* Fixed deprecated create_function in PHP7.2

= 1.0.7 =
* Added `icon_widget` shortcode.
* Added conditional exit to settings file.

= 1.0.6 =
* Added streamline icons.

= 1.0.5 =
* Fixed scripts and styles loading on all admin screens.

= 1.0.4 =
* Added filters for plugin defaults.

= 1.0.3 =
* Added filterable default settings.

= 1.0.2 =
* Added Ionicons font.

= 1.0.1 =
* Added settings page.
* Fixed multiple color picker instances.
* Removed caching and clean up output.

= 1.0.0 =
* Initial release.
