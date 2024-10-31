=== Post Script Responsive Images ===
Contributors: Peter Stevenson (https://www.p-stevenson.com)
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=55UDRSZMRD4KA
Tags: Responsive, Images, Responsive Images, Srcset, Post Script, Custom
Requires at least: 4.0
Tested up to: 4.7.1
Stable tag: 2.1.0
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

SRCSET responsive images on wordpress for content images.


== Description ==

In short, this plugin modifies "the_content()" and the post thumbnail function by re-rendering the images with use of the SRCSET attribute. Other plugins do this for templated images, but not in the content region itself. This plugin will automatically work with any previously uploaded images as well.


== Installation ==

1. Upload `post-script-responsive` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress


== Changelog ==

= 1.0.0 =
- Initial release

= 1.0.1 =
- Quick Fix of Typo

= 1.0.2 =
- Added Support for Post Thumbnail SRCSET

= 1.0.3 =
- Fixed issue with "|" in alt tags

= 1.0.4 =
- Stopped reszing for ".gif" images to prevent overriding animations

= 2.0.0 =
- Decided to move to using Wordpress generated images instead of the SLIR library to have greater compatibility.

= 2.1.0 =
* Restructured the plugin code to give it a better namespace to avoid any potential conflicts
* Better documented plugin code


== Screenshots ==

1. Banner Image SRCSET Code
2. Content Section Image SRCSET Code