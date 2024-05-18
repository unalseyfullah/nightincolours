<?php
/**
 *
 * @package   create_dom
 * @author    Vladislav Musilek
 * @license   GPL-2.0+
 * @link      http://musilda.cz
 * @copyright 2014 Vladislav Musilek
 *
 * Version 1.1.0
 *  
 */

class Create_Dom {
	
  /**
	 * Dom document
	 *
	 * @since    1.1.0
	 *
	 * @var      object
	 */
  public $dom_document;
  
  public $service; 
                                  
                                
  /**
   * Post meta
   * since 1.1.0
   */
  public $meta;
  
           
  /**
   * All option settings
   *
   */        
  public $heureka_doba_doruceni;    
  public $item_type_global;
  public $manufacturer_global;
  public $heureka_cpc_all;
  
  public $use_excerpt;
  
  public $heureka_assing_categories;
  public $heureka_categories;
  public $heureka_delivery;
  public $heureka_excluded_categories;
  public $heureka_categories_cpc;
  public $heureka_cat_params; 
  
  public $heureka_kurz;
  
  public $srovname_categories_cpc; 
  public $srovname_cat_params;

  public $zbozi_assing_categories;
  public $zbozi_excluded_categories;
  public $zbozi_categories_cpc;
  public $zbozi_doba_doruceni;
  public $zbozi_cpc_all;
  public $zbozi_unfeatured_global;
  public $pricemania_shipping;
	/**
	 * Initialize class
	 *
	 * @since     1.1.0
	 */

  public function __construct() {
  
  
      $this->dom_document = new XMLWriter();     
        
      
    //Get all options
      
      $this->service = get_option( 'active_feed' );
      
      
      $this->manufacturer_global         = get_option( 'manufakturer' );
      $this->item_type_global            = get_option( 'heureka_item_type' );
      $this->heureka_doba_doruceni       = get_option( 'delivery_date' );
      $this->heureka_cpc_all             = get_option( 'heureka-cpc' );
      $this->zbozi_doba_doruceni         = get_option( 'delivery_date' );
      
      $this->use_excerpt                 = get_option( 'use_excerpt' );
      
    if($this->service == 'heureka-cz'){  
      $this->heureka_assing_categories   = get_option( 'woo_heureka_assing_categories');
      $this->heureka_categories          = get_option( 'woo_heureka_categories');
      $this->heureka_delivery            = get_option( 'woo_heureka_delivery');
      $this->heureka_excluded_categories = get_option( 'woo_heureka_excluded_categories');
      $this->heureka_categories_cpc      = get_option( 'woo_heureka_categories_cpc');
      $this->heureka_cat_params          = get_option( 'woo_heureka_cat_params');
    }
    elseif($this->service == 'heureka-sk'){
      $this->heureka_assing_categories   = get_option( 'woo_heureka_assing_categories_sk');
      $this->heureka_categories          = get_option( 'woo_heureka_categories_sk');
      $this->heureka_delivery            = get_option( 'woo_heureka_delivery_sk');
      $this->heureka_excluded_categories = get_option( 'woo_heureka_excluded_categories_sk');
      $this->heureka_categories_cpc      = get_option( 'woo_heureka_categories_cpc_sk');
      $this->heureka_cat_params          = get_option( 'woo_heureka_cat_params_sk');
      $this->heureka_kurz                = get_option( 'heureka-kurz');
    }
    elseif($this->service == 'google'){  
      $this->google_assing_categories   = get_option( 'woo_google_assing_categories');
      $this->google_categories          = get_option( 'woo_google_categories');
      $this->google_excluded_categories = get_option( 'woo_google_excluded_categories');
    }
    elseif($this->service == 'pricemania'){
      $this->pricemania_shipping            = get_option( 'woo_pricemania_delivery' );
      $this->pricemania_excluded_categories = get_option( 'woo_pricemania_excluded_categories');
    }
    elseif($this->service == '123-nakup'){
      $this->nakup_assing_categories   = get_option( 'woo_123_nakup_assing_categories');
      $this->nakup_delivery            = get_option( 'woo_123_nakup_delivery');
      $this->nakup_excluded_categories = get_option( 'woo_123_nakup_excluded_categories');
    }
    
    
    
    
  }
  
  /**
   *
   *
   */
   public function meta($postid){
     return get_post_meta($wp_query->post->ID);
   }        
  

  /**
   * Create element
   * since 1.1.0
   */        
  public function create_element($dom, $name){
    $root = $dom->startElement($name);
    $dom->endElement();
  } 

  /**
   * Create child element
   * since 1.1.0
   */        
  public function create_child_element($dom,$name){
  
    $dom->startElement( $name);
    $dom->endElement();
  
  } 
  /**
   * Create child text element
   * since 1.1.0
   */        
  public function create_child_text_element($string,$dom,$name){
  
    $dom->startElement( $name );
    $dom->text( $string );
    $dom->endElement();
  
   
  }
    /**
   * Create child cdata element
   * since 1.1.0
   */             
  public function create_child_cdata_element($string,$dom,$name){
  
    $dom->startElement( $name );
    $dom->writeCData( $string );
    $dom->endElement();
    
  }
  
  /**
   * Create child cdata element for simple product
   * since 1.1.0
   */        
  public function child_simple_cdata($string1,$string2,$dom,$name){
  
    $dom->startElement( $name );
    
    
    if(!empty($string1)){
    
      if(trim($string1)!=''){
      
        $dom->writeCData( $string1 );
      
      }
    
    }else{
    
      $dom->writeCData( $string2 );
      
    }
    
    $dom->endElement();
    
  }
  
  /**
   * Create child cdata element for variantion
   * since 1.1.0
   */        
  public function child_variable_cdata($string1,$string2,$meta,$meta_name,$dom,$name){
  
    $dom->startElement( $name );
    if(!empty($string1) && trim($string1)!=''){
      $dom->writeCData( $string1 );
    }elseif(!empty($meta[$meta_name][0]) && trim($meta[$meta_name][0])!=''){
      $dom->writeCData( $meta[$meta_name][0] );
    }else{
      $dom->writeCData( $string2 );
    }
    $dom->endElement();
  }
  
  /**
   * Create element with control post meta exist and global data exits
   * since 1.0.0
   */         
  public function simple_cdata_meta_element($meta_name,$meta,$option,$dom,$name){
     
    if(!empty($meta[$meta_name][0])){
    
      $dom->startElement( $name );
      $dom->writeCData( $meta[$meta_name][0] );      
      $dom->endElement();
    
    }else{
      $global = $option;
      if(!empty($global)){
        $dom->startElement( $name );
        $dom->writeCData( $global );      
        $dom->endElement();
      }
    }
  
  }
  /**
   * Create element with control post meta exist 
   * if post meta empty, element not exist   
   * since 1.0.0
   */         
  public function simple_cdata_only_meta_element($meta_name,$meta,$dom,$name){
     
    if(!empty($meta[$meta_name][0])){
    
      $dom->startElement( $name );
      $dom->writeCData( $meta[$meta_name][0] );      
      $dom->endElement();
      
    }  
  } 
  /**
   * Create element with control post meta exist 
   * if post meta empty, element not exist   
   * since 1.0.0
   */         
  public function simple_cdata_only_postmeta_element($meta_name,$postid,$dom,$name){
   $meta = get_post_meta( $postid, $meta_name );
    if(!empty($meta[0])){
      $dom->startElement( $name );
      $dom->writeCData( $meta[0] ); 
      $dom->endElement();
    }  
  }   
  
  /**
   * Create element with control post meta exist 
   * if post meta empty, element not exist   
   * since 1.0.0
   */         
  public function simple_text_only_meta_element($meta_name,$meta,$dom,$name){
     
    if(!empty($meta[$meta_name][0])){
    
      $dom->startElement( $name );
      $dom->text( $meta[$meta_name][0] ); 
      $dom->endElement();
    
    }  
  }  
   
  
  
  
  
  /**
   * Get simple ITEM_TYPE
   * function is only for Heureka   
   * since 1.1.0
   */           
  public function simple_item_type($meta,$dom,$name){
    
    if(!empty($meta['heureka_item_type'][0] )&& $meta['heureka_item_type'][0] == 'bazar'){
      $item_type = 'ITEM_TYPE';
      $dom->startElement( $item_type );
      $dom->writeCData( $meta['heureka_item_type'][0] ); 
      $dom->endElement();
    }else{
      $item_type_global = $this->item_type_global;
      
      if(!empty($item_type_global) && $item_type_global == 'bazar'){
        $item_type = 'ITEM_TYPE';
        $dom->startElement( $item_type );
        $dom->writeCData( $item_type_global ); 
        $dom->endElement();
      }
    
    }
  
  }
  
  /**
   * Get simple Heureka category
   * function is only for Heureka   
   * since 1.1.0
   */         
  public function simple_heureka_category($meta,$postid,$dom,$name){
  
  
    if(!empty($meta['heureka_category_sk'][0]) && $meta['heureka_category_sk'][0]!='default' ){
    $terms = wc_get_product_terms($postid,'product_cat',array('orderby' => 'parent','fields'=>'ids'));
    //CATEGORYTEXT
    $dom->startElement( 'CATEGORYTEXT' );
    $dom->writeCData( $this->heureka_categories[$meta['heureka_category_sk'][0]]['category_fullname'] ); 
    $dom->endElement();
     
    }else{
    
    $terms = wc_get_product_terms($postid,'product_cat',array('orderby' => 'parent','fields'=>'ids'));     
    //CATEGORYTEXT
    $dom->startElement( 'CATEGORYTEXT' );
    if(!empty($this->heureka_assing_categories[$terms[0]]) && $this->heureka_assing_categories[$terms[0]] != 'default' ){
      $dom->writeCData( $this->heureka_categories[$this->heureka_assing_categories[$terms[0]]]['category_fullname'] );
    } 
    $dom->endElement();
    
    
    }
    
  } 
  
  /**
   * Get variation Heureka category
   * function is only for Heureka   
   * since 1.1.0
   */         
  public function variation_heureka_category($meta,$varid,$postid,$dom){
  
    $cat_var = get_post_meta( $varid, '_variation_heureka_category' );
    if(!empty($cat_var[0]) && $cat_var[0] != 'default' ){
    
      $dom->startElement('CATEGORYTEXT');
      $dom->writeCData($cat_var[0]);
      $dom->endElement();
    
    }else{
      if(!empty($meta['heureka_category'][0]) && $meta['heureka_category'][0]!='default'){
    
        $dom->startElement('CATEGORYTEXT');
        $dom->writeCData($meta['heureka_category'][0]);
        $dom->endElement();
      }else{
        $terms = wc_get_product_terms($postid,'product_cat',array('orderby' => 'parent','fields'=>'ids'));
        
        $dom->startElement('CATEGORYTEXT');
          
          if(!empty($this->heureka_assing_categories[$terms[0]]) && $this->heureka_assing_categories[$terms[0]] != 'default'){
        
            $dom->writeCData($this->heureka_categories[$this->heureka_assing_categories[$terms[0]]]['category_fullname']);
        
          }
     
        $dom->endElement();
     
      }
    }
  
  }
  
  /**
   * Get simple Zbozi category
   * function is only for Zbozi   
   * since 1.1.0
   */         
  public function simple_zbozi_category($meta,$postid,$dom,$name){
  
  
    if(!empty($meta['zbozi_category'][0]) && $meta['zbozi_category'][0]!='default'){
      //CATEGORYTEXT
      $dom->startElement($name);
      $dom->writeCData($meta['zbozi_category'][0]);
      $dom->endElement();
    }else{
      $terms = wc_get_product_terms($postid,'product_cat',array('orderby' => 'parent','fields'=>'ids'));
    
      //CATEGORYTEXT
      $dom->startElement($name);
      $dom->writeCData($this->zbozi_assing_categories[$terms[0]]);
      $dom->endElement();
    
    }
  
  } 
  
  /**
   * Get variation zbozi category
   * since 1.1.0
   */        
  public function variation_zbozi_category($meta,$varid,$postid,$dom){
  
  
       $cat_var = get_post_meta( $varid, '_variation_heureka_category' );
        if(!empty($cat_var[0]) && $cat_var[0] != 'default' ){
          //CATEGORYTEXT
          $dom->startElement('CATEGORYTEXT');
          $dom->writeCData($cat_var[0]);
          $dom->endElement();
        }else{
          
          if(!empty($meta['zbozi_category'][0]) && $meta['zbozi_category'][0]!='default'){
            //CATEGORYTEXT
            $dom->startElement('CATEGORYTEXT');
            $dom->writeCData($meta['zbozi_category'][0]);
            $dom->endElement();
          }else{
            $terms = wc_get_product_terms($postid,'product_cat',array('orderby' => 'parent','fields'=>'ids'));
    
            //CATEGORYTEXT
            $dom->startElement('CATEGORYTEXT');
            $dom->writeCData($this->zbozi_assing_categories[$terms[0]]);
            $dom->endElement();
    
          }
        }
  
  
  } 
  
  
  /** 
   * Get variation Heureka image alternative for variations
   * since 1.1.0
   */        
  public function variation_heureka_image_alternative($varid,$dom){ 
  
    $img_var = get_post_meta( $varid, '_variation_imgurl_alternative' );
    if(!empty($img_var)){
    $count = count($img_var[0]);
      if($count > 0){
        $met = $img_var[0];
        foreach($met as $item){
          if(trim($item)!=''){
            $dom->startElement('IMGURL_ALTERNATIVE');
            $dom->writeCData($item);
            $dom->endElement();
          }
        }
      }
    }
  
  }
  
  
  /**
   * Get simple Heureka cpc
   * function is only for Heureka
   * since 1.1.0
   */
  public function simple_heureka_cpc($meta,$postid,$option,$dom,$name){
  
    if($this->service == 'heureka-cz'){
      $meta_name = 'heureka_cpc';
    }elseif($this->service == 'heureka-sk'){
      $meta_name = 'heureka_cpc_sk';
    }
    elseif($this->service == 'srovname'){
      $meta_name = 'srovname_toll';
    }
  
        //HEUREKA_CPC
    if(!empty($meta[$meta_name][0])){
      
      $dom->startElement($name);
      $dom->writeCData($meta[$meta_name][0]);
      $dom->endElement();
      
    }else{
    //Get category cpc
    $terms = wc_get_product_terms($postid,'product_cat',array('orderby' => 'parent','fields'=>'ids'));  
      if(!empty($this->heureka_categories_cpc[$terms[0]])){
          
          $dom->startElement($name);
          $dom->writeCData($this->heureka_categories_cpc[$terms[0]]);
          $dom->endElement();
          
      }else{    
        //Get Global cpc
         if($this->service == 'heureka-cz'){
            $option = 'heureka_cpc_all';
          }elseif($this->service == 'heureka-sk'){
            $option = 'heureka-cpc_sk';
          }
        
        $heureka_cpc_all = get_option($option);
        if(!empty($heureka_cpc_all)){
        
          $dom->startElement($name);
          $dom->writeCData($heureka_cpc_all);
          $dom->endElement();
        
        }
      }
    }
  
  }   
  
    /**
   * Get simple Srovname cpc
   * function is only for Srovname
   * since 1.1.0
   */
  public function simple_toll_cpc($meta,$postid,$option,$dom,$name){
  
      $meta_name = 'srovname_toll';
    
        //HEUREKA_CPC
    if(!empty($meta[$meta_name][0])){
      
      $dom->startElement($name);
      $dom->writeCData($meta[$meta_name][0]);
      $dom->endElement();
      
    }else{
    //Get category cpc
      $terms = $this->get_product_terms($postid,'product_cat',array('orderby' => 'parent','fields'=>'ids'));
    
      if(!empty($this->srovname_categories_cpc[$terms[0]])){
          
          $dom->startElement($name);
          $dom->writeCData($this->srovname_categories_cpc[$terms[0]]);
          $dom->endElement();
          
      }else{    
        //Get Global cpc
        $option = 'heureka_cpc_all';
        
        $heureka_cpc_all = $option;
        if(!empty($heureka_cpc_all)){
        
          $dom->startElement($name);
          $dom->writeCData($heureka_cpc_all);
          $dom->endElement();
        
        }
      }
    }
  
  }   
  
  /**
   * Get simple Heureka PARAMS
   * function is only for Heureka
   * create attributes param for simple prodct   
   * since 1.1.0
   */          
  public function simple_heureka_params($my_product,$dom){
  
     if($my_product->has_attributes()){

    $attributes = $my_product->get_attributes();
    
    foreach ( $attributes as $attribute ) :
		
		$att_label =  wc_attribute_label( $attribute['name'] ); 
		
    		if ( $attribute['is_taxonomy'] ) {
	            $values = wc_get_product_terms( $my_product->id, $attribute['name'], array( 'fields' => 'names' ) );
    		} else {
    			$values = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
		     }
         
        foreach ( $values as $value ) :
     
          //PARAM
          $dom->startElement('PARAM');
          
          
          //PARAM_NAME
          $dom->startElement('PARAM_NAME');
          $dom->writeCData($att_label);
          $dom->endElement();
          
          //VAL
          $dom->startElement('VAL');
          $dom->writeCData($value);
          $dom->endElement();
          
          $dom->endElement();     
     
        endforeach;    
         
	   endforeach; 
    
    }
  
  } 
  
  
  /**
   * Get variation Heureka PARAMS
   * function is only for Heureka
   * create attributes param for product with variations   
   * since 1.1.0
   */          
  public function variation_heureka_params($varid,$postid,$dom){
  
    $attributes = maybe_unserialize( get_post_meta( $postid, '_product_attributes', true ) );
    
    foreach ( $attributes as $attribute ) :
		$variation_data = get_post_meta( $varid );
    
    $variation_selected_value = isset( $variation_data[ 'attribute_' . sanitize_title( $attribute['name'] ) ][0] ) ? $variation_data[ 'attribute_' . sanitize_title( $attribute['name'] ) ][0] : '';
        
    $att_label =  wc_attribute_label( $attribute['name'] ); 
		
    		if ( $attribute['is_taxonomy'] ) {
	          $values = wc_get_product_terms( $postid, $attribute['name'], array( 'fields' => 'slugs' ));
     
            $nvalues = wc_get_product_terms( $postid, $attribute['name'], array( 'fields' => 'names' ));
            $i = 0 ;
            foreach ( $values as  $term ) {
            if($variation_selected_value == $term){ $value = $nvalues[$i];}
            $i++;
            }
            
    		} else {
    			$values = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
          foreach ( $values as $option ) {
						if($variation_selected_value==sanitize_title( $option )){ $value = esc_html( apply_filters( 'woocommerce_variation_option_name', $option ));}
          }
		     }
       
          //if(!empty($variation_selected_value)){ $value = $variation_selected_value; }
          if(!empty($value)){
         //PARAM
          $dom->startElement('PARAM');
   
          //PARAM_NAME
          $dom->startElement('PARAM_NAME');
          $dom->writeCData($att_label);
          $dom->endElement(); 
                      
          //VAL
          $dom->startElement('VAL');
          $dom->writeCData($value);
          $dom->endElement(); 
          
          $dom->endElement(); 
          
          unset($value);
          
          }
         
	   endforeach; 
  
  
  }
  
  
    /**
   * Get simple Heureka PARAMS
   * function is only for Heureka
   * create attributes param for simple prodct   
   * since 1.1.0
   */          
  public function simple_heureka_custom_params($postid,$dom){
      $terms = wc_get_product_terms($postid,'product_cat',array('orderby' => 'parent','fields'=>'ids'));
     if(!empty($this->heureka_cat_params[$terms[0]]['parametry'])){
      
      foreach($this->heureka_cat_params[$terms[0]]['parametry'] as $lit => $var){
      
      
          $dom->startElement('PARAM');
          
          //PARAM_NAME
          $dom->startElement('PARAM_NAME');
          $dom->writeCData($this->heureka_cat_params[$terms[0]]['parametry'][$lit]['nazev_parametru']);
          $dom->endElement(); 
          
          //VAL
          $dom->startElement('VAL');
          $dom->writeCData($this->heureka_cat_params[$terms[0]]['parametry'][$lit]['hodnota_parametru']);
          $dom->endElement(); 
          
          $dom->endElement(); 
      
      }
     }
  } 
  
  
  /**
   * Get variation imgurl
   * since 1.0.0
   */        
  public function create_variation_img_url($varsid,$postid,$dom,$name){
  
     $img = wp_get_attachment_image_src( get_post_thumbnail_id($varsid), 'shop_single' ); 
        if(!empty($img[0])){
    
          $dom->startElement($name);
          $dom->writeCData($img[0]);
          $dom->endElement();
        }else{
          $img = wp_get_attachment_image_src( get_post_thumbnail_id($postid), 'shop_single' ); 
          if(!empty($img[0])){
    
            $dom->startElement($name);
            $dom->writeCData($img[0]);
            $dom->endElement(); 
          }
        }
  
  } 
  
   
  /**
   * Get variation Delivery date
   * function is only for Heureka
   * since 1.1.1
   */
  public function variation_delivery_date($var_id,$meta,$dom,$name){
   $var_del = get_post_meta( $var_id, '_variation_delivery_date' );
   //DELIVERY_DATE
   if(!empty($var_del[0])){
      if($var_del[0]=='skladom'){$del = '0';}else{$del = $var_del[0];}
   
      $dom->startElement($name);
      $dom->text($del);
      $dom->endElement();
   }else{ 
    if(!empty($meta['delivery_date'][0])){
      if($meta['delivery_date'][0]=='skladom'){$del = '0';}else{$del = $meta['delivery_date'][0];}
      //Nastavení pro produkt
      $dom->startElement($name);
      $dom->text($del);
      $dom->endElement();
    }else{
      if(!empty($this->heureka_doba_doruceni)){
        //Globální nastavení
        if($this->heureka_doba_doruceni == 'skladom'){$del = '0';}else{$del = $this->heureka_doba_doruceni;}
        $dom->startElement($name);
        $dom->text($del);
        $dom->endElement();
    
      }else{
        //Nastavíme skladem
        $dom->startElement($name);
        $dom->text('0');
        $dom->endElement();
      }
    }
   
  } 
  } 


  /**
   * Get global Delivery date
   * function is only for Heureka
   * since 1.1.0
   */
  public function global_delivery_date($meta,$dom,$name){
   
   //DELIVERY_DATE
    if(!empty($meta['delivery_date'][0])){
      if($meta['delivery_date'][0]=='skladom'){$del = '0';}else{$del = $meta['delivery_date'][0];}
      //Nastavení pro produkt
      $dom->startElement($name);
      $dom->text($del);
      $dom->endElement();
    }else{
      if(!empty($this->heureka_doba_doruceni)){
        //Globální nastavení
        if($this->heureka_doba_doruceni == 'skladom'){$del = '0';}else{$del = $this->heureka_doba_doruceni;}
        $dom->startElement($name);
        $dom->text($del);
        $dom->endElement();
    
      }else{
        //Nastavíme skladem
        $dom->startElement($name);
        $dom->text('0');
        $dom->endElement();
      }
    }
   
   
  } 

  /**
   * Get global Delivery 
   * function is only for Heureka
   * since 1.1.0
   */
   public function global_delivery($dom){
   
      if(count($this->heureka_delivery)>0){
       foreach ( $this->heureka_delivery as $item ) :
    
        if($item['active'] == 'on'){
    
         $dom->startElement('DELIVERY');
         
          //DELIVERY_ID
          $dom->startElement('DELIVERY_ID');
          $dom->text($item['id']);
          $dom->endElement();
          //DELIVERY_PRICE
          $dom->startElement('DELIVERY_PRICE');
          $dom->text($item['delivery_price']);
          $dom->endElement();
          //DELIVERY_PRICE_COD
          $dom->startElement('DELIVERY_PRICE_COD');
          $dom->text($item['delivery_price_cod']);
          $dom->endElement();
          
          $dom->endElement();
         
         } 
         
       endforeach;
    
    }
    
   }

   /**
    *
    * Pricemania section
    *
    *
    *
    *
    *
    *
    *
    * Methods only for pricemania feed
    *
    */                                               

   /**
    * Get lowest category - Pricemania
    * since 1.1.0
    */               
   public function get_lowest_category($post_id){
   
     $termy = get_the_terms($post_id,'product_cat');
      $parent = array();
      $child = array();
        foreach($termy as $l){
          $parent[]=$l->parent;
          $child[]=$l->term_id;
        }
          foreach($child as $polozka){
            if(!in_array($polozka,$parent)){
              $kat_id = $polozka;
            }
          }
      return $kat_id;
   }


   /**
    * Get term road Pricemania
    * since 1.1.0
    */           
    public function get_term_road($term_id,$string){
      $item = get_term( $term_id, 'product_cat');
      $string = $item->name.' > '.$string;
        
      if(!empty($item->parent)){
        $item = $this->get_term_road($item->parent,$string);
      } 
      if(empty($item->parent)){
          return $string;
        }
      
      
    }

    /**
     * Get variation Pricemania category
     * since 1.1.0
     */              
    public function get_variation_pricemania_category($kat_id,$varid,$meta,$dom){
    
      $cat_var = get_post_meta( $varid, '_variation_pricemania_category' );
      if(!empty($cat_var[0]) && $cat_var[0] != 'default' ){
    
        $dom->startElement('category');
        $dom->writeCData($cat_var[0]);
        $dom->endElement();
    
      }else{
      
        if(!empty($meta['pricemania_category'][0]) && $meta['pricemania_category'][0]!='default'){
          $dom->startElement('category');
          $dom->writeCData($meta['pricemania_category'][0]);
          $dom->endElement();
        }else{
          //Get category text
          $term = get_term( $kat_id, 'product_cat');
          $kategorie = '';
            if(!empty($term->parent)):
              $string = $term->name;
              $kategorie = $this->get_term_road($term->parent,$string);
            else:
              $kategorie = $term->name;
            endif;
          
          $dom->startElement('category');
          $dom->text($kategorie);
          $dom->endElement();
        }
      }
    
    }


    /**
     * Get simple Pricemania category
     * since 1.1.0
     */              
    public function get_simple_pricemania_category($kat_id,$meta,$dom){
    
        if(!empty($meta['pricemania_category'][0]) && $meta['pricemania_category'][0]!='default'){
          $dom->startElement('category');
          $dom->writeCData($meta['pricemania_category'][0]);
          $dom->endElement();
        }else{
          //Get category text
          $term = get_term( $kat_id, 'product_cat');
            if(!empty($term->parent)):
              $string = $term->name;
              $kategorie = $this->get_term_road($term->parent,$string);
            else:
              $kategorie = $term->name;
            endif;
          
          $dom->startElement('category');
          $dom->writeCData($kategorie);
          $dom->endElement();
        }
    }

  /**
   * Get variation Pricemania PARAMS
   * function is only for Pricemania
   * create attributes param for product with variations   
   * since 1.1.0
   */          
  public function variation_pricemania_params($varid,$postid,$dom){
  
    $attributes = maybe_unserialize( get_post_meta( $postid, '_product_attributes', true ) );
    
          //params
          $dom->startElement('params');
          
          
    foreach ( $attributes as $attribute ) :
		$variation_data = get_post_meta( $varid );
    
    $variation_selected_value = isset( $variation_data[ 'attribute_' . sanitize_title( $attribute['name'] ) ][0] ) ? $variation_data[ 'attribute_' . sanitize_title( $attribute['name'] ) ][0] : '';
    
    $att_label =  wc_attribute_label( $attribute['name'] ); 
		
    		if ( $attribute['is_taxonomy'] ) {
	          $values = wc_get_product_terms( $postid, $attribute['name'], array( 'fields' => 'slugs' ));
              
            $nvalues = wc_get_product_terms( $postid, $attribute['name'], array( 'fields' => 'names' ));
           
            $i = 0 ;
            foreach ( $values as  $term ) {
            if($variation_selected_value == $term){ $value = $nvalues[$i];}
            $i++;
            }
            
    		} else {
    			$values = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
          foreach ( $values as $option ) {
						if($variation_selected_value==sanitize_title( $option )){ $value = esc_html( apply_filters( 'woocommerce_variation_option_name', $option ));}
            //var_dump($value);
          }
		     }
          
          
          if(!empty($value)){
         //param
          $dom->startElement('param');
          
          //PARAM_NAME
          $dom->startElement('param_name');
          $dom->text($att_label);
          $dom->endElement();
          
          //VAL
          $dom->startElement('param_value');
          $dom->text($value);
          $dom->endElement();
          
          $dom->endElement();
          
          unset($value);
          
          }
         
	   endforeach; 
     
     $dom->endElement();
  
  
  }
  
  
  /**
   * Get simple Pricemania PARAMS
   * function is only for Pricemania
   * create attributes param for simple prodct   
   * since 1.1.0
   */          
  public function simple_pricemania_params($my_product,$dom){
  
     if($my_product->has_attributes()){

    //PARAM s
    $dom->startElement('params');
    
    $attributes = $my_product->get_attributes();
    
    foreach ( $attributes as $attribute ) :
		
		$att_label =  wc_attribute_label( $attribute['name'] ); 
		
    		if ( $attribute['is_taxonomy'] ) {
	        $values = wc_get_product_terms( $my_product->id, $attribute['name'], array( 'fields' => 'names' ) );
          
       	} else {
    			$values = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
         }
       
         
        foreach ( $values as $value ) :
     
          //PARAM
          $dom->startElement('param');
          
          //PARAM_NAME
          $dom->startElement('param_name');
          $dom->text($att_label);
          $dom->endElement();
          
          //VAL
          $dom->startElement('param_value');
          $dom->text($value);
          $dom->endElement();
          
          $dom->endElement();
     
        endforeach;    
         
	   endforeach; 
    
     $dom->endElement();
    
    }
  
  } 



  /**
   * Check if is method deprecated
   *
   */        

  public function get_product_terms( $postid, $taxonomy, $args ){
      $args = array('orderby' => 'parent','fields'=>'ids');
  
  	  unset( $args['orderby'] );
		  unset( $args['fields'] );
      $terms = $this->woo_get_object_terms( $postid, $args );
  
      usort( $terms, array( $this, 'usort_callback') );
      $terms = wp_list_pluck( $terms, 'term_id' );
  
      return $terms;
      
  }
  
  
  /**
    * Sort by parent
    * @param  WP_POST object $a
    * @param  WP_POST object $b
    * @return int
    */
  private function usort_callback( $a, $b ) {
	   if ( $a->parent === $b->parent ) {
		  return 0;
	   }
	   return ( $a->parent < $b->parent ) ? 1 : -1;
  }

  /**
   *
   *
   */
  function woo_get_object_terms($object_ids, $args = array()) {
	global $wpdb;

	$taxonomies = array('product_cat');

	if ( !is_array($object_ids) )
		$object_ids = array($object_ids);
	$object_ids = array_map('intval', $object_ids);

	$defaults = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'all');
	$args = wp_parse_args( $args, $defaults );

	$terms = array();
	
  	$t = get_taxonomy($taxonomies[0]);
		if ( isset($t->args) && is_array($t->args) )
			$args = array_merge($args, $t->args);
	
	$orderby = $args['orderby'];
	$order = $args['order'];
	$fields = $args['fields'];

	
  $orderby = 't.name';
	
		$orderby = "ORDER BY $orderby";

	$order = strtoupper( $order );
	if ( '' !== $order && ! in_array( $order, array( 'ASC', 'DESC' ) ) )
		$order = 'ASC';


	$taxonomies = "'" . implode("', '", $taxonomies) . "'";
	
  $object_ids = implode(', ', $object_ids);

	$select_this = '';
	$select_this = 't.*, tt.*';
	
  
	$query = "SELECT $select_this FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy IN ($taxonomies) AND tr.object_id IN ($object_ids) $orderby $order";

	$objects = false;
	if ( 'all' == $fields || 'all_with_object_id' == $fields ) {
		$_terms = $wpdb->get_results( $query );
		foreach ( $_terms as $key => $term ) {
			$_terms[$key] = sanitize_term( $term, $taxonomy, 'raw' );
		}
		$terms = array_merge( $terms, $_terms );
		update_term_cache( $terms );
		$objects = true;
	} 

	if ( ! $terms ) {
		$terms = array();
	} elseif ( $objects && 'all_with_object_id' !== $fields ) {
		$_tt_ids = array();
		$_terms = array();
		foreach ( $terms as $term ) {
			if ( in_array( $term->term_taxonomy_id, $_tt_ids ) ) {
				continue;
			}

			$_tt_ids[] = $term->term_taxonomy_id;
			$_terms[] = $term;
		}
		$terms = $_terms;
	} elseif ( ! $objects ) {
		$terms = array_values( array_unique( $terms ) );
	}

	return $terms;
}


 /**
   * Google nákupy metody
   * je potřeba volat Namespace g
   *
   */           

          
  /**
   * Create child NAMESPACE data element for variantion
   * With global option
   *      
   * since 2.1.1
   */        
  public function child_variable_nsdata($string1,$string2,$meta,$meta_name,$dom,$name){
  
    if(!empty($string1) && trim($string1)!=''){
      $text = $string1;
    }elseif(!empty($meta[$meta_name][0]) && trim($meta[$meta_name][0])!=''){
      $text = $meta[$meta_name][0];
    }else{
      $text = $string2;
    }
    
    $dom->writeElementNS('g', $name, null, $text);
    
    
  }
  
  /**
   * Create child NAMESPACE data element for variantion
   * without global option 
   *     
   * since 2.1.1
   */        
  public function child_variable_simple_nsdata($string,$meta,$meta_name,$dom,$name){
  
    if(!empty($string) && trim($string)!=''){
    
      $dom->writeElementNS('g', $name, null, $string);
      
    }elseif(!empty($meta[$meta_name][0]) && trim($meta[$meta_name][0])!=''){
    
      $dom->writeElementNS('g', $name, null, $meta[$meta_name][0]);
    
    }
    
  }
  
  /**
   * Get variation imgurl
   * since 1.0.0
   */        
  public function ns_variation_img_url($varsid,$postid,$dom,$name){
  
     $img = wp_get_attachment_image_src( get_post_thumbnail_id($varsid), 'shop_single' ); 
        if(!empty($img[0])){
    
          $dom->writeElementNS('g', $name, null, $img[0]);
          
        }else{
          $img = wp_get_attachment_image_src( get_post_thumbnail_id($postid), 'shop_single' ); 
          if(!empty($img[0])){
    
            $dom->writeElementNS('g', $name, null, $img[0]);
             
          }
        }
  
  } 
  /** 
   * Get variation Heureka image alternative for variations and Google nákupy
   * since 1.1.0
   */        
  public function variation_google_image_alternative($varid,$dom){ 
  
    $img_var = get_post_meta( $varid, '_variation_imgurl_alternative' );
    if(!empty($img_var)){
    $count = count($img_var[0]);
      if($count > 0){
        $met = $img_var[0];
        foreach($met as $item){
          if(trim($item)!=''){
            $dom->writeElementNS('g', 'additional_image_link', null, $item);
          }
        }
      }
    }
  }
  
  /**
   * Create g:namespace element with control post meta exist 
   * if post meta empty, element not exist   
   * since 2.1.1
   */         
  public function ns_cdata_only_meta_element($meta_name,$meta,$dom,$name){
     
    if(!empty($meta[$meta_name][0])){
      $dom->writeElementNS('g', $name, null, $meta[$meta_name][0]);
    }  
  } 
  
    /**
   * Create g:namespace element with control post meta exist and global data exits
   * since 2.1.1
   */         
  public function ns_data_meta_element($meta_name,$meta,$option,$dom,$name){
     
    if(!empty($meta[$meta_name][0])){
    
      $dom->writeElementNS('g', $name, null, $meta[$meta_name][0]);
    
    }else{
    
      $global = $option;
      if(!empty($global)){
        $dom->writeElementNS('g', $name, null, $global);
      }
    
    }
  
  }



  /**
   * Get simple Google category
   * function is only for Google   
   * since 1.1.0
   */         
  public function simple_google_category($meta,$postid,$dom,$name){
  
    if(!empty($meta['google_category'][0]) && $meta['google_category'][0]!='default' ){
    $terms = wc_get_product_terms($postid,'product_cat',array('orderby' => 'parent','fields'=>'ids'));
    
    $dom->writeElementNS('g', 'google_product_category', null, $this->google_categories[$meta['google_category'][0]]['category_fullname']);
    
    }else{
    
    $terms = wc_get_product_terms($postid,'product_cat',array('orderby' => 'parent','fields'=>'ids'));     
    
    $dom->writeElementNS('g', 'google_product_category', null, $this->google_categories[$this->google_assing_categories[$terms[0]]]['category_fullname']);
    
    }
  
  } 
  
  /**
   * Get variation Google category
   * function is only for Google   
   * since 1.1.0
   */         
  public function variation_google_category($meta,$varid,$postid,$dom){
  
    $cat_var = get_post_meta( $varid, '_variation_google_category' );
    if(!empty($cat_var[0]) && $cat_var[0] != 'default' ){
    
      $dom->writeElementNS('g', 'google_product_category', null, $cat_var[0]);
    
    }else{
      if(!empty($meta['google_category'][0]) && $meta['google_category'][0]!='default'){
    
        $dom->writeElementNS('g', 'google_product_category', null, $this->google_categories[$meta['google_category'][0]]['category_fullname']);
        
      }else{
        $terms = wc_get_product_terms($postid,'product_cat',array('orderby' => 'parent','fields'=>'ids'));
    
        $dom->writeElementNS('g', 'google_product_category', null, $this->google_categories[$this->google_assing_categories[$terms[0]]]['category_fullname']);
     
      }
    }
  
  }
  
  /**
   * Create g:namespace simple data element for simple product
   * since 1.1.0
   */        
  public function create_child_ns_element($string,$dom,$name){
  
    if(!empty($string) && trim($string)!=''){
      
      $dom->writeElementNS('g', $name, null, $string);
    
    }
    
  }
  
  /**
   * Create g:namespace child data element for simple product
   * since 1.1.0
   */        
  public function child_ns_data($string1,$string2,$dom,$name){
  
    if(!empty($string1) && trim($string1)!=''){
      
      $dom->writeElementNS('g', $name, null, $string1);
    
    }else{
    
      $dom->writeElementNS('g', $name, null, $string2);
      
    }
    
  }


  /**
   * 123 N�kup
   *
   */        
/**
   * Get variation 123 N�kup category
   * function is only for 123 N�kup   
   * since 2.0.4
   */         
  public function variation_nakup_category($meta,$varid,$postid,$dom){
  
    $cat_var = get_post_meta( $varid, '_variation_123_nakup_category' );
    if(!empty($cat_var[0]) && $cat_var[0] != 'default' ){
    
      $cats = explode($cat_var[0]);
      if(!is_array($cats)){
          $dom->startElement('CATEGORYTEXT');
          $dom->writeCData($cat_var[0]);
          $dom->endElement();
      }else{
        foreach($cats as $item){
          $dom->startElement('CATEGORYTEXT');
          $dom->writeCData($item);
          $dom->endElement();
        }
      
      }
    
    }else{
      if(!empty($meta['123_nakup_category'][0]) && $meta['123_nakup_category'][0]!='default'){
    
        $cats = explode($meta['123_nakup_category'][0]);
      if(!is_array($cats)){
          $dom->startElement('CATEGORYTEXT');
          $dom->writeCData($meta['123_nakup_category'][0]);
          $dom->endElement();
      }else{
        foreach($cats as $item){
          $dom->startElement('CATEGORYTEXT');
          $dom->writeCData($item);
          $dom->endElement();
        }
      }  
      }else{
        $terms = wc_get_product_terms($postid,'product_cat',array('orderby' => 'parent','fields'=>'ids'));
        
          if(!empty($this->nakup_assing_categories[$terms[0]]) && $this->nakup_assing_categories[$terms[0]] != 'default'){
            $dom->startElement('CATEGORYTEXT');
            $dom->writeCData($this->nakup_assing_categories[$terms[0]]);
            $dom->endElement();
          }
     
      }
    }
  
  } 
  
    /**
   * Get simple 123 N�kup category
   * function is only for 123 N�kup   
   * since 1.1.0
   */         
  public function simple_nakup_category($meta,$postid,$dom,$name){
  
  
    if(!empty($meta['123_nakup_category'][0]) && $meta['123_nakup_category'][0]!='default' ){
      //CATEGORYTEXT
      $cats = explode($meta['123_nakup_category'][0]);
      if(!is_array($cats)){
          $dom->startElement('CATEGORYTEXT');
          $dom->writeCData($meta['123_nakup_category'][0]);
          $dom->endElement();
      }else{
        foreach($cats as $item){
          $dom->startElement('CATEGORYTEXT');
          $dom->writeCData($item);
          $dom->endElement();
        }
      }  
    
    }else{
    
        $terms = wc_get_product_terms($postid,'product_cat',array('orderby' => 'parent','fields'=>'ids'));
        
          if(!empty($this->nakup_assing_categories[$terms[0]]) && $this->nakup_assing_categories[$terms[0]] != 'default'){
            $dom->startElement('CATEGORYTEXT');
            $dom->writeCData($this->nakup_assing_categories[$terms[0]]);
            $dom->endElement();
          }
    
    
    }
    
  } 

   /**
   * Get global Delivery 
   * function is only for 123 N�kup
   * since 1.1.0
   */
   public function nakup_global_delivery($dom){
   
      if(!empty($this->nakup_delivery)){
       foreach ( $this->nakup_delivery as $item ) :
    
         $dom->startElement('DELIVERY');
         
          //DELIVERY_ID
          $dom->startElement('DELIVERY_ID');
          $dom->text($item['delivery_name']);
          $dom->endElement();
          //DELIVERY_PRICE
          $dom->startElement('DELIVERY_PRICE');
          $dom->text($item['delivery_price']);
          $dom->endElement();
          
          
          $dom->endElement();
         
         
       endforeach;
    
    }
    
   }



}//End class
?>