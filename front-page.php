<?php
/**
 * The main template file for homepage.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */
get_header('home');
if (have_posts()) {
	while (have_posts()) {
		the_post();
		//echo '<h1 class="pagetitle">' . get_the_title() . '</h1>';
		echo '<div class="padded container">';
		//
		echo '<div id="howworks">';
		echo '<h2>How It Works</h2>';
		echo '<ul class="columns">';
		echo '<li class="w33 padded centertext"><div class="step"><span>1</span></div><h3>Shop</h3><h4>Fill your cart anytime during the open markets days of Saturday thru Friday.</h4></li>';
		echo '<li class="w33 padded centertext"><div class="step"><span>2</span></div><h3>Pay Online</h3><h4>Choose from multiple producers and pay online through Stripe for the best local food while supporting food security.</h4></li>';
		echo '<li class="w33 padded centertext"><div class="step"><span>3</span></div><h3>Pick Up</h3><h4>Your order will be ready for pick up at Fairwinds Florist on Thursday between 2PM and 5PM.</h4></li>';

		echo '</ul>';
		echo '</div>';
		the_content();
		echo '</div>';
		//

		$market_open = get_field('market_open', 'options');
		if ($market_open) {
			$status = '<h3 class="market">Open</h3>';
		} else {
			$status = '<h3 class="market">Closed</h3>';
			echo '<div id="pop-closed" class="pop"><div class="wrap-modal"><div class="modal-pop padded centertext"><h3 class="centertext">Thank you for visiting Blue Hill FarmDrop</h3><div class="padded">';

			$pop_message = get_field('closed_pop_message', 'options');
			echo $pop_message;
			//echo 'We are open from Saturday 10am - Wednesday 10am.<br/>Please pick up your order on Thursday 2-5 pm at Fairwinds Florist – 5 Main St, Blue Hill.<br/>Contact us with any questions: farmdrop@healthyacadia.org';
			echo '</div>';
			echo '<div class="padded">' . do_shortcode('[mc4wp_form id="895"]') . '</div>';
			echo '</div></div></div>';
		}

		echo '<a href="/producer/"><div class="centerall featured" style="background-image:url(' . get_stylesheet_directory_uri() . '/images/slide-market.jpg)"><div class="center"><div class="centered block"><h2>The Market</h2>' . $status . '</div></div></div></a>';

		$args = array('role' => 'shop_manager', 'orderby' => 'user_nicename', 'order' => 'ASC', 'meta_query' => array( array('key' => 'dokan_enable_selling', 'value' => 'yes', 'compare' => '=')));

		$users = get_users($args);
		echo '<div class="padded container"><ul class="columns block">';
		foreach ($users as $user) {
			//inspect($user -> ID);
			$userdata = get_user_meta($user -> ID);
			$description = $userdata['description'][0];
			//inspect($userdata);

			$user_meta = unserialize($userdata['dokan_profile_settings'][0]);
			//inspect(unserialize($userdata['dokan_profile_settings'][0]));
			$banner = $user_meta['banner'];
			if ($banner) {
				$photo = wp_get_attachment_image_src($banner, 'medium')[0];
			} else {
				$photo = get_stylesheet_directory_uri() . '/images/slide-market.jpg';
			}
			//inspect($user_info['social']);

			$store_url = dokan_get_store_url($user -> ID);
			//inspect($store_info);
			$info = get_excerpt_trim($description, 180);

			echo '<li class="w50 padded alignleft"><div class="block infopanel"><h2>&nbsp;' . $userdata['dokan_store_name'][0] . '</h2><div class="item_thumb" style="background-image:url(' . $photo . ');"></div><div>' . $info . '</div><a class="coolbutton" href="' . $store_url . '">View Products</a></li>';
		}
		echo '</ul></div>';
	};
}
?>
<?php
get_footer();
?>
