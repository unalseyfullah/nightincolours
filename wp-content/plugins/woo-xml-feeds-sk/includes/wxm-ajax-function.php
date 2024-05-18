<?php 

   /**
   * Save one product stock data 
   *
   */        
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


  /**
   * Save variation product stock data 
   *
   */        
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
