<?php
/**
 Template Name:  Home Page
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit ;
}

get_header('home');
if (have_posts()) {
	while (have_posts()) {
		the_post();
		echo '<h1 class="pagetitle">' . get_the_title() . '</h1>';
		the_content();
	};
}
?>
<?php
get_footer();
?>
