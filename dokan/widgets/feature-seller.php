<?php
/**
 * Dokan Featured Seller Widget Content Template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>


    <ul class="dokan-feature-sellers">
        <?php
		$args = array('role' => 'shop_manager', 'orderby' => 'user_nicename', 'order' => 'ASC', 'meta_query' => array( array('key' => 'dokan_enable_selling', 'value' => 'yes', 'compare' => '=')));

		$users = get_users($args);
		//echo '<div class="padded container"><ul class="columns block">';
		foreach ($users as $user) {

			$store_info = dokan_get_store_info($user -> ID);
			$store_url = dokan_get_store_url($user -> ID);

			echo '<li><a href="' . $store_url . '">' . esc_html($store_info['store_name']) . '</a></li>';

		}
        ?>
    </ul>

