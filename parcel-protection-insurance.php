<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wordpress.org
 * @since             1.0.0
 * @package           Parcel_Protection_Insurance
 *
 * @wordpress-plugin
 * Plugin Name:       Parcel Protection (Insurance)
 * Plugin URI:        https://wordpress.org
 * Description:       <strong>Admin configuration:</strong> WooCommerce > Parcel Protection (Insurance)
 * Version:           1.0.0
 * Author:            Logicrays
 * Author URI:        https://wordpress.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       parcel-protection-insurance
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('PARCEL_PROTECTION_INSURANCE_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-parcel-protection-insurance-activator.php
 */
function activate_parcel_protection_insurance()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-parcel-protection-insurance-activator.php';
	Parcel_Protection_Insurance_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-parcel-protection-insurance-deactivator.php
 */
function deactivate_parcel_protection_insurance()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-parcel-protection-insurance-deactivator.php';
	Parcel_Protection_Insurance_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_parcel_protection_insurance');
register_deactivation_hook(__FILE__, 'deactivate_parcel_protection_insurance');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-parcel-protection-insurance.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_parcel_protection_insurance()
{

	$plugin = new Parcel_Protection_Insurance();
	$plugin->run();
}
run_parcel_protection_insurance();
