<?php
/**
 Template Name:  Regular Page
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit ;
}

get_header('page');
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
