<?php
/*
	Plugin Name: PixelYourSite
	Description: Add the Facebook Pixel code into your Wordpress site and set up standard events with just a few clicks. Fully compatible with Woocommerce, purchase event included.
	Plugin URI: http://www.pixelyoursite.com/facebook-pixel-plugin-help
	Author: PixelYourSite
	Author URI: http://www.pixelyoursite.com
	Version: 5.0.0
	License: GPLv3
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'PYS_FREE_VERSION', '5.0.0' );

require_once( 'inc/admin_notices.php' );
require_once( 'inc/common.php' );
require_once( 'inc/common-edd.php' );
require_once( 'inc/core.php' );
require_once( 'inc/core-edd.php' );
require_once( 'inc/ajax-standard.php' );

add_action( 'plugins_loaded', 'pys_free_init' );
function pys_free_init() {

	$options = get_option( 'pixel_your_site' );
	if ( ! $options || ! isset( $options['general']['pixel_id'] ) || empty( $options['general']['pixel_id'] ) ) {
		pys_initialize_settings();
	}

	if ( is_admin() || pys_get_option( 'general', 'enabled' ) == false || pys_is_disabled_for_role() || ! pys_get_option( 'general', 'pixel_id' ) ) {
		return;
	}

	add_action( 'wp_enqueue_scripts', 'pys_public_scripts' );
	add_action( 'wp_head', 'pys_head_comments', 10 );

	/**
	 * Hooks call priority:
	 * wp_head:
	 * 1 - pixel events options - PRO only;
	 * 2 - init event;
	 * 3 - evaluate events;
	 * 4 - output events;
	 * 9 (20) - enqueue public scripts (head/footer);
	 * wp_footer
	 */

	add_action( 'wp_head', 'pys_pixel_init_event', 2 );

	add_action( 'wp_head', 'pys_page_view_event', 3 );
	add_action( 'wp_head', 'pys_general_event', 3 );
	add_action( 'wp_head', 'pys_search_event', 3 );
	add_action( 'wp_head', 'pys_standard_events', 3 );
	add_action( 'wp_head', 'pys_woocommerce_events', 3 );
	add_action( 'wp_head', 'pys_edd_events', 3 );

	add_action( 'wp_head', 'pys_output_js_events_code', 4 );
	add_action( 'wp_head', 'pys_output_custom_events_code', 4 );

	add_action( 'wp_footer', 'pys_output_noscript_code', 10 );
	add_action( 'wp_footer', 'pys_output_woo_ajax_events_code', 10 );
	add_action( 'wp_footer', 'pys_output_edd_ajax_events_code', 10 );

	// add add_to_cart ajax support only if woocommerce installed and events enabled
	if ( pys_get_option( 'woo', 'enabled' ) && ( pys_get_option( 'woo', 'on_add_to_cart_btn' ) || pys_get_option( 'woo', 'on_thank_you_page' ) ) ) {

		add_filter( 'woocommerce_loop_add_to_cart_link', 'pys_add_code_to_woo_cart_link', 10, 2 );

	}

	## add pixel code to EDD add_to_cart buttons
	if ( pys_get_option( 'edd', 'enabled' ) && pys_get_option( 'edd', 'on_add_to_cart_btn', false ) ) {

		add_filter( 'edd_purchase_link_args', 'pys_edd_purchase_link_args', 10, 1 );

	}

	add_filter( 'pys_event_params', 'pys_add_domain_param', 10, 2 );

}

if ( ! function_exists( 'pys_admin_menu' ) ) {

	function pys_admin_menu() {

		if ( false == current_user_can( 'manage_options' ) ) {
			return;
		}

		add_menu_page( 'PixelYourSite', 'PixelYourSite', 'manage_options', 'pixel-your-site', 'pys_admin_page_callback', plugins_url( 'pixelyoursite/img/favicon.png' ) );

	}

	add_action( 'admin_menu', 'pys_admin_menu' );

}

if ( ! function_exists( 'pys_restrict_admin_pages' ) ) {

	function pys_restrict_admin_pages() {

		$screen = get_current_screen();

		if ( $screen->id == 'toplevel_page_pixel-your-site' & false == current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Sorry, you are not allowed to access this page.' ) );
		}

	}

	add_action( 'current_screen', 'pys_restrict_admin_pages' );

}

if ( ! function_exists( 'pys_admin_page_callback' ) ) {

	function pys_admin_page_callback() {

		## update plugin options
		if ( ! empty( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'pys_update_options' ) && isset( $_POST['pys'] ) ) {
			update_option( 'pixel_your_site', $_POST['pys'] );
		}

		## delete standard events
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'pys_delete_events'
			&& isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'pys_delete_events' )
			&& isset( $_GET['events_ids'] ) && isset( $_GET['events_type'] )
		) {

			pys_delete_events( $_GET['events_ids'], $_GET['events_type'] );

			$redirect_to = add_query_arg(
				array(
					'page'       => 'pixel-your-site',
					'active_tab' => $_GET['events_type'] == 'standard' ? 'posts-events' : 'dynamic-events',
				),
				admin_url( 'admin.php' )
			);

			wp_safe_redirect( $redirect_to );

		}

		include( 'inc/html-admin.php' );

	}

}

if ( ! function_exists( 'pys_admin_scripts' ) ) {

	add_action( 'admin_enqueue_scripts', 'pys_admin_scripts' );
	function pys_admin_scripts() {

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'pixel-your-site' ) {

			add_thickbox();

			wp_enqueue_style( 'pys', plugins_url( 'css/admin.css', __FILE__ ), array(), PYS_FREE_VERSION );
			wp_enqueue_script( 'pys', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), PYS_FREE_VERSION );

		}

	}

}

if ( ! function_exists( 'pys_public_scripts' ) ) {

	function pys_public_scripts() {

		$in_footer = (bool) pys_get_option( 'general', 'in_footer', false );

		wp_enqueue_script( 'pys', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery' ), PYS_FREE_VERSION, $in_footer );

	}

}

if ( ! function_exists( 'pys_free_plugin_activated' ) ) {

	register_activation_hook( __FILE__, 'pys_free_plugin_activated' );
	function pys_free_plugin_activated() {

		if ( false == is_admin() || false == current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if ( is_plugin_active( 'pixelyoursite-pro/pixelyoursite-pro.php' ) ) {
			wp_die( 'Please deactivate PixelYourSite Pro version First.', 'Plugin Activation' );
		}

		$options = get_option( 'pixel_your_site' );
		if ( ! $options || ! isset( $options['general']['pixel_id'] ) || empty( $options['general']['pixel_id'] ) ) {
			pys_initialize_settings();
		}

	}

}

if ( ! function_exists( 'pys_initialize_settings' ) ) {

	function pys_initialize_settings() {

		if ( false == current_user_can( 'manage_options' ) ) {
			return;
		}

		// set default options values
		$defaults = pys_get_default_options();
		update_option( 'pixel_your_site', $defaults );

		// migrate settings from old versions
		if ( get_option( 'woofp_admin_settings' ) ) {

			require_once( 'inc/migrate.php' );
			pys_migrate_from_22x();

		}

	}

}