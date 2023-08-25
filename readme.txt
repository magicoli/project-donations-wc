=== Project Donations for WooCommerce (dev) ===
Contributors: magicoli69
Donate link: https://magiiic.com/support/Project+Donations+plugin
Tags: woocommerce, projects, product, donation
Requires at least: 4.7
Tested up to: 6.3
Requires PHP: 7.3
Stable tag: 1.5.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Collect donations for projects with Woocommerce.

== Description ==

Collect donations for different projects with a WooCommerce product.

If you are like me, you work on several projects and would like to simply collect donations for them, without bothering creating and configuring a product for each project.

This plugin is mostly intended to be easy to set up. It is mostly usefull if you want to get donations and need or already use WooCommerce platform.

= Features =

* any product as Project Donation
* define project post type (existing or register a new one)
* assign project to specific products
* **add project field** in add to cart block
  - with free type project name (if no defined project post type)
  - or drop down selection list of projects
* replace price with a **flexible Amount field** in add to cart block
* **compatible with subscriptions, variable products**, and probably any other WooCommerce product type
* **compatible with WooCommerce Name Your Price** (although main features are included in Project Donations)
* localization ready
* add **goals** and **counterparts** to project post edit package
  - display goal progress in add to cart block
  - `[goals]` and `[achievements]` shortcodes can be included anywhere


= Roadmap =

* global or per-product settings
* add donation field to cart or checkout page
* permalink like /donate/projectname/amount
* customize notification mails

== Installation ==

* Install as usual (download and unzip in wp-content/plugins/ folder, then activate)
* Set preferences in WooCommerce -> Settings -> Product Projects
  - optionally activate "create project post type" or choose and existing post type to use as projects (if set, a selection menu will be presented to the user instead of a text input box)
  - optionally allow client to choose amount to pay (if you don't already use another plugin for this feature)
* Create a product
  - check "Project Donation" option, near product type selection
  - select project in "Project Donation" tab (optional)
  - check "Virtual" option (recommended but optional)
  - set product price to zero (recommended), or to a higher amount (in this case, the donation will be added to the fixed price, but it would be clearer for the customer to set a minimum donation amount instead)

The product page will display "Project", "Amount" fields and achievement progress bar if linked to a project.
To create use a generic product for donation and get a link for a specific project, add "project" parameter to the URL, like:

https://magiiic.com/donate/project/?project=Project+Donations+plugin

You can also specify an amount in the URL, like:

https://magiiic.com/donate/project/?project=Project+Donations+plugin&amount=5

== Frequently Asked Questions ==

= Can I link project to a project page / a specific projects list? =

Yes, activate "Add project post type" or choose a post type dedicated to projects in WooCommerce Product Donations settings tab.

= Can cutomer choose the purchase amount? =

Yes, activate "Customer defined amount" in WooCommerce Product Donations settings tab.

= Can I see sales statistics per project? =

Yes, with a progress bar. It will appear on products and can be included with a shortcode on any page.

== Changelog ==

= Unreleased (1.5.6-rc-3) =
* added docblock comments
* updated external libraries
* fix in progress orders not counted in achievements total

= 1.5.5 =
* updated readme

= 1.5.4 =
* code normalized

= 1.5.3 =
* fix library update conflict

= 1.5.2 =
* fix project type hardcoded as records
* classes reorganisation

= 1.5.1 =
* added goals shortcode
* fixed get_project_id() returning wrong projects
* use magicoli/wp-package-updater-lib library

= 1.5 =
* new project goals and counterparts
* added achievements graphical progress bar
* added project achievements shortcode

= 1.4.7 =
* fix empty project list if wrong project-id provided in url parameters
* fix project name with html code in add to cart options

= 1.4.6 =
* added Project Donations tab to product data
* select project in product page if product is linked to a project
* show/hide Project Donations tab when Project Donation according to product option checkbox

= 1.4.5 =
* fix a few glitches while publishing

= 1.4.4 =
* fix some more sanitizations

= 1.4.3 =
* fix some sanitizations

= 1.4.2 =
* updated minimum wp version to 4.7

= 1.4.1 =
* fix update library and assets

= 1.4 =
* renamed plugin as project-donations-wc

= 1.3.1 =
* added NL and DE localizations

= 1.3 =
* new choice to create "project" post type or choose an existing post type as projects * product page display a project selection menu  post type if project post type is set, free type text input instead
* added allow custom amount option for project donations
* added WooCommerce settings tab for Project Donations
* added WooCommerce settings tab

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
* added "Project Donation" option to product type section in edit page
* added french localization

= 0.1.0 =
* Initial commit
