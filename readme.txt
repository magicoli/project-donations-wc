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

Allow adding a project field to WooCommerce products, to link purchases to specific projects. Mostly useful with [WooCommerce Name Your Price](https://woocommerce.com/products/name-your-price/) plugin, to allow cutomers to specify an amount and a project to donate.

== Installation ==

* Install as usual (download and unzip in wp-content/plugins/ folder, then activate).
* Optionally install [WooCommerce Name Your Price](https://woocommerce.com/products/name-your-price/) or a similar plugin to allow customer to choose a purchase amount.
* Create a product, check "Project Donation" option.

The product page will display a new "Project" field, that customer must fill to specify the related project.
To create a link for a specific project, add "project" parameter to the URL, like:

https://magiiic.com/donate/project/?project=Project+Donations+plugin

== Frequently Asked Questions ==

= Can I link project to a project page / a specific projects list? =

Not currently, but these features could be added in a future release.

= Can I see sales statistics per project? =

Not currently, but these features could be added in a future release.

= Can cutomer choose the purchase amount? =

Not currently, but it can be achived with another plugin like [WooCommerce Name Your Price](https://woocommerce.com/products/name-your-price/).

== Changelog ==

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
