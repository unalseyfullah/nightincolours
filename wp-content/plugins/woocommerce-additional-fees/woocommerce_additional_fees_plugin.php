<?php
/*
Plugin Name: WooCommerce Additional Fees
Plugin URI: https://www.woothemes.com/products/payment-gateway-based-fees/ 
Description: This <a href="http://www.inoplugs.com" target="_blank">Additional Fees Plugin</a> provides a chance to add additional fees to an order automatically depending on the payment gateway. You may add the fee on product level and on total cart value. You may change the amount later on the order page or can add a fee manually on a manually created order.<br /> Email to <a href="mailto:support@inoplugs.com">support@inoplugs.com</a> with any questions.
Version: 1.0.1
Author: InoPlugs
Author URI: http://inoplugs.com
Text Domain: woocommerce_additional_fees
*/

/*  Copyright 2013  Inoplugs  (email : support@inoplugs.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), 'c634a2d133341d02fd2cbe7ee00e7fbe', '272217' );

/**
 * Check for activation, .... to speed up loading
 */
global $ips_are_activation_hooks;
$ips_are_activation_hooks = false;
if(is_admin())
{
	isset($_REQUEST['action']) ? $action = $_REQUEST['action'] : $action = '';

	switch ($action)
	{
		case 'activate':
		case 'deactivate':
		case 'delete-selected':
			$ips_are_activation_hooks = true;
			break;
		default:
			$ips_are_activation_hooks = false;
			break;
	}
}

if(is_woocommerce_active() || $ips_are_activation_hooks)
{
	$plugin_path = str_replace(basename( __FILE__),"",__FILE__);
	require_once $plugin_path.'woocommerce_additional_fees_loader.php';
}


/**
 * Register activation, deactivation, uninstall hooks
 * ==================================================
 *
 * See Documentation for WP 3.3.1
 */

global $ips_additional_fees_activation;
$ips_additional_fees_activation = null;

if(is_admin() && $ips_are_activation_hooks)
{
	$ips_additional_fees_activation = new woocommerce_additional_fees_activation();

	register_activation_hook(__FILE__, 'handler_woocommerce_additional_fees_activate' );
	register_deactivation_hook(__FILE__, 'handler_woocommerce_additional_fees_deactivate' );
	register_uninstall_hook(__FILE__, 'handler_woocommerce_additional_fees_uninstall' );

	function handler_woocommerce_additional_fees_activate()
	{
		global $ips_additional_fees_activation;
		$ips_additional_fees_activation->on_activate();
	}

	function handler_woocommerce_additional_fees_deactivate()
	{
		global $ips_additional_fees_activation;
		$ips_additional_fees_activation->on_deactivate();
	}

	function handler_woocommerce_additional_fees_uninstall()
	{
		global $ips_additional_fees_activation;
		$ips_additional_fees_activation->on_uninstall();
	}
}

?>
