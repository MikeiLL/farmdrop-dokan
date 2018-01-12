<?php

include_once ('includes/ajax.php');

$messages = Array();
function inspect($object, $script = false, $to_string = false) {
	if ($to_string == true) {
		return print_r($object, $to_string);
	} else {
		if ($script == true) {
			echo PHP_EOL . '/*';
			echo PHP_EOL;
			print_r($object);
			echo '*/';
		} else {
			echo PHP_EOL . '<!--';
			echo PHP_EOL;
			print_r($object);
			echo '-->';
		}
	}

};

function kill($data) {
	die(var_dump($data));
}

function my_theme_enqueue_styles() {
	$parent_style = 'parent-style';
	// This is 'twentyfifteen-style' for the Twenty Fifteen theme.

	wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');
	wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css?v=' . time());
	wp_enqueue_style('child-style-datepicker', get_stylesheet_directory_uri() . '/datepicker.css');
}

add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles', 20);

add_action('user_register', 'myplugin_registration_save', 10, 1);
function myplugin_registration_save($user_id) {
	$user = get_user_by('id', $user_id);

	if ((in_array('customer', (array)$user -> roles)) || (in_array('administrator', (array)$user -> roles))) {

	} else {
		$user -> add_role('shop_manager');
	}
}

//error_reporting(0);

if (function_exists('acf_add_options_page')) {
	acf_add_options_page(array('page_title' => 'General Settings', 'menu_title' => 'General Settings', 'menu_slug' => 'theme-general-settings', 'capability' => 'edit_posts', 'redirect' => false));
}

function closetags($input) {
	// Close <br> tags
	$buffer = str_replace("<br>", "<br/>", $input);
	// Find all matching open/close HTML tags (using recursion)
	$pattern = "/<([\w]+)([^>]*?) (([\s]*\/>)| (>((([^<]*?|<\!\-\-.*?\-\->)| (?R))*)<\/\\1[\s]*>))/ixsm";
	preg_match_all($pattern, $buffer, $matches, PREG_OFFSET_CAPTURE);
	// Mask matching open/close tag sequences in the buffer
	foreach ($matches[0] as $match) {
		$ofs = $match[1];
		for ($i = 0; $i < strlen($match[0]); $i++, $ofs++)
			$buffer[$ofs] = "#";
	}
	// Remove unclosed tags
	$buffer = preg_replace("/<.*$/", "", $buffer);
	// Put back content of matching open/close tag sequences to the buffer
	foreach ($matches[0] as $match) {
		$ofs = $match[1];
		for ($i = 0; $i < strlen($match[0]) && $ofs < strlen($buffer); $i++, $ofs++)
			$buffer[$ofs] = $match[0][$i];
	}
	return $buffer;
}

function get_excerpt_trim($content, $charlength) {
	$excerpt = strip_tags($content);
	$charlength++;

	if (mb_strlen($excerpt) > $charlength && mb_strlen($excerpt) > 0) {
		$subex = mb_substr($excerpt, 0, $charlength - 5);
		$exwords = explode(' ', $subex);
		$excut = -( mb_strlen($exwords[count($exwords) - 1]));
		if ($excut < 0) {
			return closetags(mb_substr($subex, 0, $excut)) . ' ...';
		} else {
			return closetags($subex) . ' ...';
		}
	} else {
		return closetags($excerpt);
	}
}

//add_filter('woocommerce_enqueue_styles', '__return_false');

// add_filter( 'woocommerce_enqueue_styles', 'jk_dequeue_styles' );
// function jk_dequeue_styles( $enqueue_styles ) {
// unset( $enqueue_styles['woocommerce-general'] );	// Remove the gloss
// //unset( $enqueue_styles['woocommerce-layout'] );		// Remove the layout
// //unset( $enqueue_styles['woocommerce-smallscreen'] );	// Remove the smallscreen optimisation
// return $enqueue_styles;
// }

// Add filter
add_filter('woocommerce_placeholder_img_src', 'growdev_custom_woocommerce_placeholder', 10);
/**
 * Function to return new placeholder image URL.
 */
function growdev_custom_woocommerce_placeholder($image_url) {
	$image_url = 'http://bh.farmdrop.us/wp-content/uploads/2017/12/placeholder_product-1.jpg';
	// change this to the URL to your custom placeholder
	return $image_url;
}

add_filter('loop_shop_per_page', 'new_loop_shop_per_page', 20);

function new_loop_shop_per_page($cols) {
	// $cols contains the current number of products per page based on the value stored on Options -> Reading
	// Return the number of products you wanna show per page.
	$cols = 12;
	return $cols;
}

function add_extra_fields($current_user, $profile_info) {
	//inspect($current_user);
	$user_info = get_userdata($current_user);
	$user_url = $user_info -> data -> user_url;

	$website = isset($user_url) ? $user_url : 'http://';

	echo '<div class="gregcustom dokan-form-group">
		<label class="dokan-w3 dokan-control-label" for="setting_address">Website (URL)</label>
		<div class="dokan-w5">
			<input type="text" class="dokan-form-control input-md valid" name="vendor_website" id="website" value="' . $user_url . '" />
		</div>
	</div>';

}

add_filter('dokan_settings_after_banner', 'add_extra_fields', 10, 2);

function save_extra_fields($store_id, $dokan_settings) {
	if (isset($_POST['vendor_website'])) {
		//$dokan_settings['vendor_website'] = $_POST['vendor_website'];
		wp_update_user(array('ID' => $store_id, 'user_url' => strtolower(trim($_POST['vendor_website']))));
	}
	//update_user_meta($store_id, 'dokan_profile_settings', $dokan_settings);

}

add_action('dokan_store_profile_saved', 'save_extra_fields', 10, 2);

// Add Fee Product

/**
 *  Add custom handling fee to an order
 */

// add_action('woocommerce_cart_calculate_fees', 'pt_add_handling_fee');
// function pt_add_handling_fee() {
// global $woocommerce;
//
// if (is_admin() && !defined('DOING_AJAX'))
// return;
//
// $items = $woocommerce -> cart -> get_cart();
// $fee = 0;
// foreach ($items as $item => $values) {
// $price = get_post_meta($values['product_id'], '_price', true);
// $fee = $fee + .1 * $price;
// }
//
// $title = 'FarmDrop Handling Fee (10%)';
// $woocommerce -> cart -> add_fee($title, $fee, TRUE, 'standard');
// }

add_action('woocommerce_before_calculate_totals', 'add_custom_total_price', 99);

function add_custom_total_price($cart_object) {
	// if (is_admin() && !defined('DOING_AJAX'))
	// return;
	global $messages;

	$farmdrop_fee_product = get_field('farmdrop_fee_product', 'options');
	$farmdrop_fee_product_id = $farmdrop_fee_product -> ID;

	global $woocommerce;
	$items = $woocommerce -> cart -> get_cart();

	if (!is_checkout()) {
		$cart_id = $cart_object -> generate_cart_id($farmdrop_fee_product_id);
		$cart_object -> remove_cart_item($cart_id);
		return;
	};

	$fee_in_cart = false;
	$cart_total = 0;
	foreach ($items as $cart_item) {
		//$messages[] = inspect($cart_item['data'], false, true);
		$id = $cart_item['data'] -> get_id();

		$product = wc_get_product($id);
		$price = get_post_meta($id, '_price', true);
		$quantity = (int)$cart_item['quantity'];

		if ($id == $farmdrop_fee_product_id) {
			$fee_in_cart = true;
		} else {
			$cart_total += $price * $quantity;
		}
	}

	//$cart_total = $order->get_total();

	//$cart_total = floatval(preg_replace('#[^\d.]#', '', $woocommerce -> cart -> get_cart_total()));

	//$messages[] = $cart_total;
	//inspect($cart_total, false, true);

	if (!$fee_in_cart) {
		$cart_object -> add_to_cart($farmdrop_fee_product_id, $quantity = 1);
	}

	foreach ($items as $cart_item) {
		$id = $cart_item['data'] -> get_id();
		if ($id == $farmdrop_fee_product_id) {
			$cart_item['data'] -> set_price($cart_total * .1);
		}
	}
}

add_action('woocommerce_before_checkout_form', 'wnd_checkout_message', 10);

function wnd_checkout_message() {
	echo '<div class="wnd-checkout-message"><h3>Returning FarmDrop customer? Please click <strong><a href="/my-account">here</a></strong> to log in.</h3></div>';
	echo '<div class="distanttop"><ul>
	<li>We use Stripe as a Payment method, and if you are making a first time order, you might hear from your financial institution to make sure that you are in fact the one making this payment.</li><li>FarmDrop is a direct-to-consumer marketplace and all charges on your bank account or credit card statement will reflect the individual payments to the persons or business you are purchasing from</li><li>Healthy Acadia charges a 10% FarmDrop Handling Fee on all purchases, which gets added at checkout, and goes towards supporting our food security programs.</li></ul></div>';
}

//add_action('woocommerce_new_order', 'action_woocommerce_new_order', 1, 1);

add_action('woocommerce_thankyou', 'action_woocommerce_new_order');

function action_woocommerce_new_order($order_id) {
	//global $woocommerce;

	$order = wc_get_order($order_id);

	//$order = new WC_Order($order_id);

	$order_data = $order -> get_data();
	$order_parent_id = $order_data['parent_id'];

	//$messages[] = inspect($order, false, true);

	if ($order_parent_id == 0) {
		$email_customer = $order -> billing_email;
		$name = $order -> billing_first_name;
		//$order -> billing_last_name . ' ' .
		$order_date = date_i18n(woocommerce_date_format(), strtotime($order -> order_date));
		$order_number = $order -> get_order_number();
		$subject = "Your " . get_bloginfo('description') . " order receipt from $order_date";
		$products_html = '<table border="0" cellspacing="0" cellpadding="5" style="width:100%;border:none;"><thead><tr><th scope="col" style="text-align:left">Product</th><th scope="col" style="text-align:left">Producer</th><th scope="col" style="text-align:right">Quantity</th><th scope="col" style="text-align:right">Price</th></tr></thead><tbody>';

		//$sub_orders = get_children(array('author' => $seller_id, 'post_parent' => $order_id, 'post_type' => 'shop_order', 'post_status' => array('wc-pending', 'wc-completed', 'wc-processing', 'wc-on-hold')));
		foreach ($order -> get_items() as $item_id => $item_data) {
			$product = $item_data -> get_product();
			$product_name = $product -> get_name();
			$store_info = dokan_get_store_info($product -> post -> post_author);
			$vendor = $store_info['store_name'];
			$item_quantity = $item_data -> get_quantity();
			$item_total = $item_data -> get_total();
			$products_html = $products_html . '<tr><td style="border-top:solid 1px #ddd;">' . $product_name . '</td><td style="border-top:solid 1px #ddd;">' . $vendor . '</td><td style="text-align:right;border-top:solid 1px #ddd;">' . $item_quantity . '</td><td style="text-align:right;border-top:solid 1px #ddd;"><span><span>$</span>' . number_format($item_total, 2) . '</span></td></tr>';
		}

		$products_html = $products_html . '</tbody><tfoot><tr><th colspan="3" scope="row" style="border-top:solid 1px #ddd;">Subtotal:</th><td style="text-align:right;border-top:solid 1px #ddd;">$' . number_format($order -> get_subtotal(), 2) . '</td></tr><tr><th colspan="2" scope="row"></th><td></td></tr><tr><th colspan="3" scope="row" style="border-top:solid 1px #ddd;">Total:</th><td style="text-align:right;border-top:solid 1px #ddd;">$' . number_format($order -> get_total(), 2) . '</td></tr></tfoot></table>';

		$variables = array('client' => $name, 'order_number' => $order_number, 'order_date' => $order_date, 'order_table' => $products_html);

		$template = get_field('customer_email', 'options');

		foreach ($variables as $key => $value) {
			$template = str_replace('{{' . $key . '}}', $value, $template);
		}

		wp_mail($email_customer, $subject, $template, array('Content-Type: text/html; charset=UTF-8'));
	}
};

function action_woocommerce_order_status_completed($order_id) {
	//error_log("Order complete for order $order_id", 0);

	//global $woocommerce;

	$order = wc_get_order($order_id);

	//$order = new WC_Order($order_id);

	$order_data = $order -> get_data();
	$order_parent_id = $order_data['parent_id'];

	//$messages[] = inspect($order, false, true);

	if ($order_parent_id == 0) {
		$email_customer = $order -> billing_email;
		$name = $order -> billing_first_name;
		//$order -> billing_last_name . ' ' .
		$order_date = date_i18n(woocommerce_date_format(), strtotime($order -> order_date));
		$order_number = $order -> get_order_number();
		$subject = "Your " . get_bloginfo('description') . " order is complete ($order_date)";
		$products_html = '<table border="0" cellspacing="0" cellpadding="5" style="width:100%;border:none;"><thead><tr><th scope="col" style="text-align:left">Product</th><th scope="col" style="text-align:left">Producer</th><th scope="col" style="text-align:right">Quantity</th><th scope="col" style="text-align:right">Price</th></tr></thead><tbody>';

		//$sub_orders = get_children(array('author' => $seller_id, 'post_parent' => $order_id, 'post_type' => 'shop_order', 'post_status' => array('wc-pending', 'wc-completed', 'wc-processing', 'wc-on-hold')));
		foreach ($order -> get_items() as $item_id => $item_data) {
			$product = $item_data -> get_product();
			$product_name = $product -> get_name();
			$store_info = dokan_get_store_info($product -> post -> post_author);
			$vendor = $store_info['store_name'];
			$item_quantity = $item_data -> get_quantity();
			$item_total = $item_data -> get_total();
			$products_html = $products_html . '<tr><td style="border-top:solid 1px #ddd;">' . $product_name . '</td><td style="border-top:solid 1px #ddd;">' . $vendor . '</td><td style="text-align:right;border-top:solid 1px #ddd;">' . $item_quantity . '</td><td style="text-align:right;border-top:solid 1px #ddd;"><span><span>$</span>' . number_format($item_total, 2) . '</span></td></tr>';
		}

		$products_html = $products_html . '</tbody><tfoot><tr><th colspan="3" scope="row" style="border-top:solid 1px #ddd;">Subtotal:</th><td style="text-align:right;border-top:solid 1px #ddd;">$' . number_format($order -> get_subtotal(), 2) . '</td></tr><tr><th colspan="2" scope="row"></th><td></td></tr><tr><th colspan="3" scope="row" style="border-top:solid 1px #ddd;">Total:</th><td style="text-align:right;border-top:solid 1px #ddd;">$' . number_format($order -> get_total(), 2) . '</td></tr></tfoot></table>';

		$variables = array('client' => $name, 'order_number' => $order_number, 'order_date' => $order_date, 'order_table' => $products_html);

		$template = get_field('order_complete_email', 'options');

		foreach ($variables as $key => $value) {
			$template = str_replace('{{' . $key . '}}', $value, $template);
		}

		wp_mail($email_customer, $subject, $template, array('Content-Type: text/html; charset=UTF-8'));
	}
}

add_action('woocommerce_order_status_completed', 'action_woocommerce_order_status_completed', 10, 1);

//include_once ('includes/webfix-email.php');
// foreach ($order_items as $item_id => $product) {
//
// //$seller = get_user_by('id', $product -> post -> post_author);
//
// $store_info = dokan_get_store_info($product -> post -> post_author);
//
// $vendor = $store_info['store_name'];
// $vendor_url = dokan_get_store_url($author_id);
// $title = get_the_title($product -> post -> ID);
// $quantity = 1;
// $price = 100;
// $products_html = $products_html . '<tbody><tr><td>' . $title . '</td><td>' . $quantity . '</td><td><span><span>$</span>' . $price . '</span></td></tr></tbody>';
// }

add_filter('dokan_query_var_filter', 'dokan_load_document_menu');
function dokan_load_document_menu($query_vars) {
	$query_vars['weekly'] = 'weekly';
	return $query_vars;
}

add_filter('dokan_get_dashboard_nav', 'dokan_add_help_menu');
function dokan_add_help_menu($urls) {
	$urls['weekly'] = array('title' => __('Weekly Orders', 'dokan'), 'icon' => '<i class="fa fa-user"></i>', 'url' => dokan_get_navigation_url('weekly'), 'pos' => 41);
	return $urls;
}

add_action('dokan_load_custom_template', 'dokan_load_template');
function dokan_load_template($query_vars) {
	if (isset($query_vars['weekly'])) {
		require_once dirname(__FILE__) . '/weekly.php';
		exit();
	}
}

function format_phone_number($phone, $international = false) {
	$format = "/(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?$/";

	$alt_format = '/^(\+\s*)?((0{0,2}1{1,3}[^\d]+)?\(?\s*([2-9][0-9]{2})\s*[^\d]?\s*([2-9][0-9]{2})\s*[^\d]?\s*([\d]{4})){1}(\s*([[:alpha:]#][^\d]*\d.*))?$/';
	// Trim & Clean extension
	$phone = trim($phone);
	$phone = preg_replace('/\s+(#|x|ext(ension)?)\.?:?\s*(\d+)/', ' ext \3', $phone);
	if (preg_match($alt_format, $phone, $matches)) {
		return '(' . $matches[4] . ') ' . $matches[5] . '-' . $matches[6] . (!empty($matches[8]) ? ' ' . $matches[8] : '');
	} elseif (preg_match($format, $phone, $matches)) {
		// format
		$phone = preg_replace($format, "($2) $3-$4", $phone);
		// Remove likely has a preceding dash
		$phone = ltrim($phone, '-');
		// Remove empty area codes
		if (false !== strpos(trim($phone), '()', 0)) {
			$phone = ltrim(trim($phone), '()');
		}
		// Trim and remove double spaces created
		return preg_replace('/\\s+/', ' ', trim($phone));
	}
	return $phone;
}
?>