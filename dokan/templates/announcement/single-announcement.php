<?php
/**
 * Dokan Announcement Template
 *
 * @since 2.2
 *
 * @package dokan
 */
?>
<div class="dokan-dashboard-wrap">

    <?php

        /**
         *  dokan_dashboard_content_before hook
         *  dokan_dashboard_single_announcement_content_before
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_before' );
        do_action( 'dokan_dashboard_single_announcement_content_before' );
    ?>

    <div class="dokan-dashboard-content dokan-notice-listing">

        <?php

            /**
             *  dokan_before_single_notice hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_before_single_notice' );


            /**
             * dokan_single_announcement_content hook
             *
             * @since 2.4
             */
            do_action( 'dokan_single_announcement_content' );

            /**
             *  dokan_after_listing_notice hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_after_listing_notice' );
        ?>
    </div><!-- #primary .content-area -->

    <?php

        /**
         *  dokan_dashboard_content_after hook
         *  dokan_dashboard_single_announcement_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
        do_action( 'dokan_dashboard_single_announcement_content_after' );
    ?>

</div><!-- .dokan-dashboard-wrap -->