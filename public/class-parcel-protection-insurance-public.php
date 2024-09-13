<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wordpress.org
 * @since      1.0.0
 *
 * @package    Parcel_Protection_Insurance
 * @subpackage Parcel_Protection_Insurance/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Parcel_Protection_Insurance
 * @subpackage Parcel_Protection_Insurance/public
 * @author     Logicrays <info@gmail.com>
 */
class Parcel_Protection_Insurance_Public
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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/parcel-protection-insurance-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/parcel-protection-insurance-public.js', array('jquery'), $this->version, false);
	}
	/**
	 * Calculation of standalone nad dynamic cost value
	 *
	 * @return void
	 */
	public function parcel_protection_get_insurance_amount()
	{
		global $woocommerce;

		$method = get_option('calculation_method');
		$subtotal = $woocommerce->cart->cart_contents_total;

		if ($method == 'dynamic') {
			$frequencies = get_option('dynamic_cost');

			$cost = 0;
			if (!empty($frequencies)) {
				foreach ($frequencies as $k => $v) {
					$diff[abs($v['insurance_frequency'] - $subtotal)] = $k;
				}
				ksort($diff, SORT_NUMERIC);
				$closest_key = current($diff);
				$frequency = $frequencies[$closest_key]['insurance_frequency'];
				$costPerFrequency = $frequencies[$closest_key]['insurance_cost'];
				$cost = ceil($subtotal / $frequency) * $costPerFrequency;
			}
		} else {
			$frequency = get_option('standalone_insurance_frequency');
			$costPerFrequency = get_option('standalone_insurance_cost');
			$cost = ceil($subtotal / $frequency) * $costPerFrequency;
		}

		return $cost;
	}
	/**
	 * Display parcel protection content in cart and checkout page
	 *
	 * @return void
	 */
	public function parcel_protection_insurance_checkbox()
	{
		global $woocommerce;

		if (get_option('insurance_enable')) {

			$insurance_cost = $this->parcel_protection_get_insurance_amount();
		?>
			<div class="parcel-protection-get-insurance-amount insurance-wrapper">
				<div class="parcel-content">
					<div class="precent-content-left">
						<div class="percent-title">
							<strong><?php if (get_option('checkout_label')) { echo get_option('checkout_label'); } ?></strong>
						</div>
						<img src="<?php echo plugin_dir_url(__FILE__); ?>images/parcel.jpeg" alt="Parcel protection">
						<span><?php _e('Against loss, theft, or damage in transit with instant resolution.', 'parcel-protection-insurance'); ?></span>
						<?php if (get_option('checkout_tooltip')) { ?>
							<a class="modal-toggle parcel-toggle"><img src="<?php echo plugin_dir_url(__FILE__); ?>images/help.png" alt="help"></a>
						<?php } ?>
					</div>
					<div class="precent-content-right">
						<div class="switch">
							<input type="checkbox" class="insurancefee" name="insurance_fee" value="1" <?php if ($this->isChecked()) { echo "checked='checked'";} ?> <?php if ( is_checkout()) { echo "disabled='disabled'"; } ?> />
							<div class="slider round"></div>
						</div>
						<span class="precent-content-price">
							<?php echo get_woocommerce_currency_symbol(); ?>
							<?php echo $insurance_cost; ?>
						</span>
					</div>
				</div>
			</div>
		<?php
		}
	}
	/**
	 * Parcel protection insurance modal popup
	 *
	 * @return void
	 */
	public function parcel_protection_insurance_modalpopup(){
		if (get_option('checkout_tooltip')) {
		?>
		<div class="parcel-protection-help">
			<div class="modal">
				<div class="modal-overlay modal-toggle parcel-toggle"></div>
				<div class="modal-wrapper modal-transition">
					<div class="modal-header">
						<button class="modal-close modal-toggle parcel-toggle">&times;</button>
						<h2 class="modal-heading"><?php if (get_option('checkout_label')) { echo get_option('checkout_label'); } ?></h2>
					</div>
					<div class="modal-body">
						<div class="modal-content">
							<?php if (get_option('checkout_tooltip')) {
								echo stripslashes(get_option('checkout_tooltip'));
							} ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		}
	}
	/**
	 * Checked default checkbox
	 *
	 * @return boolean
	 */
	public function isChecked()
	{
		global $woocommerce;
		
		$defautChecked = get_option('checkout_enable');
		$checkoutChecked = $woocommerce->session->get('insurance_fee');

		if ($checkoutChecked === 1){
			return 1;
		}
		if ($checkoutChecked === 0){
			return 0;
		}
		if ($defautChecked){
			return 1;
		}

		return 0;
	}
	/**
	 * Ajax for store insurance value
	 *
	 * @return void
	 */
	public function parcel_protection_insurance_script()
	{
		global $woocommerce;
		if (is_wc_endpoint_url('order-received') && $woocommerce->session->__isset('insurance_fee')) :

			$woocommerce->session->__unset('insurance_fee');

		// On Cart page: jQuert script
		elseif (is_cart()) :
		?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					if (typeof woocommerce_params === 'undefined')
						return false;

					$(document.body).on('click change', 'input[name=insurance_fee]', function() {

						var fee = $(this).prop('checked') === true ? '1' : '';

						$.ajax({
							type: 'POST',
							url: woocommerce_params.ajax_url,
							data: {
								'action': 'add_insurance_fee',
								'insurance_fee': fee,
							},
							success: function(result) {
								setTimeout(function() {
									$(document.body).trigger('added_to_cart');
								}, 500);
							},
						});
					});
				});
			</script>
		<?php
		endif;		
	}
	/**
	 * Store insurance cost in session
	 *
	 * @return void
	 */
	public function parcel_protection_insurance_cost()
	{
		global $woocommerce;

		if (isset($_POST['insurance_fee'])) {
			$woocommerce->session->set('insurance_fee', ($_POST['insurance_fee'] ? 1 : 0));
		}
		wp_die();
	}
	/**
	 * Get insurance cost and add to cart
	 *
	 * @return void
	 */
	public function parcel_protection_calculate_insurance()
	{
		global $woocommerce;

		if (is_admin() && !defined('DOING_AJAX'))
			return;

		$insurance_cost = $this->parcel_protection_get_insurance_amount();

		if($this->isChecked() && get_option('insurance_enable')) {
			$woocommerce->cart->add_fee('Insurance',  $insurance_cost);
		}
	}	
}
