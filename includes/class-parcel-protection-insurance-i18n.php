<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wordpress.org
 * @since      1.0.0
 *
 * @package    Parcel_Protection_Insurance
 * @subpackage Parcel_Protection_Insurance/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Parcel_Protection_Insurance
 * @subpackage Parcel_Protection_Insurance/includes
 * @author     Logicrays <info@gmail.com>
 */
class Parcel_Protection_Insurance_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'parcel-protection-insurance',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
