<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if (!defined('ABSPATH')) {
	exit ;
	// Exit if accessed directly
}

global $product;

// Ensure visibility
if (empty($product) || !$product -> is_visible()) {
	return;
}
?>
<li <?php post_class(); ?>>
    <?php
	/**
	 * woocommerce_before_shop_loop_item hook.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */

	$author_id = get_the_author_meta('ID');
	$store_info = dokan_get_store_info($author_id);

	$title = get_the_title();

	$vendor = $store_info['store_name'];
	$vendor_url = dokan_get_store_url($author_id);
	/**
	 * woocommerce_before_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */

	echo '<div class="vendor_name"><a href="' . $vendor_url . '">' . $vendor . '</a></div>';
	do_action('woocommerce_before_shop_loop_item');
	do_action('woocommerce_before_shop_loop_item_title');

	/**
	 * woocommerce_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10
	 */
	//do_action( 'woocommerce_shop_loop_item_title' );

	echo '<h2 class="woocommerce-loop-product__title">' . $title . '</h2>';

	//"

	//<h2 class="woocommerce-loop-product__title">Organic Blueberries, 10 lb frozen</h2>

	/**
	 * woocommerce_after_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_template_loop_rating - 5
	 * @hooked woocommerce_template_loop_price - 10
	 */
	do_action('woocommerce_after_shop_loop_item_title');

	/**
	 * woocommerce_after_shop_loop_item hook.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 * @hooked woocommerce_template_loop_add_to_cart - 10
	 */
	do_action('woocommerce_after_shop_loop_item');
	?>
</li>
