## Installation

- Install as usual (download and unzip in wp-content/plugins/ folder, then activate)
- Set preferences in WooCommerce -> Settings -> Product Projects
  - optionally activate "create project post type" or choose and existing post type to use as projects (if set, a selection menu will be presented to the user instead of a text input box)
  - optionally allow client to choose amount to pay (if you don't already use another plugin for this feature)
- Create a product
  - check "Project Donation" option, near product type selection
  - check "Virtual" option (recommended but optional)
  - set product price to zero (recommended), or to a higher amount (in this case, the donation will be added to the fixed price, but it would be clearer for the customer to set a minimum donation amount instead)

The product page will display "Project" and "Amount" fields
To create a link for a specific project, add "project" parameter to the URL, like:

https://magiiic.com/donate/project/?project=Project+Donations+plugin

You can also specify an amount in the URL, like:

https://magiiic.com/donate/project/?project=Project+Donations+plugin&amount=5

