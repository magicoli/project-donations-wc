## Installation

* Install as usual (download and unzip in wp-content/plugins/ folder, then activate)
* Set preferences in WooCommerce -> Settings -> Product Projects
  - optionally activate "create project post type" or choose and existing post type to use as projects (if set, a selection menu will be presented to the user instead of a text input box)
  - optionally allow client to choose amount to pay (if you don't already use another plugin for this feature)
* Create a product, check "Project Donation" option
* Set product price to zero, or to a minimum amount
* You would probably also activate "Virtual" option

The product page will display "Project" and "Amount" fields
To create a link for a specific project, add "project" parameter to the URL, like:

https://magiiic.com/donate/project/?project=Project+Donations+plugin

You can also specify an amount in the URL, like:

https://magiiic.com/donate/project/?project=Project+Donations+plugin&amount=5

