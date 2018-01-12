<?php
/**
 * The Template for displaying all single posts.
 *
 * @package _bootstraps
 * @package _bootstraps - 2013 1.0
 */
get_header();
?>
<div id="primary" class="content-area">
    <div id="content" class="site-content" role="main">
        <?php while (have_posts()) : the_post();
        ?>
        <?php get_template_part('content', 'page'); ?>
        <?php
		// If comments are open or we have at least one comment, load up the comment template

		$args = array('role' => 'shop_manager', 'orderby' => 'user_nicename', 'order' => 'ASC', 'meta_query' => array( array('key' => 'dokan_enable_selling', 'value' => 'yes', 'compare' => '=')));
		$users = get_users($args);
		echo '<div class="padded"><ul class="columns block">';
		foreach ($users as $user) {
			//inspect($user -> ID);
			$userdata = get_user_meta($user -> ID);
			$description = $userdata['description'][0];
			inspect($userdata);

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
        ?>
        <?php endwhile; // end of the loop. ?>
    </div>
    <!-- #content .site-content -->
</div>
<!-- #primary .content-area -->
<?php //get_sidebar(); ?>
<?php get_footer(); ?>