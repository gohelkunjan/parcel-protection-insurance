<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wordpress.org
 * @since      1.0.0
 *
 * @package    Parcel_Protection_Insurance
 * @subpackage Parcel_Protection_Insurance/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Parcel_Protection_Insurance
 * @subpackage Parcel_Protection_Insurance/includes
 * @author     Logicrays <info@gmail.com>
 */
class Parcel_Protection_Insurance {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Parcel_Protection_Insurance_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PARCEL_PROTECTION_INSURANCE_VERSION' ) ) {
			$this->version = PARCEL_PROTECTION_INSURANCE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'parcel-protection-insurance';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Parcel_Protection_Insurance_Loader. Orchestrates the hooks of the plugin.
	 * - Parcel_Protection_Insurance_i18n. Defines internationalization functionality.
	 * - Parcel_Protection_Insurance_Admin. Defines all hooks for the admin area.
	 * - Parcel_Protection_Insurance_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-parcel-protection-insurance-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-parcel-protection-insurance-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-parcel-protection-insurance-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-parcel-protection-insurance-public.php';

		$this->loader = new Parcel_Protection_Insurance_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Parcel_Protection_Insurance_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Parcel_Protection_Insurance_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Parcel_Protection_Insurance_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_filter( 'plugin_action_links', $plugin_admin, 'parcel_protection_insurance_settings_link', 10, 2);
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'parcel_protection_insurance_settings_page', 99 );
		
		$this->loader->add_action( 'woocommerce_order_status_processing', $plugin_admin, 'parcel_protection_shipping_package', 10, 2 );
		$this->loader->add_action( 'woocommerce_order_status_completed', $plugin_admin, 'parcel_protection_shipping_package', 10, 2 );
		
		$this->loader->add_filter('gettext', $plugin_admin, 'parcel_protection_fees_strings_translation', 20, 3);
		$this->loader->add_filter( 'cron_schedules', $plugin_admin, 'parcel_protection_shipping_statusupdate_schedules' );
		$this->loader->add_action( 'parcel_protection_shipping_status_update', $plugin_admin, 'parcel_protection_shipping_status' );
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Parcel_Protection_Insurance_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'woocommerce_after_cart_table', $plugin_public, 'parcel_protection_insurance_checkbox' );
		$this->loader->add_action( 'woocommerce_review_order_before_payment', $plugin_public, 'parcel_protection_insurance_checkbox' );
		$this->loader->add_action( 'woocommerce_cart_calculate_fees', $plugin_public, 'parcel_protection_calculate_insurance' );
		$this->loader->add_action( 'wp_ajax_add_insurance_fee', $plugin_public, 'parcel_protection_insurance_cost' );
		$this->loader->add_action( 'wp_ajax_nopriv_add_insurance_fee', $plugin_public, 'parcel_protection_insurance_cost' );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'parcel_protection_insurance_modalpopup' );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'parcel_protection_insurance_script' );		
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Parcel_Protection_Insurance_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
