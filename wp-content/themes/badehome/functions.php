<?php

function badeh_theme_setup() {
    load_child_theme_textdomain( 'badehome', get_stylesheet_directory() );
}

add_action('after_setup_theme', 'badeh_theme_setup');

if ( ! is_admin() ) { add_action( 'wp_enqueue_scripts', 'badeh_load_frontend_css', 20 ); }

if ( ! function_exists( 'badeh_load_frontend_css' ) ) {
	function badeh_load_frontend_css () {
        wp_enqueue_style( 'open_sans', 'http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,800,700&subset=latin,latin-ext' );
		wp_deregister_style( 'woo-layout' );
		wp_register_style( 'woo-layout', get_stylesheet_directory_uri() . '/css/layout.css' );
		wp_enqueue_style( 'woo-layout' );
        wp_register_style( 'woocommerce', get_stylesheet_directory_uri() . '/css/woocommerce.css' );
        wp_enqueue_style( 'woocommerce' );

	} // End woo_load_frontend_css()
}

function badeh_move_price() {
    // add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
    remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_price', 12 );
    add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_price', 15 );
}

add_action('after_setup_theme', 'badeh_move_price');


$site_phone = new site_phone();

class site_phone {
    function site_phone( ) {
        add_filter( 'admin_init' , array( &$this , 'register_fields' ) );
    }
    function register_fields() {
        register_setting( 'general', 'site_phone', 'esc_attr' );
        add_settings_field('site_phone', '<label for="site_phone">'.__('Telefón v hlavičke').'</label>' , array(&$this, 'fields_html') , 'general' );
    }
    function fields_html() {
        $value = get_option( 'site_phone', '' );
        echo '<input type="text" id="site_phone" name="site_phone" value="' . $value . '" />';
    }
}


$site_mail = new site_mail();

class site_mail {
    function site_mail( ) {
        add_filter( 'admin_init' , array( &$this , 'register_fields' ) );
    }
    function register_fields() {
        register_setting( 'general', 'site_mail', 'esc_attr' );
        add_settings_field('site_mail', '<label for="site_mail">'.__('Email v hlavičke').'</label>' , array(&$this, 'fields_html') , 'general' );
    }
    function fields_html() {
        $value = get_option( 'site_mail', '' );
        echo '<input type="text" id="site_mail" name="site_mail" value="' . $value . '" />';
    }
}

$site_addr = new site_addr();

class site_addr {
    function site_addr( ) {
        add_filter( 'admin_init' , array( &$this , 'register_fields' ) );
    }
    function register_fields() {
        register_setting( 'general', 'site_addr', 'esc_attr' );
        add_settings_field('site_addr', '<label for="site_addr">'.__('Adresa v hlavičke').'</label>' , array(&$this, 'fields_html') , 'general' );
    }
    function fields_html() {
        $value = get_option( 'site_addr', '' );
        echo '<input type="text" id="site_addr" name="site_addr" value="' . $value . '" />';
    }
}

$site_addr_link = new site_addr_link();

class site_addr_link {
    function site_addr_link( ) {
        add_filter( 'admin_init' , array( &$this , 'register_fields' ) );
    }
    function register_fields() {
        register_setting( 'general', 'site_addr_link', 'esc_attr' );
        add_settings_field('site_addr_link', '<label for="site_addr_link">'.__('Odkaz na adresu v hlavičke').'</label>' , array(&$this, 'fields_html') , 'general' );
    }
    function fields_html() {
        $value = get_option( 'site_addr_link', '' );
        echo '<input type="text" id="site_addr_link" name="site_addr_link" value="' . $value . '" />';
    }
}

$site_terms = new site_terms();

class site_terms {
    function site_terms( ) {
        add_filter( 'admin_init' , array( &$this , 'register_fields' ) );
    }
    function register_fields() {
        register_setting( 'general', 'site_terms', 'esc_attr' );
        add_settings_field('site_terms', '<label for="site_terms">'.__('Odkaz na obchodné podmienky v hlavičke').'</label>' , array(&$this, 'fields_html') , 'general' );
    }
    function fields_html() {
        $value = get_option( 'site_terms', '' );
        echo '<input type="text" id="site_terms" name="site_terms" value="' . $value . '" />';
    }
}

add_action( 'woo_header_before', 'header_info' );

function header_info() {
	$phone = get_option( 'site_phone', '' );
	$addr = get_option( 'site_addr', '' );
	$addr_link = get_option( 'site_addr_link', '' );
	$mail = get_option( 'site_mail', '' );
	$terms = get_option( 'site_terms', '' );
	echo '<div class="top"><div class="col-full"><ul>
		<li class="phone top_lt"><a href="tel:'.$phone.'">'.$phone.'</a> </li>
		<li class="mail"><a href="mailto:'.$mail.'">'.$mail.'</a></li>
		<li class="addr top_lt"><a href="'.$addr_link.'">'.$addr.'</a></li>
		<li class="terms top_lt"><a href="'.$terms.'">obchodné podmienky</a></li></ul></div></div>';
}

require_once(get_template_directory().'/functions.php');
remove_action( 'woo_nav_before', 'superstore_user', 40 );

add_action( 'woo_nav_before', 'login_user', 40 );

function login_user(){

	echo '<div class="account">';
		if(!is_user_logged_in()){
			echo '<a href="' . get_permalink( get_option('woocommerce_myaccount_page_id') ) . '" title="' . __('Log in','woocommerce') . '">' . __('Log in','woocommerce') .'</a>';
            echo '<a href="' . get_permalink( get_option('woocommerce_myaccount_page_id') ) . '" title="' . __('Registrácia','woocommerce') . '">' . __('Registrácia','woocommerce') .'</a>';
		} else {
            echo '<a href="' . get_permalink( get_option('woocommerce_myaccount_page_id') ) . '" title="' . __('My Account','woocommerce') . '">' . __('My Account','woocommerce') .'</a>';
			echo '<a href="'.wp_logout_url().'">'.__('Odhlásiť','woocommerce').'</a>';
		}
	echo '</div>';
}

require get_stylesheet_directory() . '/inc/template-hooks.php';
require get_stylesheet_directory() . '/inc/custom-fields.php';
?>
