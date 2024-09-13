<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wordpress.org
 * @since      1.0.0
 *
 * @package    Parcel_Protection_Insurance
 * @subpackage Parcel_Protection_Insurance/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Parcel_Protection_Insurance
 * @subpackage Parcel_Protection_Insurance/includes
 * @author     Logicrays <info@gmail.com>
 */
class Parcel_Protection_Insurance_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		// Require parent plugin
		if (!is_plugin_active('woocommerce/woocommerce.php') && current_user_can('activate_plugins')) {
			// Stop activation redirect and show error
			wp_die('Sorry, but this plugin require the <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a> plugin to be installed and active. <br><a href="' . admin_url('plugins.php') . '">&laquo; Return to Plugins</a>');
		}

		if (!is_plugin_active('woo-advanced-shipment-tracking/woocommerce-advanced-shipment-tracking.php') && current_user_can('activate_plugins')) {
			// Stop activation redirect and show error
			wp_die('Sorry, but this plugin require <a href="https://wordpress.org/plugins/woo-advanced-shipment-tracking/" target="_blank">Advanced Shipment Tracking for WooCommerce</a> plugin to be installed and active. <br><a href="' . admin_url('plugins.php') . '">&laquo; Return to Plugins</a>');
		}

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'salesforce_shipping_info';

		$sql = "CREATE TABLE $table_name (
		id bigint NOT NULL AUTO_INCREMENT,
		order_id bigint NOT NULL,
		shipment_id bigint NOT NULL,		
		amount varchar(255) NOT NULL,
		currency varchar(255) NOT NULL,
		status varchar(100) NOT NULL,
		created_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		updated_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		PRIMARY KEY  (id)
		) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		if (!wp_next_scheduled('parcel_protection_shipping_status_update')) {
			wp_schedule_event( time(), '1min', 'parcel_protection_shipping_status_update' );
		}
	}
}
