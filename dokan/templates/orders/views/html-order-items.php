<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;

// Get the payment gateway
$payment_gateway = wc_get_payment_gateway_by_order( $order );

// Get line items
$line_items          = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
$line_items_fee      = $order->get_items( 'fee' );
$line_items_shipping = $order->get_items( 'shipping' );

if ( wc_tax_enabled() ) {
	$order_taxes         = $order->get_taxes();
	$tax_classes         = WC_Tax::get_tax_classes();
	$classes_options     = array();
	$classes_options[''] = __( 'Standard', 'dokan' );

	if ( $tax_classes ) {
		foreach ( $tax_classes as $class ) {
			$classes_options[ sanitize_title( $class ) ] = $class;
		}
	}

	// Older orders won't have line taxes so we need to handle them differently :(
	$tax_data = '';
	if ( $line_items ) {
		$check_item = current( $line_items );
		$tax_data   = maybe_unserialize( isset( $check_item['line_tax_data'] ) ? $check_item['line_tax_data'] : '' );
	} elseif ( $line_items_shipping ) {
		$check_item = current( $line_items_shipping );
		$tax_data = maybe_unserialize( isset( $check_item['taxes'] ) ? $check_item['taxes'] : '' );
	} elseif ( $line_items_fee ) {
		$check_item = current( $line_items_fee );
		$tax_data   = maybe_unserialize( isset( $check_item['line_tax_data'] ) ? $check_item['line_tax_data'] : '' );
	}

	$legacy_order     = ! empty( $order_taxes ) && empty( $tax_data ) && ! is_array( $tax_data );
	$show_tax_columns = ! $legacy_order || sizeof( $order_taxes ) === 1;
}
?>
<div class="woocommerce_order_items_wrapper wc-order-items-editable">
	<table cellpadding="0" cellspacing="0" class="woocommerce_order_items dokan-table dokan-table-strip">
		<thead>
			<tr>
				<!-- <th><input type="checkbox" class="check-column" /></th> -->
				<th class="item sortable" colspan="2" data-sort="string-ins"><?php _e( 'Item', 'dokan' ); ?></th>

				<?php do_action( 'woocommerce_admin_order_item_headers' ); ?>

				<th class="item_cost sortable" data-sort="float"><?php _e( 'Cost', 'dokan' ); ?></th>
				<th class="quantity sortable" data-sort="int"><?php _e( 'Qty', 'dokan' ); ?></th>
				<th class="line_cost sortable" data-sort="float"><?php _e( 'Total', 'dokan' ); ?></th>

				<?php
					if ( empty( $legacy_order ) && ! empty( $order_taxes ) ) :
						foreach ( $order_taxes as $tax_id => $tax_item ) :
							$tax_class      = wc_get_tax_class_by_tax_id( $tax_item['rate_id'] );
							$tax_class_name = isset( $classes_options[ $tax_class ] ) ? $classes_options[ $tax_class ] : __( 'Tax', 'dokan' );
							$column_label   = ! empty( $tax_item['label'] ) ? $tax_item['label'] : __( 'Tax', 'dokan' );
							?>
								<th class="line_tax tips" data-tip="<?php
										echo esc_attr( $tax_item['name'] . ' (' . $tax_class_name . ')' );
									?>">
									<?php echo esc_attr( $column_label ); ?>
									<input type="hidden" class="order-tax-id" name="order_taxes[<?php echo $tax_id; ?>]" value="<?php echo esc_attr( $tax_item['rate_id'] ); ?>">
									<a class="delete-order-tax" href="#" data-rate_id="<?php echo $tax_id; ?>"></a>
								</th>
							<?php
						endforeach;
					endif;
				?>
				<th class="wc-order-edit-line-item" width="1%">&nbsp;</th>
			</tr>
		</thead>
		<tbody id="order_line_items">
		<?php
			foreach ( $line_items as $item_id => $item ) {
				$_product  = $order->get_product_from_item( $item );
				// $item_meta = get_metadata( 'order_item', $item_id );

				include( 'html-order-item.php' );

				do_action( 'woocommerce_order_item_' . $item['type'] . '_html', $item_id, $item );
			}
		?>
		</tbody>
		<tbody id="order_shipping_line_items">
		<?php
			$shipping_methods = WC()->shipping() ? WC()->shipping->load_shipping_methods() : array();
			foreach ( $line_items_shipping as $item_id => $item ) {
				include( 'html-order-shipping.php' );
			}
		?>
		</tbody>
		<tbody id="order_fee_line_items">
		<?php
			foreach ( $line_items_fee as $item_id => $item ) {
				include( 'html-order-fee.php' );
			}
		?>
		</tbody>
		<tbody id="order_refunds">
		<?php
			if ( $refunds = $order->get_refunds() ) {
				foreach ( $refunds as $refund ) {
					include( 'html-order-refund.php' );
				}
			}
		?>
		</tbody>
	</table>
</div>
<div class="wc-order-data-row wc-order-totals-items wc-order-items-editable">
	<?php
		$coupons = $order->get_items( array( 'coupon' ) );
		if ( $coupons ) {
			?>
			<div class="wc-used-coupons">
				<ul class="wc_coupon_list"><?php
					echo '<li><strong>' . __( 'Coupon(s) Used', 'dokan' ) . '</strong></li>';
					foreach ( $coupons as $item_id => $item ) {
						$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish' LIMIT 1;", $item['name'] ) );

						$link = $post_id ? add_query_arg( array( 'post' => $post_id, 'view' => 'add_coupons', 'action' => 'edit' ), dokan_get_navigation_url( 'coupons' ) ) : dokan_get_navigation_url( 'coupons' );

						echo '<li class="code"><a href="' . esc_url( $link ) . '" class="tips" data-tip="' . esc_attr( wc_price( $item['discount_amount'], array( 'currency' => dokan_replace_func( 'get_order_currency', 'get_currency', $order ) ) ) ) . '"><span>' . esc_html( $item['name'] ). '</span></a></li>';
					}
				?></ul>
			</div>
			<?php
		}
	?>
	<table class="wc-order-totals">
		<tr>
			<td><?php _e( 'Discount', 'dokan' ); ?> <span class="tips" data-tip="<?php _e( 'This is the total discount. Discounts are defined per line item.', 'dokan' ); ?>">[?]</span>:</td>
			<td class="total">
				<?php echo wc_price( $order->get_total_discount(), array( 'currency' => dokan_replace_func( 'get_order_currency', 'get_currency', $order ) ) ); ?>
			</td>
			<td width="1%"></td>
		</tr>

		<?php do_action( 'woocommerce_admin_order_totals_after_discount', dokan_get_prop( $order, 'id' ) ); ?>

		<tr>
			<td><?php _e( 'Shipping', 'dokan' ); ?> <span class="tips" data-tip="<?php _e( 'This is the shipping and handling total costs for the order.', 'dokan' ); ?>">[?]</span>:</td>
			<td class="total"><?php echo wc_price( $order->get_total_shipping(), array( 'currency' => dokan_replace_func( 'get_order_currency', 'get_currency', $order ) ) ); ?></td>
			<td width="1%"></td>
		</tr>

		<?php do_action( 'woocommerce_admin_order_totals_after_shipping', dokan_get_prop( $order, 'id' ) ); ?>

		<?php if ( wc_tax_enabled() ) : ?>
			<?php foreach ( $order->get_tax_totals() as $code => $tax ) : ?>
				<tr>
					<td><?php echo $tax->label; ?>:</td>
					<td class="total"><?php echo $tax->formatted_amount; ?></td>
					<td width="1%"></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_admin_order_totals_after_tax', dokan_get_prop( $order, 'id' ) ); ?>

		<tr>
			<td><?php _e( 'Order Total', 'dokan' ); ?>:</td>
			<td class="total">
				<div class="view"><?php echo $order->get_formatted_order_total(); ?></div>
				<div class="edit" style="display: none;">
					<input type="text" class="wc_input_price" id="_order_total" name="_order_total" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" value="<?php echo ( isset( $data['_order_total'][0] ) ) ? esc_attr( wc_format_localized_price( $data['_order_total'][0] ) ) : ''; ?>" />
					<div class="clear"></div>
				</div>
			</td>
			<td><?php if ( $order->is_editable() ) : ?><div class="wc-order-edit-line-item-actions"><a class="edit-order-item" href="#"></a></div><?php endif; ?></td>
		</tr>

		<?php do_action( 'woocommerce_admin_order_totals_after_total', dokan_get_prop( $order, 'id' ) ); ?>

		<tr>
			<td class="refunded-total"><?php _e( 'Refunded', 'dokan' ); ?>:</td>
			<td class="total refunded-total">-<?php echo wc_price( $order->get_total_refunded(), array( 'currency' => dokan_replace_func( 'get_order_currency', 'get_currency', $order ) ) ); ?></td>
			<td width="1%"></td>
		</tr>

		<?php do_action( 'woocommerce_admin_order_totals_after_refunded', dokan_get_prop( $order, 'id' ) ); ?>

	</table>
	<div class="clear"></div>
</div>
<div class="wc-order-data-row wc-order-bulk-actions">

	<p class="add-items">

		<?php if ( ( $order->get_total() - $order->get_total_refunded() ) > 0 ) : ?>
			<button type="button" class="dokan-btn dokan-btn-default refund-items"><?php _e( 'Request Refund', 'dokan' ); ?></button>
		<?php endif; ?>
	</p>
	<div class="clear"></div>
</div>

<?php if ( ( $order->get_total() - $order->get_total_refunded() ) > 0 ) : ?>
<div class="wc-order-data-row wc-order-refund-items" style="display: none;">
	<table class="wc-order-totals dokan-table dokan-table-strip">

		<tr>
			<td><?php _e( 'Amount already refunded', 'dokan' ); ?>:</td>
			<td class="total">-<?php echo wc_price( $order->get_total_refunded(), array( 'currency' => dokan_replace_func( 'get_order_currency', 'get_currency', $order ) ) ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Total available to refund', 'dokan' ); ?>:</td>
			<td class="total"><?php echo wc_price( $order->get_total() - $order->get_total_refunded(), array( 'currency' => dokan_replace_func( 'get_order_currency', 'get_currency', $order ) ) ); ?></td>
		</tr>
		<tr>
			<td><label for="refund_amount"><?php _e( 'Refund amount', 'dokan' ); ?>:</label></td>
			<td class="total">
				<input type="text" class="text" id="refund_amount" name="refund_amount" class="wc_input_price" />
				<div class="clear"></div>
			</td>
		</tr>
		<tr>
			<td><label for="refund_reason"><?php _e( 'Reason for refund (optional)', 'dokan' ); ?>:</label></td>
			<td class="total">
				<input type="text" class="text" id="refund_reason" name="refund_reason" />
				<div class="clear"></div>
			</td>
		</tr>
	</table>
	<div class="clear"></div>
	<div class="refund-actions">
		<?php
		$refund_amount            = '<span class="wc-order-refund-amount">' . wc_price( 0, array( 'currency' => dokan_replace_func( 'get_order_currency', 'get_currency', $order ) ) ) . '</span>'; ?>

		<button type="button" class="dokan-btn dokan-btn-default do-manual-refund tips" data-tip="<?php esc_attr_e( 'You will need to manually issue a refund through your payment gateway after using this.', 'dokan' ); ?>"><?php printf( _x( 'Submit Refund Request %s', 'Submit Refund Request $amount', 'dokan' ), $refund_amount ); ?></button>
		<button type="button" class="dokan-btn dokan-btn-default cancel-action"><?php _e( 'Cancel', 'dokan' ); ?></button>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>
<?php endif; ?>

<script type="text/template" id="wc-modal-add-products">
	<div class="wc-backbone-modal">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<a class="modal-close modal-close-link" href="#"><span class="close-icon"><span class="screen-reader-text">Close media panel</span></span></a>
					<h1><?php _e( 'Add products', 'dokan' ); ?></h1>
				</header>
				<article>
					<form action="" method="post">
						<input type="hidden" id="add_item_id" name="add_order_items" class="wc-product-search" style="width: 100%;" data-placeholder="<?php _e( 'Search for a product&hellip;', 'dokan' ); ?>" data-multiple="true" />
					</form>
				</article>
				<footer>
					<div class="inner">
						<button id="btn-ok" class="button button-primary button-large"><?php _e( 'Add', 'dokan' ); ?></button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close">&nbsp;</div>
</script>

<script type="text/template" id="wc-modal-add-tax">
	<div class="wc-backbone-modal">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<a class="modal-close modal-close-link" href="#"><span class="close-icon"><span class="screen-reader-text">Close media panel</span></span></a>
					<h1><?php _e( 'Add tax', 'dokan' ); ?></h1>
				</header>
				<article>
					<form action="" method="post">
						<table class="widefat">
							<thead>
								<tr>
									<th>&nbsp;</th>
									<th><?php _e( 'Rate name', 'dokan' ); ?></th>
									<th><?php _e( 'Tax class', 'dokan' ); ?></th>
									<th><?php _e( 'Rate code', 'dokan' ); ?></th>
									<th><?php _e( 'Rate %', 'dokan' ); ?></th>
								</tr>
							</thead>
						<?php
							$rates = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}woocommerce_tax_rates ORDER BY tax_rate_name LIMIT 100" );

							foreach ( $rates as $rate ) {
								echo '
									<tr>
										<td><input type="radio" id="add_order_tax_' . absint( $rate->tax_rate_id ) . '" name="add_order_tax" value="' . absint( $rate->tax_rate_id ) . '" /></td>
										<td><label for="add_order_tax_' . absint( $rate->tax_rate_id ) . '">' . WC_Tax::get_rate_label( $rate ) . '</label></td>
										<td>' . ( isset( $classes_options[ $rate->tax_rate_class ] ) ? $classes_options[ $rate->tax_rate_class ] : '-' ) . '</td>
										<td>' . WC_Tax::get_rate_code( $rate ) . '</td>
										<td>' . WC_Tax::get_rate_percent( $rate ) . '</td>
									</tr>
								';
							}
						?>
						</table>
						<?php if ( absint( $wpdb->get_var( "SELECT COUNT(tax_rate_id) FROM {$wpdb->prefix}woocommerce_tax_rates;" ) ) > 100 ) : ?>
							<p>
								<label for="manual_tax_rate_id"><?php _e( 'Or, enter tax rate ID:', 'dokan' ); ?></label><br/>
								<input type="number" name="manual_tax_rate_id" id="manual_tax_rate_id" step="1" placeholder="<?php _e( 'Optional', 'dokan' ); ?>" />
							</p>
						<?php endif; ?>
					</form>
				</article>
				<footer>
					<div class="inner">
						<button id="btn-ok" class="button button-primary button-large"><?php _e( 'Add', 'dokan' ); ?></button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close">&nbsp;</div>
</script>
