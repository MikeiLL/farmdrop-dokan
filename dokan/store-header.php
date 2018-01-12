<?php
$store_user =    dokan() -> vendor -> get(get_query_var('author'));
$store_info = $store_user -> get_shop_info();
$social_info = $store_user -> get_social_profiles();
$store_tabs = dokan_get_store_tabs($store_user -> get_id());
$social_fields = dokan_get_social_profile_fields();

$dokan_appearance = get_option('dokan_appearance');
$profile_layout = empty($dokan_appearance['store_header_template']) ? 'default' : $dokan_appearance['store_header_template'];
$store_address = dokan_get_seller_short_address($store_user -> get_id(), false);

$general_settings = get_option('dokan_general', []);
$banner_width = !empty($general_settings['store_banner_width']) ? $general_settings['store_banner_width'] : 625;

if (('default' === $profile_layout) || ('layout2' === $profile_layout)) {
	$profile_img_class = 'profile-img-circle';
} else {
	$profile_img_class = 'profile-img-square';
}

if ('layout3' === $profile_layout) {
	unset($store_info['banner']);

	$no_banner_class = ' profile-frame-no-banner';
	$no_banner_class_tabs = ' dokan-store-tabs-no-banner';

} else {
	$no_banner_class = '';
	$no_banner_class_tabs = '';
}
?>
<div class="profile-frame<?php echo $no_banner_class; ?> ws">

    <div class="profile-info-box profile-layout-<?php echo $profile_layout; ?>">
        <?php if ( $store_user->get_banner() ) { ?>
            <img src="<?php echo $store_user -> get_banner(); ?>"
                 alt="<?php echo $store_user -> get_shop_name(); ?>"
                 title="<?php echo $store_user -> get_shop_name(); ?>"
                 class="profile-info-img">
        <?php } else { ?>
            <div class="profile-info-img dummy-image">&nbsp;</div>
        <?php } ?>

        <div class="profile-info-summery-wrapper dokan-clearfix">
            <div class="profile-info-summery">
                <div class="profile-info-head">
                    <div class="profile-img <?php echo $profile_img_class; ?>">
                        <?php echo get_avatar($store_user -> get_id(), 150); ?>
                    </div>
                    <?php if ( ! empty( $store_user->get_shop_name() ) && 'default' === $profile_layout ) { ?>
                        <h1 class="store-name"><?php echo esc_html($store_user -> get_shop_name()); ?></h1>
                    <?php }

							// $userdata = get_user_meta($store_user -> get_id());
							// $description = $userdata['description'][0];
							// if($description){
							// echo '<a class="centertext block" href="#" id="link-story"><strong>Our Story</strong></a><br/>';//<div class="hidden" id="bio">'.$description.'</div>'
							// echo '<div id="pop-story" class="pop hiding" style="display: none;"><a class="closepop" href="#"></a><div class="wrap-modal"><div class="modal-pop padded"><h3 class="centertext">Our Story</h3><div class="padded">'.$description.'</div></div></div></div>';
							// }
                    ?>
                </div>

                <div class="profile-info">
                    <?php if ( ! empty( $store_user->get_shop_name() ) && 'default' !== $profile_layout ) { ?>
                        <h1 class="store-name"><?php echo esc_html($store_user -> get_shop_name()); ?></h1>
                    <?php } ?>

                    <ul class="dokan-store-info">
                        <?php 
                        
                       
                        
                        
                        
                        if ( isset( $store_address ) && !empty( $store_address ) ) { ?>
                            <li class="dokan-store-address"><i class="fa fa-map-marker"></i>
                                <?php echo $store_address; ?>
                            </li>
                        <?php } ?>

                        <?php if ( !empty( $store_user->get_phone() ) ) { ?>
                            <li class="dokan-store-phone">
                                <i class="fa fa-mobile"></i>
                                <a href="tel:<?php echo esc_html($store_user -> get_phone()); ?>"><?php echo esc_html($store_user -> get_phone()); ?></a>
                            </li>
                        <?php } ?>

                        <?php 
                        
                        // $userdata = get_user_meta($store_user -> id);
						// $user_meta = unserialize($userdata['dokan_profile_settings'][0]);
						// inspect($store_user);
						
						
						$user_info = get_userdata($store_user -> id);						
						$website=$user_info->data->user_url;
						//inspect($user_info->data->user_url);
                        if ($website) { ?>
                            <li class="dokan-store-website">      
                            	<i class="fa fa-globe" aria-hidden="true"></i>                          
                                <a target="_blank" href="<?php echo antispambot($website); ?>">Website</a>
                            </li>
                        <?php } ?>
                        
                        <?php if ( $store_user->show_email() == 'yes' ) { ?>
                            <li class="dokan-store-email">
                                <i class="fa fa-envelope-o"></i>
                                <a href="mailto:<?php echo antispambot($store_user -> get_email()); ?>"><?php echo antispambot($store_user -> get_email()); ?></a>
                            </li>
                        <?php } ?>

                        <li class="dokan-store-rating">
                            <i class="fa fa-star"></i>
                            <?php dokan_get_readable_seller_rating($store_user -> get_id()); ?>
                        </li>
                    </ul>

                    <?php if ( $social_fields ) { ?>
                        <div class="store-social-wrapper">
                            <ul class="store-social">
                                <?php foreach( $social_fields as $key => $field ) { ?>
                                    <?php if ( !empty( $social_info[ $key ] ) ) { ?>
                                        <li>
                                            <a href="<?php echo esc_url($social_info[$key]); ?>" target="_blank"><i class="fa fa-<?php echo $field['icon']; ?>"></i></a>
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                            </ul>
                        </div>
                    <?php } ?>
</div> <!-- .profile-info -->
</div><!-- .profile-info-summery -->
</div><!-- .profile-info-summery-wrapper -->
</div> <!-- .profile-info-box -->
</div> <!-- .profile-frame -->

<?php


if ( $store_tabs ) { ?>
<div class="dokan-store-tabs<?php echo $no_banner_class_tabs; ?>">
<ul class="dokan-list-inline">
<?php foreach( $store_tabs as $key => $tab ) { ?>
<li><a href="<?php echo esc_url($tab['url']); ?>"><?php echo $tab['title']; ?></a></li>
<?php } ?>
<?php do_action('dokan_after_store_tabs', $store_user -> get_id()); ?>
</ul>
</div>
<?php }

	$userdata = get_user_meta($store_user -> get_id());
	$description = $userdata['description'][0];
	if($description){
	//echo '<a class="centertext coolbutton" href="#" id="link-story">Our Story</a>';//<div class="hidden" id="bio">'.$description.'</div>'
	echo '<h3 class="centertext">Our Story</h3><div class="padded">'.$description.'</div>';
	}
?>