<?php
/*
 * Handles loading of classes and environment
 *
 * @author Schoenmann Guenter
 * @version 1.0.0.0
 */
$plugin_path = str_replace(basename( __FILE__),"",__FILE__);

require_once $plugin_path.'classes/woocommerce_additional_fees.php';
require_once $plugin_path.'classes/WC_calc_add_fee.php';

woocommerce_additional_fees::$show_activation = false;			//	true to show deactivation and uninstall checkbox
woocommerce_additional_fees::$show_uninstall = false;
woocommerce_additional_fees::$plugin_path = $plugin_path;
woocommerce_additional_fees::$plugin_url = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),'',plugin_basename(__FILE__));

	//	initialise, attach to hooks and to load textdomain
global $woocommerce_additional_fees, $ips_are_activation_hooks;
$woocommerce_additional_fees = new woocommerce_additional_fees();


if(is_admin())
{
	require_once $plugin_path.'classes/woocommerce_addons_add_fees.php';
	require_once $plugin_path.'classes/woocommerce_additional_fees_admin.php';
	require_once $plugin_path.'classes/panels/wc_panel_admin.php';

	if($ips_are_activation_hooks)
	{
		require_once $plugin_path.'classes/woocommerce_additional_fees_activation.php';
	}

	$obj = new woocommerce_additional_fees_admin();
}

?>