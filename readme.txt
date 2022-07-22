=== Project Products for WooCommerce ===
Contributors: magicoli69
Donate link: https://magiiic.com/support/Project+Products+plugin
Tags: woocommerce, projects, product, donation
Requires at least: 4.5
Tested up to: 6.0.1
Requires PHP: 5.6
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add project field to products, allow cutomers to link their purchase to a project

== Description ==

Allow to set WooCommerce product as "Project Product"

* add a "Project" field
* allow creation of a "Project" post type
* allow free type project name or select from a list (from projects or another post type)
* allow flexible price with "Amount" field (if the product has a price higher than zero, the donation amount will be added to the normal product price)
* compatible with subscriptions and variations

Mostly useful for donations, to allow cutomers to support different project.

== Installation ==

* Install as usual (download and unzip in wp-content/plugins/ folder, then activate)
* Set preferences in WooCommerce -> Settings -> Product Projects
  - optionally activate "create project post type" or choose and existing post type to use as projects (if set, a selection menu will be presented to the user instead of a text input box)
  - optionally allow client to choose amount to pay (if you don't already use another plugin for this feature)
* Create a product, check "Project Product" option
* Set product price to zero, or to a minimum amount
* You would probably also activate "Virtual" option

The product page will display "Project" and "Amount" fields
To create a link for a specific project, add "project" parameter to the URL, like:

https://magiiic.com/donate/project/?project=Project+Donations+plugin

You can also specify an amount in the URL, like:

https://magiiic.com/donate/project/?project=Project+Donations+plugin&amount=5

== Frequently Asked Questions ==

= Can I link project to a project page / a specific projects list? =

Not currently, but these features could be added in a future release.

= Can I see sales statistics per project? =

Not currently, but these features could be added in a future release.

= Can cutomer choose the purchase amount? =

Yes

== Changelog ==

= 1.2.1 =
* updated readme with 1.2 changes

= 1.2 =
* added flexible amount field
* fix fields position for variable products

= 1.1.1 =
* updated tags

= 1.1 =
* added update library

= 1.0.1 =
* updated Plugin URI

= 1.0 =
* new Project name field on products, autofill if URL parameter project is provided, project name added in cart item name
* added "Project Product" option to product type section in edit page
* added french localization

= 0.1.0 =
* Initial commit
