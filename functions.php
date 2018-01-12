<?php

function inspect($object, $script = false) {
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
};

function kill($data) {
	die(var_dump($data));
}

function my_theme_enqueue_styles() {
	$parent_style = 'parent-style';
	// This is 'twentyfifteen-style' for the Twenty Fifteen theme.

	wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');
	wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css?v=' . time());
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

/**
 *  Add custom handling fee to an order
 */

add_action('woocommerce_cart_calculate_fees', 'pt_add_handling_fee');
function pt_add_handling_fee() {
	global $woocommerce;

	if (is_admin() && !defined('DOING_AJAX'))
		return;

	$items = $woocommerce -> cart -> get_cart();
	$fee = 0;
	foreach ($items as $item => $values) {
		//$_product = wc_get_product($values['data'] -> get_id());
		//product image
		//$getProductDetail = wc_get_product($values['product_id']);
		//echo $getProductDetail -> get_image();
		// accepts 2 arguments ( size, attr )

		//echo "<b>" . $_product -> get_title() . '</b>  <br> Quantity: ' . $values['quantity'] . '<br>';
		$price = get_post_meta($values['product_id'], '_price', true);

		$fee = $fee + .1 * $price;

		//echo "  Price: " . $price . "<br>";
		/*Regular Price and Sale Price*/
		//echo "Regular Price: " . get_post_meta($values['product_id'], '_regular_price', true) . "<br>";
		//echo "Sale Price: " . get_post_meta($values['product_id'], '_sale_price', true) . "<br>";
	}

	$title = 'FarmDrop Handling Fee (10%)';
	$woocommerce -> cart -> add_fee($title, $fee, TRUE, 'standard');
}

//add_action('woocommerce_before_calculate_totals', 'add_custom_total_price');

// function add_custom_total_price($cart_object) {
// session_start();
// global $woocommerce;
//
// $custom_price = 100;
//
// if (!empty($_POST['totalValue'])) {
// $theVariable = str_replace(' ', '', $_POST['totalValue']);
//
// if (is_numeric($theVariable)) {
// $custom_price = $theVariable;
// $_SESSION['customDonationValue'] = $custom_price;
// } else {
// $custom_price = 100;
// }
// } else if (!empty($_SESSION['customDonationValue'])) {
// $custom_price = $_SESSION['customDonationValue'];
// } else {
// $custom_price = 50;
// }
//
// foreach ($cart_object->cart_contents as $key => $value) {
// $value['data'] -> price = $custom_price;
// }
// }

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
?>