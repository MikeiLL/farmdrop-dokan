<?php
/**
 *  Dokan Dashboard Template
 *
 *  Dokan Main Dahsboard template for Fron-end
 *
 *  @since 2.4
 *
 *  @package dokan
 */
?>
<div class="dokan-dashboard-wrap">
    <?php
	/**
	 *  dokan_dashboard_content_before hook
	 *
	 *  @hooked get_dashboard_side_navigation
	 *
	 *  @since 2.4
	 */
	do_action('dokan_dashboard_content_before');
    ?>
    <div class="dokan-dashboard-content">
        <?php
		/**
		 *  dokan_dashboard_content_before hook
		 *
		 *  @hooked show_seller_dashboard_notice
		 *
		 *  @since 2.4
		 */
		do_action('dokan_help_content_inside_before');
		$day = date('w');
		$week_start = date('Y-m-d', strtotime('-' . $day . ' days'));
		$saturday = date('m/d/Y', strtotime($week_start . ' -1 day'));
        ?>
        <article class="help-content-area">
            <h1>Weekly Orders</h1>
            <div>
                <h4>Start Date</h4>
                <div class="input _messages">
                    <input type="text" value="<?php echo $saturday?>" id="startdate" class="date form-control dokan-ajax-search-textfield w50">
                    <br/>
                    <a id="cancel" href="javascript:getReport()" class="block button fullwidth inline w50 distanttop">Generate Report</a>
                </div>
                <div class="loader hidden"></div>
                <div id="messages"></div>
                <div class="distanttop hidden" id="get_pdf">
                    <a id="pdf_link" href="javascript:" target="_blank" class="block button fullwidth inline">Open Report</a>
                </div>
            </div>
            <?php if ( current_user_can( 'manage_options' ) ) {
            ?>
            <br/>
            <br/>
            <h1 class="distanttop">Weekly Orders for All Vendors</h1>
            <div>
            <h4>Start Date</h4>
            <div class="input _messages">
            <input type="text" value="<?php echo $saturday?>" id="startdate_all" class="date form-control dokan-ajax-search-textfield w50">
            <br/>
            <a id="cancel" href="javascript:getReportAll()" class="block button fullwidth inline w50 distanttop">Generate Report</a>
            </div>
            <div class="loader hidden"></div>
            <div id="messages_all"></div>
            <div class="distanttop hidden" id="get_pdf_all">
            <a id="pdf_link_all" href="javascript:" target="_blank" class="block button fullwidth inline">Open Report</a>
            </div>
            </div>
            <?php }; ?>
        </article>
        <!-- .dashboard-content-area -->
        <?php
		/**
		 *  dokan_dashboard_content_inside_after hook
		 *
		 *  @since 2.4
		 */
		do_action('dokan_dashboard_content_inside_after');
        ?>
    </div>
    <!-- .dokan-dashboard-content -->
    <?php
	/**
	 *  dokan_dashboard_content_after hook
	 *
	 *  @since 2.4
	 */
	do_action('dokan_dashboard_content_after');
	echo '<script type="text/javascript">var webfixAjax = {"ajaxurl": "' . admin_url('admin-ajax.php') . '","nextNonce": "' . wp_create_nonce('myajax-next-nonce') . '"};</script>';
?>
</div>
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/jquery.date.min.js"></script>
<script>
	var startDate;
	function setActivity(busy) {
		if (busy == true) {
			$('.loader').removeClass('hidden').show();
		} else {
			$('.loader').addClass('hidden').hide();
		}
	}

	function getReport() {
		setActivity(true);
		$('#messages').html('');
		$('#get_pdf').hide();
		startDate = $('#startdate').val();
		console.log('startDate', startDate);
		$.ajax({
			type : 'POST',
			url : webfixAjax.ajaxurl,
			data : {
				nonce : webfixAjax.nextNonce,
				action : 'save_report',
				date : startDate
			},
			success : function(response) {
				console.log(response);
				if (response.result == true) {
					$('#pdf_link').attr('href', response.data);
					$('#get_pdf').removeClass('hidden').show();
					$('#messages').html('Success, click on Open Report to review or print');
				} else {
					$('#messages').html(response.messages.join('<br/>'));
				}
				setActivity(false);
			},
			error : function(error) {
				setActivity(false);
			}

		});
	}

	function getReportAll() {
		setActivity(true);
		$('#messages_all').html('');
		$('#get_pdf_all').hide();
		startDate = $('#startdate_all').val();
		console.log('startDate', startDate);
		$.ajax({
			type : 'POST',
			url : webfixAjax.ajaxurl,
			data : {
				nonce : webfixAjax.nextNonce,
				action : 'print_orders',
				date : startDate
			},
			success : function(response) {
				console.log(response);
				if (response.result == true) {
					$('#pdf_link_all').attr('href', response.data);
					$('#get_pdf_all').removeClass('hidden').show();
					$('#messages_all').html('Success, click on Open Report to review or print');
				} else {
					$('#messages_all').html(response.messages.join('<br/>'));
				}
				setActivity(false);
			},
			error : function(error) {
				setActivity(false);
			}

		});
	}
</script>
<script>
	$().ready(function() {
		$('input[type=text].date').datepicker({
			autoHide : true
		});
		//updateRecords(0);
	}); 
</script>
<?php
// $seller_id = get_current_user_id();
// $date = new DateTime('11/01/2017');
// $start_date = sanitize_key($date -> format('Y-m-d'));
// //
// $today = new DateTime();
// $end_date = sanitize_key($today -> format('Y-m-d'));
// $items_all = array();
//
// $active_sellers = dokan_get_sellers(array('number' => -1, ));
// $sellers = array_unique(array_keys($active_sellers));
// inspect($sellers);
// foreach ($sellers as $seller_id => $seller_products) {
// //
// $user_orders = dokan_get_seller_orders_by_date($start_date, $end_date, $seller_id, $status = 'all');
// if ($user_orders) {
// //$messages[] = sizeof($orders);
// foreach ($user_orders as $order) {
// $the_order = new WC_Order($order -> order_id);
// $messages[] = $order -> order_id;
// foreach ($the_order -> get_items() as $item_id => $item_data) {
// $product = $item_data -> get_product();
// $product_name = $product -> get_name();
// $item_quantity = $item_data -> get_quantity();
// $item_total = $item_data -> get_total();
// $items_all[] = array('id' => $product -> id, 'name' => $product_name, 'quantity' => $item_quantity, 'total' => $item_total);
// }
// }
// inspect($items_all);
// $array_consolidated = array();
// foreach ($items_all as $item) {
// if (isset($array_consolidated[$item['id']])) {
// $array_consolidated[$item['id']]['quantity'] += $item['quantity'];
// $array_consolidated[$item['id']]['total'] += $item['total'];
// } else
// $array_consolidated[$item['id']] = array('name' => $item['name'], 'quantity' => $item['quantity'], 'total' => $item['total']);
// }
// inspect($array_consolidated);
// }
//}
global $wpdb;
// $end_date = date('Y-m-d 00:00:00', strtotime($end_date));
// $end_date = date('Y-m-d h:i:s', strtotime($end_date . '-1 minute'));
// $start_date = date('Y-m-d', strtotime($start_date));
 //$status_where = ($status == 'all') ? '' : $wpdb -> prepare(' AND order_status = %s', $status);
	$status_where = $wpdb -> prepare(' AND order_status = %s', 'wc-completed');
 $date_query = $wpdb -> prepare(' AND DATE( p.post_date ) >= %s AND DATE( p.post_date ) <= %s', '2018-01-06', '2018-01-13');
 /*$sql = "SELECT * FROM (
 SELECT do.*, p.post_date, p.post_author, MAX(CASE WHEN um.meta_key = 'last_name' THEN meta_value END) AS last_name
 FROM {$wpdb->prefix}dokan_orders AS do
 LEFT JOIN $wpdb->posts p ON do.order_id = p.ID
 LEFT JOIN $wpdb->users u on p.post_author = u.ID
 INNER JOIN $wpdb->usermeta as um ON p.post_author = um.user_id
 WHERE
 p.post_status != 'trash'
 $date_query
 GROUP BY do.order_id
 ORDER BY p.post_date ASC)
 AS tmp_table GROUP BY LOWER(`last_name`)";*/
 	$sql = "SELECT do.*, p.post_date
FROM {$wpdb->prefix}dokan_orders AS do
LEFT JOIN $wpdb->posts p ON do.order_id = p.ID
WHERE
p.post_status != 'trash'
$status_where
$date_query
GROUP BY do.order_id
ORDER BY p.post_date ASC";
 //do.seller_id = %d AND
 $orders = $wpdb -> get_results($sql);

 //mz_pr($sql);
 $orders_html .= '';

	if ($orders) {
		$order_index = 0;
		$stamp_orders = current_time('Ymd', 0);
		// Build an array of "orders". Because it's multivendor, each customer may have multiple orders
		// from the same checkout.
		foreach ($orders as $order) {
			// The WC_Order object contains date and payment information of transaction
			$the_order = new WC_Order($order -> order_id);
			//$messages[] = $order -> order_id;
			$date_order = new DateTime($order -> post_date);
			$customer_phone = esc_html(get_post_meta($order -> order_id, '_billing_phone', true));
			$user_info = '';
			if ($the_order -> get_user_id()) {
				$user_info = get_userdata($the_order -> get_user_id());
			}
			$customer_id = $the_order -> get_user_id();

			if (!empty($user_info)) {
				$user_display = '';
				$sort_key = $user_info -> last_name;
				if ($user_info -> first_name || $user_info -> last_name) {
					$user_display .= esc_html($user_info -> first_name . ' ' . $user_info -> last_name);
				} else {
					$user_display .= esc_html($user_info -> display_name);
				}			} else {
				$customer_id = $stamp_orders . sprintf('%04d', $order_index);
				$user = esc_html(get_post_meta($order -> order_id, '_billing_first_name', true)) . ' ' . esc_html(get_post_meta($order -> order_id, '_billing_last_name', true)) . ' (guest)';
				$sort_key = 0;
				//$order -> billing_first_name
				$order_index++;
			}
			

			if ('0000-00-00 00:00:00' == dokan_get_date_created($the_order)) {
				$t_time = $h_time = __('Unpublished', 'dokan-lite');
			} else {
				$t_time = get_the_time(__('Y/m/d g:i:s A', 'dokan-lite'), dokan_get_prop($the_order, 'id'));
				$gmt_time = strtotime(dokan_get_date_created($the_order) . ' UTC');
				$time_diff = current_time('timestamp', 1) - $gmt_time;
				if ($time_diff > 0 && $time_diff < 24 * 60 * 60)
					$h_time = sprintf(__('%s ago', 'dokan-lite'), human_time_diff($gmt_time, current_time('timestamp', 1)));
				else
					$h_time = get_the_time(__('Y/m/d  h:i:s A', 'dokan-lite'), dokan_get_prop($the_order, 'id'));
			}
			$items = '';
			
			// There will be one order per customer per vendor. So the following debug might show:
			// Ingrid, Ingrid, Ingrid, Tom, Tom, etc...
			//mz_pr($the_order->data['billing']['first_name']);
			foreach ($the_order -> get_items() as $item_id => $item_data) {
				$product = $item_data -> get_product();

				if (is_object($product)) {
					$product_name = $product -> get_name();
					// If it's the farmdrop fee, don't include it in the report
					if (strpos(strtolower($product_name), 'fee') !== false) {

					} else {$item_quantity = $item_data -> get_quantity();
						$store_info = dokan_get_store_info($order -> seller_id);
						$vendor = $store_info['store_name'];
						$item_total = $item_data -> get_total();
						// If it's not a user, do not add to items all
						if ($sort_key === 0) continue;
						$items_all[] = array('id' => $product -> id, 
											'order_id' => $order -> order_id, 
											'customer_id' => $customer_id, 
											'customer_lastname' => $sort_key,
											'customer' => $user_display . ' (Phone: ' . format_phone_number($customer_phone) . ')', 
											'order_date_post' => $date_order -> format('m/d/Y  h:i:s A'), 
											'order_date' => esc_html(apply_filters('post_date_column_time', dokan_date_time_format($h_time, true), dokan_get_prop($the_order, 'id'))), 
											'name' => $product_name, 
											'vendor' => $vendor, 
											'quantity' => $item_quantity, 
											'total' => $item_total);
					}
				}

			}

			//$messages[] = inspect($items_all, false, true);

			// $orders_html .= '<tr>
			// <td style="border-bottom: 1px solid #ddd;text-align:left;vertical-align:top;">' . esc_attr($the_order -> get_order_number()) . '</td><td style="border-bottom: 1px solid #ddd;text-align:left;vertical-align:top;">' . $items . '</td><td style="border-bottom: 1px solid #ddd;text-align:right;vertical-align:top;">' . $the_order -> get_formatted_order_total() . '</td>
			// <td  style="border-bottom: 1px solid #ddd;text-align:left;vertical-align:top;">' . $user_display . '</td><td style="border-bottom: 1px solid #ddd;text-align:left;vertical-align:top;">' . esc_html(apply_filters('post_date_column_time', dokan_date_time_format($h_time, true), dokan_get_prop($the_order, 'id'))) . '</td></tr>';
		}

		$array_consolidated = array();
		mz_pr($items_all[0]);
		mz_pr($items_all[1]);
		mz_pr($items_all[2]);
		// Loop through the item's all array and build a single key for each customer
		foreach ($items_all as $item) {
			if (isset($array_consolidated[$item['customer_id']])) {
				$array_consolidated[$item['customer_id']]['items'][] = array('name' => $item['name'], 'vendor' => $item['vendor'], 'quantity' => $item['quantity'], 'total' => $item['total'], 'date' => $item['order_date_post']);

			} else {
				$array_consolidated[$item['customer_id']]['customer'] = $item['customer'];
				$array_consolidated[$item['customer_id']]['customer_lastname'] = $item['customer_lastname'];
				$array_consolidated[$item['customer_id']]['items'] = array( array('name' => $item['name'], 'vendor' => $item['vendor'], 'quantity' => $item['quantity'], 'total' => $item['total'], 'date' => $item['order_date_post']));
			}
		}
		//inspect($array_consolidated);
		//ksort($array_consolidated, SORT_NATURAL);
		usort($array_consolidated, function($a, $b) {
			if ($a == $b) {
				return 0;
			}
			return ($a['customer_lastname'] < $b['customer_lastname']) ? -1 : 1;
		});
		
		$messages[] = inspect($array_consolidated, false, true);
		if ($array_consolidated) {
			$page_break = false;
			foreach ($array_consolidated as $customer) {
				if ($page_break == true) {
					$orders_html .= '<div style="page-break-before:always"></div>';
				} else {
					$page_break = true;
				}
				$orders_html .= '
						<h2 style="text-align:left;vertical-align:top;font-size:15pt;"><strong>' . strtoupper($customer['customer']) . '<strong></h2>						
					';

				$orders_html .= '<table style="width:100%;"><tbody>';
				$orders_html .= '<thead>
									<tr>
									<th style="border-bottom: 1px solid #ddd;text-align:left;">Item</th>
									<th style="border-bottom: 1px solid #ddd;text-align:left;">Vendor</th>
									<th style="border-bottom: 1px solid #ddd;text-align:right;">Quantity</th>
									<th style="border-bottom: 1px solid #ddd;text-align:right;">Total</th>
									<th style="border-bottom: 1px solid #ddd;text-align:right;">Date</th>
									</tr>
									</thead>';
				foreach ($customer['items'] as $item) {
					$orders_html .= '<tr>
					<td style="border-bottom: 1px solid #ddd;text-align:left;vertical-align:top;">' . $item['name'] . '</td>
					<td style="border-bottom: 1px solid #ddd;text-align:left;vertical-align:top;">' . $item['vendor'] . '</td>
					<td style="border-bottom: 1px solid #ddd;text-align:right;vertical-align:top;">' . $item['quantity'] . '</td>
					<td style="border-bottom: 1px solid #ddd;text-align:right;vertical-align:top;">' . $item['total'] . '</td>
					<td style="border-bottom: 1px solid #ddd;text-align:right;vertical-align:top;">' . $item['date'] . '</td>
					</tr>';
				}
				$orders_html .= '</tbody></table>';

			}
		}
	}
?>
<!-- .dokan-dashboard-wrap -->
