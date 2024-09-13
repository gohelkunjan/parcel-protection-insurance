<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

$nonce_code = 'parcel-protection-insurance-settings';
$nonce = wp_create_nonce($nonce_code);

//Get the active tab from the $_GET param
$default_tab = null;
$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && wp_verify_nonce($_POST['nonce'], $nonce_code)) {


    if ($tab == 'checkout-configuration') {

        if (isset($_POST['checkout_enable'])) {
            update_option('checkout_enable', true);
        } else {
            delete_option('checkout_enable');
        }

        update_option('checkout_label', esc_attr($_POST['checkout_label']));
        update_option('checkout_tooltip', $_POST['checkout_tooltip']);
    } else if ($tab == 'salesforce') {

        update_option('auth_mode', esc_attr($_POST['auth_mode']));
        update_option('mode', esc_attr($_POST['mode']));
        update_option('debug_mode', esc_attr($_POST['debug_mode']));
        update_option('client_id', esc_attr($_POST['client_id']));
        update_option('client_secret', esc_attr($_POST['client_secret']));
        update_option('merchant_name', esc_attr($_POST['merchant_name']));
        update_option('username', esc_attr($_POST['username']));
        update_option('password', esc_attr($_POST['password']));
        update_option('security_token', esc_attr($_POST['security_token']));
        update_option('partner_id', esc_attr($_POST['partner_id']));
    } else {

        if (isset($_POST['insurance_enable'])) {
            update_option('insurance_enable', true);
        } else {
            delete_option('insurance_enable');
        }

        update_option('calculation_method', esc_attr($_POST['calculation_method']));
        update_option('standalone_insurance_frequency', esc_attr($_POST['standalone_insurance_frequency']));
        update_option('standalone_insurance_cost', esc_attr($_POST['standalone_insurance_cost']));

        $new = array();
        $insurance_frequency = !empty($_POST['insurance_frequency']) ? $_POST['insurance_frequency'] : null;
        $insurance_cost = !empty($_POST['insurance_cost']) ? $_POST['insurance_cost'] : null;
                
        $count = count($insurance_frequency);
        for ($i = 0; $i < $count; $i++) {
            if ($insurance_frequency[$i] != '') :
                $new[$i]['insurance_frequency'] = stripslashes(strip_tags($insurance_frequency[$i]));
                $new[$i]['insurance_cost'] = stripslashes($insurance_cost[$i]);
            endif;
        }
        update_option('dynamic_cost', $new);
        
    }
}
$calculation_method     = get_option('calculation_method');
$insurance_enable       = get_option('insurance_enable', false);
$checkout_enable        = get_option('checkout_enable', false);
$checkout_tooltip       = get_option('checkout_tooltip');
$auth_mode              = get_option('auth_mode');
$mode                   = get_option('mode');
$debug_mode             = get_option('debug_mode');
$dynamic_cost           = get_option('dynamic_cost');

?>
<div class="wrap parcel-protection">
    <h1><?php _e('Parcel Protection (Insurance)', 'parcel-protection-insurance'); ?></h1>
    <h2 class="nav-tab-wrapper">
        <a href="?page=parcel-protection-settings" class="nav-tab <?php if ($tab === null) : ?>nav-tab-active<?php endif; ?>"><?php _e('General Configuration', 'parcel-protection-insurance'); ?></a>
        <a href="?page=parcel-protection-settings&tab=checkout-configuration" class="nav-tab <?php if ($tab === 'checkout-configuration') : ?>nav-tab-active<?php endif; ?>"><?php _e('Checkout Configuration', 'parcel-protection-insurance'); ?></a>
        <a href="?page=parcel-protection-settings&tab=salesforce" class="nav-tab <?php if ($tab === 'salesforce') : ?>nav-tab-active<?php endif; ?>"><?php _e('SalesForce API Configuration', 'parcel-protection-insurance'); ?></a>
    </h2>
    <div class="tab-content">
        <form action="#" method="POST">
            <table class="form-table">
                <tbody>
                    <?php
                    switch ($tab):
                        case 'checkout-configuration':
                    ?>
                            <tr>
                                <th>
                                    <label><?php _e('Checkbox Default Checked', 'parcel-protection-insurance'); ?></label>
                                </th>
                                <td>
                                    <input type="checkbox" name="checkout_enable" <?php echo $checkout_enable ? 'checked' : ''; ?> />
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label><?php _e('Checkbox Label', 'parcel-protection-insurance'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="checkout_label" value="<?php echo get_option('checkout_label', __('Package protection', 'parcel-protection-insurance')); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label><?php _e('Checkbox Tooltip', 'parcel-protection-insurance'); ?></label>
                                </th>
                                <td>
                                    <?php
                                    echo wp_editor(stripslashes($checkout_tooltip), 'checkout_tooltip', array('textarea_name' => 'checkout_tooltip', 'media_buttons' => true, 'editor_height' => 150));
                                    ?>
                                </td>
                            </tr>
                        <?php
                            break;
                        case 'salesforce':
                        ?>
                            <tr>
                                <th>
                                    <label><?php _e('Auth Mode', 'parcel-protection-insurance'); ?></label>
                                </th>
                                <td>
                                    <select name="auth_mode">
                                        <option value="password" <?php echo selected('password', $auth_mode, false); ?>><?php _e('Password', 'parcel-protection-insurance'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label><?php _e('Client ID', 'parcel-protection-insurance'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="client_id" value="<?php echo get_option('client_id'); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label><?php _e('Client Secret', 'parcel-protection-insurance'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="client_secret" value="<?php echo get_option('client_secret'); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label><?php _e('Merchant Name', 'parcel-protection-insurance'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="merchant_name" value="<?php echo get_option('merchant_name'); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label><?php _e('Username', 'parcel-protection-insurance'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="username" value="<?php echo get_option('username'); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label><?php _e('Password', 'parcel-protection-insurance'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="password" value="<?php echo get_option('password'); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label><?php _e('Security Token', 'parcel-protection-insurance'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="security_token" value="<?php echo get_option('security_token'); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label><?php _e('Partner ID', 'parcel-protection-insurance'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="partner_id" value="<?php echo get_option('partner_id'); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label><?php _e('Mode', 'parcel-protection-insurance'); ?></label>
                                </th>
                                <td>
                                    <select name="mode">
                                        <option value="live" <?php echo selected('live', $mode, false); ?>><?php _e('Live', 'parcel-protection-insurance'); ?></option>
                                        <option value="sandbox" <?php echo selected('sandbox', $mode, false); ?>><?php _e('Sandbox', 'parcel-protection-insurance'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label><?php _e('Debug Mode Enable', 'parcel-protection-insurance'); ?></label>
                                </th>
                                <td>
                                    <select name="debug_mode">
                                        <option value="yes" <?php echo selected('yes', $debug_mode, false); ?>><?php _e('Yes', 'parcel-protection-insurance'); ?></option>
                                        <option value="no" <?php echo selected('no', $debug_mode, false); ?>><?php _e('No', 'parcel-protection-insurance'); ?></option>
                                    </select>
                                </td>
                            </tr>
                        <?php
                            break;
                        default:
                        ?>
                            <tr>
                                <th>
                                    <label><?php _e('Enable', 'parcel-protection-insurance'); ?></label>
                                </th>
                                <td>
                                    <input type="checkbox" name="insurance_enable" <?php echo $insurance_enable ? 'checked' : ''; ?> />
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label><?php _e('Calculation Method', 'parcel-protection-insurance'); ?></label>
                                </th>
                                <td>
                                    <select name="calculation_method" id="calculation_method">
                                        <option value="standalone" <?php echo selected('standalone', $calculation_method, false); ?>><?php _e('Standalone cost', 'parcel-protection-insurance'); ?></option>
                                        <option value="dynamic" <?php echo selected('dynamic', $calculation_method, false); ?>><?php _e('Dynamic cost', 'parcel-protection-insurance'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="standalone">
                                <th>
                                    <label><?php _e('Insurance Frequency (Per)', 'parcel-protection-insurance'); ?></label>
                                </th>
                                <td>
                                    <input type="number" id="standalone_insurance_frequency" name="standalone_insurance_frequency" value="<?php echo get_option('standalone_insurance_frequency'); ?>" required/>
                                </td>
                            </tr>
                            <tr class="standalone">
                                <th>
                                    <label><?php _e('Insurance Cost (Per Frequency)', 'parcel-protection-insurance'); ?></label>
                                </th>
                                <td>
                                    <input type="number" id="standalone_insurance_cost" name="standalone_insurance_cost" value="<?php echo get_option('standalone_insurance_cost'); ?>" required/>
                                </td>
                            </tr>
                            <tr class="dynamic">
                                <th>
                                    <label><?php _e('Dynamic Cost', 'parcel-protection-insurance'); ?></label>
                                </th>
                                <td>
                                    <table class="form-table dynamic-table-content repeater-text-fields-table">
                                        <thead>
                                        <tr class="dynamic-heading">
                                                <th><?php _e('Insurance Frequency (Per)', 'parcel-protection-insurance'); ?></th>
                                                <th><?php _e('Insurance Cost (Per Frequency)', 'parcel-protection-insurance'); ?></th>
                                                <th><?php _e('Action', 'parcel-protection-insurance'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="repeater-text-fields-wrapper">                                             
                                            <?php
                                            if ($dynamic_cost) {
                                                foreach ($dynamic_cost as $value) {
                                                    echo '<tr><td><input type="number" id="insurance_frequency" name="insurance_frequency[]" value="' . $value['insurance_frequency'] . '"></td><td><input type="number" id="insurance_cost" name="insurance_cost[]" value="' . $value['insurance_cost'] . '"></td><td><button class="remove-repeater-text-field button-secondary">Remove</button></td></tr>';
                                                }
                                            } else {
                                                echo '<tr><td><input type="number" id="insurance_frequency" name="insurance_frequency[]" value=""></td><td><input type="number" id="insurance_cost" name="insurance_cost[]" value=""></td><td><button class="remove-repeater-text-field button-secondary">Remove</button></td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    <a class="add-repeater-text-field button-primary">Add</a>
                                </td>
                            </tr>
                    <?php
                            break;
                    endswitch;
                    ?>
                </tbody>
            </table>
            <p class="submit">
            <input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
            <input type="submit" class="button button-primary btn" value="<?php _e('Save Config', 'parcel-protection-insurance'); ?>" />
            </p>
        </form>
    </div>
</div>