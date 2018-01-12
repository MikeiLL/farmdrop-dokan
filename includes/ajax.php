<?php
error_reporting(0);
function save_report() {
	// check nonce

	$messages = array();
	$nonce = $_POST['nonce'];
	if (!wp_verify_nonce($nonce, 'myajax-next-nonce')) {
		$messages[] = 'bad nonce';
		//die('Busted!');
	};

	$TIMESTAMP = current_time('YmdGis', 0);

	$start_date = NULL;

	global $woocommerce;
	$seller_id = get_current_user_id();
	$order_status = 'wc-completed';
	// 'all';
	$date;
	if (isset($_POST['date'])) {
		$date = new DateTime($_POST['date']);
		$start_date = sanitize_key($date -> format('Y-m-d'));
		//$date -> format('y-n-d')
		//$order_date = NULL;
	}

	$today = new DateTime();
	$end_date = sanitize_key($today -> format('Y-m-d'));

	$user_orders = dokan_get_seller_orders_by_date($start_date, $end_date, $seller_id, 'wc-completed');

	$orders_html = '';

	$items_all = array();
	if ($user_orders) {
		foreach ($user_orders as $order) {
			$the_order = new WC_Order($order -> order_id);
			$messages[] = $order -> order_id;
			foreach ($the_order -> get_items() as $item_id => $item_data) {
				$product = $item_data -> get_product();
				if (is_object($product)) {
					$product_name = $product -> get_name();
				} else {
					$product_name = 'Deleted Item';
				}

				$item_quantity = $item_data -> get_quantity();
				$item_total = $item_data -> get_total();
				$items_all[] = array('id' => $product -> id, 'name' => $product_name, 'quantity' => $item_quantity, 'total' => $item_total);
			}
		}
		//inspect($items_all);
		$array_consolidated = array();
		foreach ($items_all as $item) {
			if (isset($array_consolidated[$item['id']])) {
				$array_consolidated[$item['id']]['quantity'] += $item['quantity'];
				$array_consolidated[$item['id']]['total'] += $item['total'];
			} else
				$array_consolidated[$item['id']] = array('name' => $item['name'], 'quantity' => $item['quantity'], 'total' => $item['total']);
		}
		//inspect($array_consolidated);

		$orders_html .= '<h2 style="margin-left:.5in; margin-right:.5in;margin-top:.1in;margin-bottom:0in;">Items Ordered</h2><table style="width:100%;">';
		$orders_html .= '<thead>
				<tr>
                <th style="border-bottom: 1px solid #ddd;text-align:left;">Item</th>
                <th style="border-bottom: 1px solid #ddd;text-align:right;">Quantity</th>
                <th style="border-bottom: 1px solid #ddd;text-align:right;">Total</th>           
	            </tr>
	        </thead>';

		$orders_html .= '<tbody>';
		if ($array_consolidated) {
			foreach ($array_consolidated as $item_c) {
				$orders_html .= '<tr>
						<td style="border-bottom: 1px solid #ddd;text-align:left;vertical-align:top;">' . $item_c['name'] . '</td>
						<td style="border-bottom: 1px solid #ddd;text-align:right;vertical-align:top;">' . $item_c['quantity'] . '</td>
						<td style="border-bottom: 1px solid #ddd;text-align:right;vertical-align:top;">$' . $item_c['total'] . '</td>						
					</tr>';
			}
		}

		$orders_html .= '</tbody>';
		$orders_html .= '</table>';
	}

	$orders_html .= '<h2 style="margin-left:.5in; margin-right:.5in;margin-top:.1in;margin-bottom:0in;"><br/><br/>Orders</h2><table style="width:100%;">';
	$orders_html .= '<thead>
				<tr>
                <th style="border-bottom: 1px solid #ddd;text-align:left;">Order</th>
                <th style="border-bottom: 1px solid #ddd;text-align:left;">Items</th>
                <th style="border-bottom: 1px solid #ddd;text-align:right;">Order Total</th>
				<th style="border-bottom: 1px solid #ddd;text-align:left;">Customer</th>
                <th style="border-bottom: 1px solid #ddd;text-align:left;">Date</th>           
	            </tr>
	        </thead>';

	$orders_html .= '<tbody>';

	if ($user_orders) {
		foreach ($user_orders as $order) {
			$the_order = new WC_Order($order -> order_id);
			$messages[] = $order -> order_id;
			$user_info = '';
			if ($the_order -> get_user_id()) {
				$user_info = get_userdata($the_order -> get_user_id());
			}
			if (!empty($user_info)) {
				$user = '';
				if ($user_info -> first_name || $user_info -> last_name) {
					$user .= esc_html($user_info -> first_name . ' ' . $user_info -> last_name);
				} else {
					$user .= esc_html($user_info -> display_name);
				}
			} else {
				$user = 'guest';
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
					$h_time = get_the_time(__('Y/m/d', 'dokan-lite'), dokan_get_prop($the_order, 'id'));
			}
			$items = '';
			foreach ($the_order -> get_items() as $item_id => $item_data) {
				$product = $item_data -> get_product();
				if (is_object($product)) {
					$product_name = $product -> get_name();
				} else {
					$product_name = 'Deleted Item';
				}
				$item_quantity = $item_data -> get_quantity();
				$item_total = $item_data -> get_total();
				$items .= '<div>' . $product_name . ' (qty:' . $item_quantity . ', total:$' . $item_total . ')</div>';
			}

			$orders_html .= '<tr>
						<td style="border-bottom: 1px solid #ddd;text-align:left;vertical-align:top;">' . esc_attr($the_order -> get_order_number()) . '</td><td style="border-bottom: 1px solid #ddd;text-align:left;vertical-align:top;">' . $items . '</td><td style="border-bottom: 1px solid #ddd;text-align:right;vertical-align:top;">' . $the_order -> get_formatted_order_total() . '</td>
						<td  style="border-bottom: 1px solid #ddd;text-align:left;vertical-align:top;">' . $user . '</td><td style="border-bottom: 1px solid #ddd;text-align:left;vertical-align:top;">' . esc_html(apply_filters('post_date_column_time', dokan_date_time_format($h_time, true), dokan_get_prop($the_order, 'id'))) . '</td></tr>';
		}
	}
	$orders_html .= '</tbody>';
	$orders_html .= '</table>';

	$messages[] = $orders_html;
	$html = '<!doctype html>
				<html>
				    <head>
				        <style>
							*{-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;}
							body{font-family: sans-serif;line-height:1.6em;font-size:16pt}
							h1,h2{font-family: sans-serif;line-height:1.6em;font-weight:300}
							table { border-collapse:collapse; margin-top: 0;text-align: center; font-family: sans-serif;line-height:1.6em;font-size:9pt}
							td,th {padding: 0.5em;font-weight:normal}
							h2 {margin-bottom: 0;}
							
						</style>
			    	</head>
			    <body>';
	$html .= '<h1 class="title" style="margin-left:.5in; margin-right:.5in;margin-top:.1in;margin-bottom:0in;">Orders</h1>';
	$html .= $orders_html;
	$html .= '</body></html>';
	$pdf_file = dirname(__FILE__) . '/../orders/order_' . $TIMESTAMP . '.pdf';

	$handle = fopen($pdf_file, 'w') or die('Cannot open file:  ' . $pdf_file);
	$pdf_url = get_stylesheet_directory_uri() . '/orders/order_' . $TIMESTAMP . '.pdf';

	// include ('pdf/mpdf.php');
	// $mpdf = new mPDF('', 'letter', 8, '', 0, 0, 16, 16, 9, 9, 'P');
	// $mpdf -> SetDisplayMode('fullwidth', 'continuous');
	//
	// $mpdf -> WriteHTML($html);
	// //$mpdf -> Output();
	//
	// //$messages[] = $mpdf -> Output('', 'S');
	//
	// fwrite($handle, $mpdf -> Output('', 'S'));
	// fclose($handle);

	//

	///////
	include ("mpdf60/mpdf.php");
	//$mpdf = new mPDF('', 'letter', 8, '', 0, 0, 16, 16, 9, 9, 'P');
	$mpdf = new mPDF('c');

	$mpdf -> WriteHTML($html);
	fwrite($handle, $mpdf -> Output('', 'S'));
	fclose($handle);

	////
	$response = json_encode(array('result' => TRUE, 'data' => $pdf_url, 'messages' => $messages));

	// } else {
	// $messages[] = 'no orders';
	// $response = json_encode(array('result' => FALSE, 'messages' => $messages));
	// }
	// response output
	header("Content-Type: application/json");
	echo $response;
	exit ;
}

add_action('wp_ajax_nopriv_save_report', 'save_report');
add_action('wp_ajax_save_report', 'save_report');

function print_orders() {
	// check nonce

	$messages = array();
	$nonce = $_POST['nonce'];
	if (!wp_verify_nonce($nonce, 'myajax-next-nonce')) {
		$messages[] = 'bad nonce';
		//die('Busted!');
	};

	$TIMESTAMP = current_time('YmdGis', 0);

	$start_date = NULL;

	global $woocommerce;
	$seller_id = get_current_user_id();
	$order_status = 'wc-completed';
	// 'all';
	$date;
	if (isset($_POST['date'])) {
		$date = new DateTime($_POST['date']);
		$start_date = sanitize_key($date -> format('Y-m-d'));
	}

	$today = new DateTime();
	$end_date = sanitize_key($today -> format('Y-m-d'));

	global $wpdb;
	$end_date = date('Y-m-d 00:00:00', strtotime($end_date));
	$end_date = date('Y-m-d h:i:s', strtotime($end_date . '-1 minute'));
	$start_date = date('Y-m-d', strtotime($start_date));
	$status_where = $wpdb -> prepare(' AND order_status = %s', 'wc-completed');
	$date_query = $wpdb -> prepare(' AND DATE( p.post_date ) >= %s AND DATE( p.post_date ) <= %s', $start_date, $end_date);
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
	//

	$items_all = array();

	//$orders_html .= '<h2 style="margin-left:.5in; margin-right:.5in;margin-top:.1in;margin-bottom:0in;"><br/><br/>Weekly Orders for All vendors</h2><table style="width:100%;">';
	// $orders_html .= '<thead>
	// <tr>
	// <th style="border-bottom: 1px solid #ddd;text-align:left;">Order</th>
	// <th style="border-bottom: 1px solid #ddd;text-align:left;">Items</th>
	// <th style="border-bottom: 1px solid #ddd;text-align:right;">Order Total</th>
	// <th style="border-bottom: 1px solid #ddd;text-align:left;">Customer</th>
	// <th style="border-bottom: 1px solid #ddd;text-align:left;">Date</th>
	// </tr>
	// </thead>';

	$orders_html .= '';

	if ($orders) {
		$order_index = 0;
		$stamp_orders = current_time('Ymd', 0);
		foreach ($orders as $order) {
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
				$user = '';
				if ($user_info -> first_name || $user_info -> last_name) {
					$user .= esc_html($user_info -> first_name . ' ' . $user_info -> last_name);
				} else {
					$user .= esc_html($user_info -> display_name);
				}
			} else {
				$user = esc_html(get_post_meta($order -> order_id, '_billing_first_name', true)) . ' ' . esc_html(get_post_meta($order -> order_id, '_billing_last_name', true)) . ' (guest)';
				//$order -> billing_first_name
				$customer_id = $stamp_orders . sprintf('%04d', $order_index);
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

			foreach ($the_order -> get_items() as $item_id => $item_data) {
				$product = $item_data -> get_product();

				if (is_object($product)) {
					$product_name = $product -> get_name();
					if (strpos(strtolower($product_name), 'fee') !== false) {

					} else {$item_quantity = $item_data -> get_quantity();
						$store_info = dokan_get_store_info($order -> seller_id);
						$vendor = $store_info['store_name'];
						$item_total = $item_data -> get_total();
						$items_all[] = array('id' => $product -> id, 'order_id' => $order -> order_id, 'customer_id' => $customer_id, 'customer' => $user . ' (Phone: ' . format_phone_number($customer_phone) . ')', 'order_date_post' => $date_order -> format('m/d/Y  h:i:s A'), 'order_date' => esc_html(apply_filters('post_date_column_time', dokan_date_time_format($h_time, true), dokan_get_prop($the_order, 'id'))), 'name' => $product_name, 'vendor' => $vendor, 'quantity' => $item_quantity, 'total' => $item_total);
					}
				}

			}

			//$messages[] = inspect($items_all, false, true);

			// $orders_html .= '<tr>
			// <td style="border-bottom: 1px solid #ddd;text-align:left;vertical-align:top;">' . esc_attr($the_order -> get_order_number()) . '</td><td style="border-bottom: 1px solid #ddd;text-align:left;vertical-align:top;">' . $items . '</td><td style="border-bottom: 1px solid #ddd;text-align:right;vertical-align:top;">' . $the_order -> get_formatted_order_total() . '</td>
			// <td  style="border-bottom: 1px solid #ddd;text-align:left;vertical-align:top;">' . $user . '</td><td style="border-bottom: 1px solid #ddd;text-align:left;vertical-align:top;">' . esc_html(apply_filters('post_date_column_time', dokan_date_time_format($h_time, true), dokan_get_prop($the_order, 'id'))) . '</td></tr>';
		}

		$array_consolidated = array();
		foreach ($items_all as $item) {
			if (isset($array_consolidated[$item['customer_id']])) {
				$array_consolidated[$item['customer_id']]['items'][] = array('name' => $item['name'], 'vendor' => $item['vendor'], 'quantity' => $item['quantity'], 'total' => $item['total'], 'date' => $item['order_date_post']);

			} else {
				$array_consolidated[$item['customer_id']]['customer'] = $item['customer'];
				$array_consolidated[$item['customer_id']]['items'] = array( array('name' => $item['name'], 'vendor' => $item['vendor'], 'quantity' => $item['quantity'], 'total' => $item['total'], 'date' => $item['order_date_post']));
			}
		}
		//inspect($array_consolidated);
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

	$messages[] = $orders_html;
	$html = '<!doctype html>
				<html>
				    <head>
				        <style>
							*{-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;}
							body{font-family: sans-serif;line-height:1.6em;font-size:16pt}
							h1,h2{font-family: sans-serif;line-height:1.6em;font-weight:300}
							table { border-collapse:collapse; margin-top: 0;text-align: center; font-family: sans-serif;line-height:1.6em;font-size:9pt}
							td,th {padding: 0.5em;font-weight:normal}
							h2 {margin-bottom: 0;}
							
						</style>
			    	</head>
			    <body>';
	//$html .= '<h1 class="title" style="margin-left:.5in; margin-right:.5in;margin-top:.1in;margin-bottom:0in;">Weekly Orders for All Vendors</h1>';
	$html .= $orders_html;
	$html .= '</body></html>';
	$pdf_file = dirname(__FILE__) . '/../orders/order_' . $TIMESTAMP . '.pdf';

	$handle = fopen($pdf_file, 'w') or die('Cannot open file:  ' . $pdf_file);
	$pdf_url = get_stylesheet_directory_uri() . '/orders/order_' . $TIMESTAMP . '.pdf';

	// include ('pdf/mpdf.php');
	// $mpdf = new mPDF('', 'letter', 8, '', 0, 0, 16, 16, 9, 9, 'P');
	// $mpdf -> SetDisplayMode('fullwidth', 'continuous');
	//
	// $mpdf -> WriteHTML($html);
	// //$mpdf -> Output();
	//
	// //$messages[] = $mpdf -> Output('', 'S');
	//
	// fwrite($handle, $mpdf -> Output('', 'S'));
	// fclose($handle);

	//

	///////
	include ("mpdf60/mpdf.php");
	//$mpdf = new mPDF('', 'letter', 8, '', 0, 0, 16, 16, 9, 9, 'P');
	$mpdf = new mPDF('c');

	$mpdf -> WriteHTML($html);
	fwrite($handle, $mpdf -> Output('', 'S'));
	fclose($handle);

	////
	$response = json_encode(array('result' => TRUE, 'data' => $pdf_url, 'messages' => $messages));

	// } else {
	// $messages[] = 'no orders';
	// $response = json_encode(array('result' => FALSE, 'messages' => $messages));
	// }
	// response output
	header("Content-Type: application/json");
	echo $response;
	exit ;
}

add_action('wp_ajax_nopriv_print_orders', 'print_orders');
add_action('wp_ajax_print_orders', 'print_orders');
?>
