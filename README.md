# farmdrop-dokan
A wordpress theme based on the dokan theme for woocommerce multi-vendor store.

Important Configurations

## Advanced Custom Fields

A Single Field group, General Settings, which is called by the `acf_add_options_page` function.
See [ACF docs](https://www.advancedcustomfields.com/resources/options-page/). We have four fields
which are used for email templates and the, checkbox and pop-up for when store is closed.
 * * TODO: putting store open on an automatic schedule

## WooCommerce Settings

 * Checkout Settings:
 * *  * * Force Secure Checkout ENABLED
 * * Enable Guest Checkout: DISABLED

 * *  * Email
 * * Maybe 	New order 	text/html 	farmdrop@healthyacadia.org
 * * Yes	Cancelled order 	text/html 	farmdrop@healthyacadia.org
 * * Yes	Failed order 	text/html 	farmdrop@healthyacadia.org
 * * Yes	Order on-hold 	text/html 	Customer
 * * -	Processing order 	text/html 	Customer
 * * -	Completed order 	text/html 	Customer
 * * Yes	Refunded order 	text/html 	Customer
 * * Manual	Customer invoice / Order details 	text/html 	Customer
 * * Yes	Customer note 	text/html 	Customer
 * * Yes	Reset password 	text/html 	Customer
 * * Yes	New account 	text/html 	Customer
 * * Yes	Dokan New Product 	text/html 	farmdrop@healthyacadia.org, mike@mzoo.org
 * * Yes	Dokan New Pending Product 	text/html 	farmdrop@healthyacadia.org, mike@mzoo.org
 * * -	Dokan Pending Product Published 	text/html 	vendor@ofthe.product
 * * Yes	Dokan New Seller Registered 	text/html 	farmdrop@healthyacadia.org, mike@mzoo.org
 * * Yes	Dokan New Withdrawal Request 	text/html 	farmdrop@healthyacadia.org, mike@mzoo.org
 * * Yes	Dokan Withdraw Approved 	text/html 	vendor@ofthe.product
 * * Yes	Dokan Withdraw cancelled 	text/html 	vendor@ofthe.product
 * * Yes	Dokan Contact Vendor 	text/html 	vendor@ofthe.product
 * * Yes	Dokan Announcement 	text/html 	selecetedvendors@the.announcement
 * * Yes	Dokan Updated Pending Product 	text/html 	farmdrop@healthyacadia.org, mike@mzoo.org
 * * Yes	Dokan New Refund Request 	text/html 	farmdrop@healthyacadia.org, mike@mzoo.org
 * * Yes	Dokan Refund Processed 	text/html 	vendor@ofthe.product
 
 * Disable “terms and conditions” checkbox in WooCommerce > Settings > Checkout and insure that there no page selected for “Terms and Conditions”.
 
## Dokan

 * Store URL: producer
 * Extra Fee Recipient: Admin setting never seemed to work
 * Commission Type: Percentage, although the percentage is set to 0.
 * Admin Commission: 0
 * Withdraw Methods: Dokan/Stripe
 * Order Status for Withdraw: Completed
 * TODO: Consider adding Social Login 
 
## Stripe Gotchas

 * When working with Stripe in Test Mode
 * * They are not expecting you to go back to testing once have gone live
 * When deleting orders and vendor accounts you may have to delete Transients as well to avoid 
 Stripe errors. Stripe order numbers and WooCommerce order numbers should basically match. 
 * [WP DB Cleaner](https://wordpress.org/plugins/wp-db-cleaner) and [Transient Cleaner](https://wordpress.org/plugins/artiss-transient-cleaner) are useful plugins for this

## Dokan Templates Directory

 * Within this theme directory, we have over-written some of the core Dokan Templates
 
TODO: Implement feature to automatically delete any PDFs older than say, six months, probably in the weekly.php file.

## The Hidden Virtual Product

 * Create a (Simple) Virtual Product in WooCommerce
 * * Price needs to be set, but amount not relevant, as 10% is hard-coded in functions.php
 * * This product has to belong to the Vendor who receives fees–see Select box below main product MetaBox
 * * In the Publishing Status MetaBox, Catalog Visibility: Hidden
 * * TODO: programaticaly create this product
 * There is Code in Functions.php which adds to shopping cart
 * TODO: Allow Admin to select which product 