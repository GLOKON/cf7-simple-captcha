=== Simple CAPTCHA for Contact Form 7 ===
Contributors: dmcassey
Donate link: https://glokon.me/
Tags: cf7 simple captcha, simple captcha cf7, captcha, form, cf7
Requires at least: 5.1
Tested up to: 6.6
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

An addon for CF7 that enables a non-js and non-data based CAPTCHA solution, by using a "nonce" and a hidden field.

== Description ==

An addon for CF7 that enables a non-js and non-data based CAPTCHA solution, by using a "nonce" and a hidden field.
It generates a unique ID every time the form is shown, as well as a hidden CAPTCHA field, that bots normally fill in.
This should prevent most of the automated spam.

This is the long description.  No limit, and you can use Markdown (as well as in the following sections).

== Installation ==

Installing Simple CAPTCHA for CF7 can be done either by searching for "Simple CAPTCHA for CF7" via the "Plugins > Add New" screen in your WordPress dashboard, or by using the following steps:

1. Download the plugin via WordPress.org.
2. Upload the ZIP file through the "Plugins > Add New > Upload" screen in your WordPress dashboard.
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Visit the settings screen and configure, as desired.

== Frequently Asked Questions ==

= Is any data sent to external services? =

No! The great thing about this plugin is that there is no JavaScript downloaded, and no data at all is sent to external services.
All the CAPTCHA processing is done on your instance of WordPress.

= Will this stop all spam bots? =

Probably not, this should stop the majority of them that fall into the captcha honeypot, if you need a more certain
CAPTCHA solution, please try reCAPTCHA or any of the other CAPTCHA providers.

= Will this stop a person spamming me? =

No, this does no behaviour checks on the user, if a person wants to use your contact form to spam you, they will pass this
CAPTCHA, this is to prevent automated systems from spamming you.

== Changelog ==

= 1.0.0 =
* Initial release
