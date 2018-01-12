<?php
/**
 * Single Product title
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/title.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @author     WooThemes
 * @package    WooCommerce/Templates
 * @version    1.6.4
 */

if (!defined('ABSPATH')) {
	exit ;
	// Exit if accessed directly.
}

// $product_id = get_the_id();
// $product = get_post($product_id);

$author_id = get_the_author_meta('ID');

//$author = get_user_by('id', $product -> post_author);

$store_info = dokan_get_store_info($author_id);

$title = get_the_title();

// inspect($store_info);
// 
// $user_info = get_userdata($author_id);
// $website = $user_info -> data -> user_url;
// 
// $title = $title;
// // . ' - <a href="'.$website.'">' . $store_info['store_name'];

echo "<h1 class=\"product_title entry-title\">$title</h1>";

//the_title('<h1 class="product_title entry-title">', '</h1>');
