<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordpress.org
 * @since      1.0.0
 *
 * @package    Parcel_Protection_Insurance
 * @subpackage Parcel_Protection_Insurance/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Parcel_Protection_Insurance
 * @subpackage Parcel_Protection_Insurance/admin
 * @author     Logicrays <info@gmail.com>
 */
class Parcel_Protection_Insurance_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Parcel_Protection_Insurance_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Parcel_Protection_Insurance_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/parcel-protection-insurance-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Parcel_Protection_Insurance_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Parcel_Protection_Insurance_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/parcel-protection-insurance-admin.js', array('jquery'), $this->version, false);
	}
	/**
	 * Plugin setting page link
	 *
	 * @param [type] $links
	 * @return void
	 */
	public function parcel_protection_insurance_settings_link($links, $plugin_file)
	{
		if (basename($plugin_file) === 'parcel-protection-insurance.php') {
			$settings_link = '<a href="admin.php?page=parcel-protection-settings">' . __('Settings', 'parcel-protection-insurance') . '</a>';
			array_unshift($links, $settings_link);
		}
		return $links;
	}
	/**
	 * Parcel protection setting menu
	 *
	 * @return void
	 */
	public function parcel_protection_insurance_settings_page()
	{
		add_submenu_page(
			'woocommerce',
			__('Parcel Protection (Insurance)', 'parcel-protection-insurance'),
			__('Parcel Protection (Insurance)', 'parcel-protection-insurance'),
			'manage_options',
			'parcel-protection-settings',
			array($this, 'parcel_protection_render_settings')
		);
	}
	/**
	 * Parcel protection setting page
	 *
	 * @return void
	 */
	public function parcel_protection_render_settings()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/parcel-protection-insurance-settings.php';
	}

	public function parcel_protection_log($message)
	{

		$upload_dir = wp_upload_dir();
		$debug_mode = get_option('debug_mode');

		if (!empty($upload_dir['basedir']) && $debug_mode == 'yes') {

			$filename = $upload_dir['basedir'] . '/' . sanitize_file_name('[parcel-protection],.log');
			$responsetxt = $message . "\n";
			if (file_exists($filename)) {
				$handle = fopen($filename, 'a') or die('Cannot open file:  ' . $filename);
				fwrite($handle, $responsetxt);
			} else {
				$handle = fopen($filename, "w") or die("Unable to open file!");
				fwrite($handle, $responsetxt);
			}
			fclose($handle);
		}
	}
	/**
	 * API authentication
	 *
	 * @return array|bool
	 */
	public function parcel_protection_authenticate()
	{

		if (get_option('mode') == 'sandbox') {
			$endpoint_url = 'https://test.salesforce.com/services/oauth2/token';
		}

		if (get_option('mode') == 'live') {
			$endpoint_url = 'https://login.salesforce.com/services/oauth2/token';
		}

		$grant_type = get_option('auth_mode');
		$client_id = get_option('client_id');
		$client_secret = get_option('client_secret');
		$username = get_option('username');
		$password = get_option('password');
		$security_token = $password . get_option('security_token');

		$params = array(
			'grant_type' => $grant_type,
			'client_id' => $client_id,
			'client_secret' => $client_secret,
			'username' => $username,
			'password' => $password,
			'Securitytoken' => $security_token
		);

		$response = wp_remote_post(
			esc_url_raw($endpoint_url),
			array(
				'method' => 'POST',
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => [],
				'body' => $params,
			)
		);

		if (is_wp_error($response)) {
			$error_message = "[" . date_i18n('Y-m-d H:i', current_time('timestamp')) . "] Parcel_Protection_Log.INFO: Error = " .$response->get_error_message();
			$this->parcel_protection_log($error_message);
		} else {
			$api_array = json_decode(wp_remote_retrieve_body($response));
		}

		return $api_array;
	}
	/**
	 * Insert package api call
	 *
	 * @return array
	 */
	public function parcel_protection_shipping_package($order_id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'salesforce_shipping_info';

		$insurance_cost = "";
		$order 			= wc_get_order($order_id);
		$currency 		= $order->get_currency();

		$types = array('line_item', 'fee', 'shipping', 'coupon');

		foreach ($order->get_items($types) as $item_id => $item) {
			if ($item->is_type('fee')) {
				$insurance_cost = wc_get_order_item_meta($item_id, '_fee_amount', true);
			}
		}

		if (empty($order->get_items('shipping'))) {

			$error_message = "[" . date_i18n('Y-m-d H:i', current_time('timestamp')) . "] Parcel_Protection_Log.INFO: Oder Id:" . esc_html($order_id) . " - Shipping method not available";
			$this->parcel_protection_log($error_message);
		} else {

			$tracking_number = "";
			$carrier_name = "";
			$shipping_date = "";
			$tracking_items = get_post_meta($order_id, '_wc_shipment_tracking_items', true);

			if ($tracking_items && is_array($tracking_items)) {

				foreach ($tracking_items as $tracking_item) {
					$tracking_number 	= $tracking_item['tracking_number'];
					$carrier_name 		= $tracking_item['tracking_provider'];
					$shipping_date 		= $tracking_item['date_shipped'];
				}

				foreach ($order->get_items('shipping') as $item_id => $item) {

					$item_data = $item->get_data();
					$shipping_data_id = $item_data['id'];
					$shipment = $wpdb->get_results("SELECT shipment_id FROM $table_name WHERE shipment_id = '" . $shipping_data_id . "'");
					$shipment_id = $shipment[0]->shipment_id;

					if ($shipment_id) {
						$data = array(
							'order_id' => $order_id,
							'shipment_id' => $shipping_data_id,
							'amount' => $insurance_cost,
							'currency' => $currency,
							'status' => 0,
							'updated_date' => date_i18n('Y-m-d H:i', current_time('timestamp')),
						);
						$where = array('shipment_id' => $shipment_id);
						$wpdb->update($table_name, $data, $where);
					} else {
						$wpdb->insert($table_name, array(
							'order_id' => $order_id,
							'shipment_id' => $shipping_data_id,
							'amount' => $insurance_cost,
							'currency' => $currency,
							'status' => 0,
							'created_date' => date_i18n('Y-m-d H:i', strtotime($shipping_date)),
							'updated_date' => date_i18n('Y-m-d H:i', current_time('timestamp')),
						));
					}
				}
			}
		}
	}
	/**
	 * Shipping status update schedule
	 */
	public function parcel_protection_shipping_statusupdate_schedules()
	{
		if (!isset($schedules["1min"])) {
			$schedules["1min"] = array(
				'interval' => 60,
				'display' => __('Once every 1 minute')
			);
		}

		return $schedules;
	}
	/**
	 * Shipping status update in database.
	 *
	 */
	public function parcel_protection_shipping_status()
	{
		$startcronmessage = "[" . date_i18n('Y-m-d H:i', current_time('timestamp')) . "] Parcel_Protection_Log.INFO: CRON process start";
		$this->parcel_protection_log($startcronmessage);

		global $wpdb;		

		$tracking_number = "";
		$carrier_name = "";
		$shipping_date = "";

		$store_raw_country = get_option('woocommerce_default_country');
		// Split the country/state
		$split_country = explode(":", $store_raw_country);
		// Country and state separated:
		$store_country = $split_country[0];
		$store_state   = $split_country[1];

		$table_name = $wpdb->prefix . 'salesforce_shipping_info';
		$results = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 0");

		foreach ($results as $result) {

			$order = wc_get_order($result->order_id);
			$tracking_items = get_post_meta($result->order_id, '_wc_shipment_tracking_items', true);

			$shipmentmessage = "[" . date_i18n('Y-m-d H:i', current_time('timestamp')) . "] Parcel_Protection_Log.INFO: Process shipment = " .json_encode($tracking_items);
			$this->parcel_protection_log($shipmentmessage);

			if ($tracking_items && is_array($tracking_items)) {
				foreach ($tracking_items as $tracking_item) {
					$tracking_number 	= $tracking_item['tracking_number'];
					$carrier_name 		= $tracking_item['tracking_provider'];
					$shipping_date 		= $tracking_item['date_shipped'];
				}
			}

			$params = array(
				"Partner_Id__c" => get_option('partner_id'),
				"Carrier__c" => $carrier_name,
				"Ship_Date__c" => date_i18n('Y-m-d', strtotime($shipping_date)),
				"Tracking_Number__c" => $tracking_number,
				"Package_Value__c" => $order->get_total(),
				"Insurance_Cost__c" => $result->amount,
				"Invoice_Number__c" => $result->order_id,
				"Merchant_Name__c" => get_option('merchant_name'),
				"Merchant_State__c" => $store_state,
				"Buyer_Country__c" => $order->get_shipping_country(),
				"Buyers_Address__c" => $order->get_shipping_address_1() . ',' . $order->get_shipping_address_2() . ',' . $order->get_shipping_city() . ',' . $order->get_shipping_postcode(),
				"Buyers_Name__c" => $order->get_shipping_first_name() . " " . $order->get_shipping_last_name(),
				"Status__c" => "New"
			);

			$authenticate = $this->parcel_protection_authenticate();
			$api_url = $authenticate->instance_url . '/services/data/v51.0/sobjects/Staging_Package__c/';
			$access_token = $authenticate->access_token;
			$token_type = $authenticate->token_type;
			$authentication = $token_type . ' ' . $access_token;

			$authenticatemessage = "[" . date_i18n('Y-m-d H:i', current_time('timestamp')) . "] Parcel_Protection_Log.INFO: Authentication Success = " .json_encode($authenticate);
			$this->parcel_protection_log($authenticatemessage);

			$apiparams = "[" . date_i18n('Y-m-d H:i', current_time('timestamp')) . "] Parcel_Protection_Log.INFO: API params = " .json_encode($params);
			$this->parcel_protection_log($apiparams);

			$response = wp_remote_post(
				esc_url_raw($api_url),
				array(
					'method' => 'POST',
					'timeout' => 45,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking' => true,
					'headers' => array(
						'Authorization' => $authentication,
						'Content-Type' => 'application/json',
					),
					'body' => json_encode($params),
				)
			);
			if (is_wp_error($response)) {

				$saleforceapi_error = "[" . date_i18n('Y-m-d H:i', current_time('timestamp')) . "] Parcel_Protection_Log.INFO: Error = " .$response->get_error_message();
				$this->parcel_protection_log($saleforceapi_error);

			}else{

				$saleforceapi_response = "[" . date_i18n('Y-m-d H:i', current_time('timestamp')) . "] Parcel_Protection_Log.INFO: Data sent successfully = " .wp_remote_retrieve_body($response);
				$this->parcel_protection_log($saleforceapi_response);

				$data = array(
					'status' => 1,
					'updated_date' => date_i18n('Y-m-d H:i', current_time('timestamp')),
				);
				$where = array('status' => $result->status);
				$wpdb->update($table_name, $data, $where);

				$endcronmessage = "[" . date_i18n('Y-m-d H:i', current_time('timestamp')) . "] Parcel_Protection_Log.INFO: CRON process finish";
				$this->parcel_protection_log($endcronmessage);
			}
		}
	}
	/**
	 * Fees text change to insurance
	 *
	 * @param [type] $translated_text
	 * @param [type] $text
	 * @param [type] $domain
	 * @return $translated_text
	 */
	public function parcel_protection_fees_strings_translation($translated_text, $text, $domain)
	{
		global $pagenow, $typenow;

		// Settings
		$current_text = "Fees:";
		$new_text     = "Insurance:";

		// Targeting admin single order pages
		if (is_admin() && in_array($pagenow, ['post.php', 'post-new.php']) && 'shop_order' === $typenow && $current_text === $text) {
			$translated_text =  __($new_text, $domain);
		}
		return $translated_text;
	}
}
