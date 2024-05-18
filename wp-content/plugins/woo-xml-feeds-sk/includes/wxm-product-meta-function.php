<?php 
/**
 *
 * Variable fields
 *
 */                   
    
/**
 * Create new fields for variations
 *
 * @since 1.0.0  
 */
add_action( 'woocommerce_product_after_variable_attributes', 'wxm_variable_fields' , 50, 3 ); 
function wxm_variable_fields( $loop, $variation_data, $variation ) {

		  include( WOOXMLDIR.'admin/includes/custom-variation-data.php' );

}    


    
/**
 * Save new fields for variations
 *  
 * @since 1.0.0
 */
add_action( 'woocommerce_save_product_variation', 'wxm_save_variable_fields' , 10, 2 );  
function wxm_save_variable_fields( $post_id ) {
	
  if (isset( $_POST['variable_sku'] ) ){
 
		$variable_sku          = $_POST['variable_sku'];
		$variable_post_id      = $_POST['variable_post_id'];
    
    
    
    $data_array = array(
          '_variation_heureka_title',
          '_variation_heureka_name',
          '_variation_ean',
          '_variation_isbn',
          '_variation_heureka_category',
          '_variation_pricemania_category',
          '_variation_najnakup_category',
          '_variation_imgurl_alternative',
          '_variation_video_url',
          '_variation_video_name',
          '_variation_heureka_cpc_sk',
          '_variation_delivery_date',
          '_variation_accessory',
          '_variation_dues',
          '_variation_google_identifikator_exists',
          '_variation_google_stitek_value_1',
          '_variation_google_stitek_value_2',
          '_variation_google_stitek_value_3',
          '_variation_google_stitek_value_4',
          '_variation_google_stitek_value_5',
          '_variation_123_nakup_category',
          '_variation_seo_title',
          '_variation_seo_keywords',
          '_variation_seo_description'
    );
    
                   
		//for ( $i = 0; $i < sizeof( $variable_post_id ); $i++ ) :
			//$variation_id = (int)$variable_post_id[$i];
      
    foreach( $variable_post_id as $i => $item ){  
      $variation_id = (int)$variable_post_id[$i];
			foreach( $data_array as $meta ){
      
      if( $meta == '_variation_imgurl_alternative' ){
        if(!empty($_POST['_variation_imgurl_alternative'][$i])){
          $alternative_images = $_POST['_variation_imgurl_alternative'][$i];
          $a_img = array(); 
          if( !empty( $alternative_images ) ){
            foreach($_POST['_variation_imgurl_alternative'][$i] as $item){
              $a_img[] = $item;
            }
              $a = serialize($a_img);
              update_post_meta( $variation_id, '_variation_imgurl_alternative', $_POST['_variation_imgurl_alternative'][$i] ) ;
          }else{
              delete_post_meta( $variation_id, '_variation_imgurl_alternative' );
          }
        }
      }else{
      
      
        if ( isset( $_POST[$meta][$i] ) ) {
				    update_post_meta( $variation_id, $meta, stripslashes( $_POST[$meta][$i] ) );
			  }else{
            delete_post_meta( $variation_id, $meta );
        }
      }
        
      }  
      }  
		//endfor;
		
	}
}

    
		/**
		 *
		 * Product fields
		 *     
		 */              
    add_action('woocommerce_product_write_panel_tabs', 'info_tab_options_tab_spec' );
  /**
   * Custom Tabs for product 
   */
  function info_tab_options_tab_spec() {
  ?>
    <li class="info_tabxml"><a href="#info_tab_dataxml"><?php _e('Specifikace pro XML feed', 'woo-xml-feed' ); ?></a></li>
  <?php
  }    
    add_action('woocommerce_product_write_panels', 'woo_add_custom_general_fields' );
  /**
	 * Display Tabs form for product
	 * 
	 * @since    1.0.0        
	 */
	function woo_add_custom_general_fields() {
    ?><div id="info_tab_dataxml" class="panel woocommerce_options_panel"><?php 
    include(WOOXMLDIR.'admin/includes/custom-general-data.php');	
    ?></div><?php
  }    
    
    add_action('woocommerce_process_product_meta', 'woo_add_custom_general_fields_save' );

  /**
   * Save general save data
   *
   * @since    1.0.0   
   */        
  function woo_add_custom_general_fields_save( $post_id ){
	
  $fields = array(
        'custom_product_title',
        'custom_product_name',
        '_ean',
        '_isbn',
        'manufacturer',
        '123_nakup_manufacturer',
        'heureka_cpc_sk',
        'imgurl_alternative',
        'video_url',
        'video_name',
        'heureka_category_sk',
        'pricemania_category',
        'najnakup_category',
        'heureka_item_type',
        'delivery_date',
        'accessory',
        'dues',
        'pricemania_shipping',
        'najnakup_shipping',
        'najnakup_availability',
        'product_deadline_time',
        'product_delivery_time',
        'google_identifikator_exists',
        'google_stitek_value_1',
        'google_stitek_value_2',
        'google_stitek_value_3',
        'google_stitek_value_4',
        'google_stitek_value_5',
        '123_nakup_category',
        'price_type',
        'is_handmate',
        'seo_title',
        'seo_keywords',
        'seo_description'
  );
  
  
    foreach($fields as $item){
      if( $item == 'imgurl_alternative' ){
        if(!empty($_POST['imgurl_alternative'])){
          $alternative_images = $_POST['imgurl_alternative'];
          $a_img = array(); 
          if( !empty( $alternative_images ) ){
            foreach($_POST['imgurl_alternative'] as $item){
              $a_img[] = $item;
            }
              $a = serialize($a_img);
              update_post_meta( $post_id, 'imgurl_alternative', $_POST['imgurl_alternative'] ) ;
          }else{
              delete_post_meta( $post_id, 'imgurl_alternative' );
          }
        }
      }else{
          if( !empty( $_POST[$item] ) ){
		        update_post_meta( $post_id, $item, esc_attr( $_POST[$item] ) );        
          }else{
            delete_post_meta( $post_id, $item );
          } 
      }
    }  
    
  
  
  
  }  