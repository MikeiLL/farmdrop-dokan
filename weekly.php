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
		$week_start = date('m/d/Y', strtotime('-' . $day . ' days'));
        ?>
        <article class="help-content-area">
            <h1>Weekly Orders</h1>
            <div>
                <h4>Start Date</h4>
                <div class="input _messages">
                    <input type="text" value="<?php echo $week_start?>" id="startdate" class="date form-control dokan-ajax-search-textfield w50">
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
            <input type="text" value="<?php echo $week_start?>" id="startdate_all" class="date form-control dokan-ajax-search-textfield w50">
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
// global $wpdb;
// $end_date = date('Y-m-d 00:00:00', strtotime($end_date));
// $end_date = date('Y-m-d h:i:s', strtotime($end_date . '-1 minute'));
// $start_date = date('Y-m-d', strtotime($start_date));
// //$status_where = ($status == 'all') ? '' : $wpdb -> prepare(' AND order_status = %s', $status);
// $date_query = $wpdb -> prepare(' AND DATE( p.post_date ) >= %s AND DATE( p.post_date ) <= %s', $start_date, $end_date);
// $sql = "SELECT do.*, p.post_date
// FROM {$wpdb->prefix}dokan_orders AS do
// LEFT JOIN $wpdb->posts p ON do.order_id = p.ID
// WHERE
// p.post_status != 'trash'
// $date_query
// GROUP BY do.order_id
// ORDER BY p.post_date ASC";
// //do.seller_id = %d AND
// $orders = $wpdb -> get_results($sql);
// //inspect($sql);
// inspect($orders);
// foreach ($orders as $order) {
// $the_order = new WC_Order($order -> order_id);
// //$messages[] = $order -> order_id;
// $date_order = new DateTime($order -> post_date);
// foreach ($the_order -> get_items() as $item_id => $item_data) {
// $product = $item_data -> get_product();
// $product_name = $product -> get_name();
// $item_quantity = $item_data -> get_quantity();
// $store_info = dokan_get_store_info($order -> seller_id);
// $vendor = $store_info['store_name'];
// $item_total = $item_data -> get_total();
// $items_all[] = array('id' => $product -> id, 'order_id' => $order -> order_id, 'order_date' => $date_order -> format('m/d/Y  h:i:s A'), 'name' => $product_name, 'vendor' => $vendor, 'quantity' => $item_quantity, 'total' => $item_total);
// }
// }
// inspect($items_all);
?>
<!-- .dokan-dashboard-wrap -->
