<?php
/**
 *
 * @package   woo_xml_feeds
 * @author    Vladislav Musilek
 * @license   GPL-2.0+
 * @link      http://musilda.cz
 * @copyright 2014 Vladislav Musilek
 *
 * @wordpress-plugin
 * Plugin Name:       Woocommerce XML Feeds SK
 * Plugin URI:        http://musilda.cz
 * Description:       Create products XML feeds from Woocommerce
 * Version:           2.0.5
 * Author:            Vladislav Musilek
 * Author URI:        http://musilda.cz
 * Text Domain:       woo-xml-feeds
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//Define
define( 'WOOXMLLANG', substr(get_bloginfo('language'),0,2) );
define( 'WOOXMLDIR', plugin_dir_path( __FILE__ ) );
define( 'WOOXMLURL', plugin_dir_url( __FILE__ ) );
define( 'WOOXMLVERSION', '2.0.5' );

require_once( plugin_dir_path( __FILE__ ) . 'includes/plugin-update-checker-master/plugin-update-checker.php' );
$MyUpdateChecker = PucFactory::buildUpdateChecker(
    'http://update-server.toret.cz/wp-update-server-master/?action=get_metadata&slug=woo-xml-feeds-sk', 
    __FILE__,
    'woo-xml-feeds-sk' 
);

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/
require_once( plugin_dir_path( __FILE__ ) . 'admin/includes/setting.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/class-woo-xml-feeds.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Woo_Xml_Feeds', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Woo_Xml_Feeds', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Woo_Xml_Feeds', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-woo-xml-feeds-admin.php' );
	add_action( 'plugins_loaded', array( 'Woo_Xml_Feeds_Admin', 'get_instance' ) );

}




/*
  function tax_pagination($cat_list){
     $catTerms = explode(',',$cat_list);
     $all = count($catTerms);
     $pages = ceil($all / 50);
     if(!empty($_GET['catoffset'])){
       $current = $_GET['catoffset'];
     }else{
       $current = 1;
     }
     
     $html = '';
     $html .= '<div class="woo-xml-pagination">';
     $query_string = $_SERVER['QUERY_STRING'];
     if($pages != 1){
     
      for ($i=1; $i <= $pages; $i++){
        if($current == $i){
            $html .= '<span class="btn btn-default">'.$i.'</span>';
        }else{
            $html .= '<a class="btn btn-primary" href="'.admin_url().'admin.php?page=heureka-sk&catoffset='.$i.'">'.$i.'</a>';
        }
      }
     
     }
     
     $html .= '</div>';
     
     return $html;
  }  



*/ 

  /**
   * Save one product stock data 
   *
   */
/*           
  add_action( 'wp_ajax_woo_xml_save_one_product', 'woo_xml_feed_save_one_product' );
  
  function woo_xml_feed_save_one_product(){
	
    $product_id            = sanitize_text_field($_POST['product']);
    $custom_product_title  = sanitize_text_field($_POST['custom_product_title']);
    $ean                   = sanitize_text_field($_POST['ean']);
    $isbn                  = sanitize_text_field($_POST['isbn']);
    $heureka_cpc           = sanitize_text_field($_POST['heureka_cpc']);
    $heureka_cpc_sk        = sanitize_text_field($_POST['heureka_cpc_sk']);
    $accessory             = sanitize_text_field($_POST['accessory']);
    $heureka_category      = sanitize_text_field($_POST['heureka_category']);
    $heureka_category_sk   = sanitize_text_field($_POST['heureka_category_sk']);
    $zbozi_category        = sanitize_text_field($_POST['zbozi_category']);
    $delivery_date         = sanitize_text_field($_POST['delivery_date']);
    $dues                  = sanitize_text_field($_POST['dues']);
    $heureka_item_type     = sanitize_text_field($_POST['heureka_item_type']);
    $zbozi_unfeatured      = sanitize_text_field($_POST['zbozi_unfeatured']);
    $zbozi_extra_message   = sanitize_text_field($_POST['zbozi_extra_message']);
    $product_deadline_time = sanitize_text_field($_POST['product_deadline_time']);
    $product_delivery_time = sanitize_text_field($_POST['product_delivery_time']);
    $video_url             = sanitize_text_field($_POST['video_url']);
    $manufacturer          = sanitize_text_field($_POST['manufacturer']);
    $zbozi_cpc             = sanitize_text_field($_POST['zbozi_cpc']);
    $srovname_toll         = sanitize_text_field($_POST['srovname_toll']);
    $pricemania_shipping   = sanitize_text_field($_POST['pricemania_shipping']);
  
  
     update_post_meta($product_id, 'custom_product_title', $custom_product_title);
     update_post_meta($product_id, '_ean', $ean);
     update_post_meta($product_id, '_isbn', $isbn);
     update_post_meta($product_id, 'heureka_cpc', $heureka_cpc);
     update_post_meta($product_id, 'heureka_cpc_sk', $heureka_cpc_sk);
     update_post_meta($product_id, 'accessory', $accessory);
     update_post_meta($product_id, 'heureka_category', $heureka_category);
     update_post_meta($product_id, 'heureka_category_sk', $heureka_category_sk);
     update_post_meta($product_id, 'zbozi_category', $zbozi_category);
     update_post_meta($product_id, 'delivery_date', $delivery_date);
     update_post_meta($product_id, 'dues', $dues);
     update_post_meta($product_id, 'heureka_item_type', $heureka_item_type);
     update_post_meta($product_id, 'zbozi_unfeatured', $zbozi_unfeatured);
     update_post_meta($product_id, 'zbozi_extra_message', $zbozi_extra_message);
     update_post_meta($product_id, 'product_deadline_time', $product_deadline_time);
     update_post_meta($product_id, 'product_delivery_time', $product_delivery_time);
     update_post_meta($product_id, 'video_url', $video_url);
     update_post_meta($product_id, 'manufacturer', $manufacturer);
     update_post_meta($product_id, 'zbozi_cpc', $zbozi_cpc);
     update_post_meta($product_id, 'srovname_toll', $srovname_toll);
     update_post_meta($product_id, 'pricemania_shipping', $pricemania_shipping);
     
     echo 'test';
     exit();
  }  
*/

  /**
   * Save variation product stock data 
   *
   */        
  /*
  add_action( 'wp_ajax_woo_xml_save_variation_product', 'woo_xml_save_variation_product' );
  
  function woo_xml_save_variation_product(){
	  
    $product_id                 = sanitize_text_field($_POST['product']);
    $variation_heureka_title    = sanitize_text_field($_POST['variation_heureka_title']);
    $variation_heureka_category = sanitize_text_field($_POST['variation_heureka_category']);
    $variation_video_url        = sanitize_text_field($_POST['variation_video_url']);
    $variation_delivery_date    = sanitize_text_field($_POST['variation_delivery_date']);
    $variation_accessory        = sanitize_text_field($_POST['variation_accessory']);
    $variation_dues             = sanitize_text_field($_POST['variation_dues']);
    
  
     update_post_meta($product_id, '_variation_heureka_title', $variation_heureka_title);
     update_post_meta($product_id, '_variation_heureka_category', $variation_heureka_category);
     update_post_meta($product_id, '_variation_video_url', $variation_video_url);
     update_post_meta($product_id, '_variation_delivery_date', $variation_delivery_date);
     update_post_meta($product_id, '_variation_accessory', $variation_accessory);
     update_post_meta($product_id, '_variation_dues', $variation_dues);
     
     
     echo 'test';
     exit();
  }  

*/

require_once( plugin_dir_path( __FILE__ ) . 'includes/wxm-product-meta-function.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/wxm-taxonomy-function.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/wxm-ajax-function.php' );