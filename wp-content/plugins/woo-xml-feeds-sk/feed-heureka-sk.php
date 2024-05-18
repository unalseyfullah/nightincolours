<?php  
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
ini_set('max_execution_time', 0);

$dir =  dirname(dirname(dirname(dirname(__FILE__))));
require_once($dir.'/wp-load.php');   
require_once($dir.'/wp-includes/option.php');
require_once('includes/class-create-dom.php');

/*
$licence_status = get_option('wooshop-xml-feeds-licence');
  if ( empty( $licence_status ) ) {
    return false;
  } 

*/
    global $post;
    global $woocommerce;
    $xmldir =  dirname(__FILE__);
    file_put_contents($xmldir.'/xml/heureka-sk.xml', '');

$i = 1;
update_option('active_feed','heureka-sk');


$feed = new Create_Dom();
$dom = $feed->dom_document;
$dom->openMemory();
$dom->startDocument( '1.0', 'utf-8' );   

$dom->startElement('SHOP');




$heureka_kurz = $feed->heureka_kurz;
if(empty($heureka_kurz)){ $heureka_kurz = 28.11; }

//Display all active products    
$args = array( 
      'post_type' => 'product', 
      'posts_per_page' => -1, 
      'post_status' => 'publish'
);
global $wpdb;
$products = $wpdb->get_results( 
	 "SELECT * FROM ".$wpdb->prefix."posts WHERE post_status='publish' AND post_type='product'"
    );

$i = 1;
foreach($products as $product_item){   

//Kontrola vyloučené kategorie
$check_terms = wc_get_product_terms($product_item->ID,'product_cat',array('orderby' => 'parent','fields'=>'ids'));
if(!empty($check_terms) && empty($feed->heureka_excluded_categories[$check_terms[0]])){


/**
 *
 *  Část generování feedu pro varianty
 *
 */   

global $product;
$product = get_product($product_item->ID);
/* Produkt variants exist */  
if($product->product_type == 'variable'){


//Společný titulek
$all_title = $product_item->post_title;

/* Product variants */

/* Get product children */
$args = array(
	'post_parent' => $product_item->ID,
	'post_type'   => 'product_variation', 
	'numberposts' => -1,
	'post_status' => 'publish' ); 
$variations_array = get_children( $args );

foreach($variations_array as $vars){

$backorders = get_post_meta($vars->ID,'_backorders', true);
$in_stock   = get_post_meta($vars->ID,'_stock', true);
$show_product = true;
if(empty($backorders) || $backorders == 'no'){ $show_back = false; }else{ $show_back = true; }
if(empty($in_stock) || $in_stock == '0'){ $show_stock = false; }else{ $show_stock = true; }
if($show_stock == true ){ $show_product = true; }
elseif($show_stock == false && $show_back == true ){ $show_product = true; }else{
  $show_product = false;
}
if($show_product){

//Společná meta nadřazeného produktu
$meta = get_post_meta($product_item->ID);

$bazar = get_option( 'heureka_hide_bazar' );
$show_item = false;
if(!empty($meta['heureka_item_type'][0]) && $meta['heureka_item_type'][0] == 'bazar'){
  if(!empty($bazar) && $bazar == 'ne'){ 
    $show_item = true; 
  }

}else{ $show_item = true; }

if($show_item){




$var_product = new WC_Product($vars);
$test = new WC_Product_Variable($vars);

//SHOPITEM
$dom->startElement( 'SHOPITEM' );

//ITEM_ID
$feed->create_child_text_element($vars->ID,$dom,'ITEM_ID' );

//PRODUCT NAME
$h_title = get_post_meta( $vars->ID, '_variation_heureka_title',true );
$h_name = get_post_meta( $vars->ID, '_variation_heureka_name',true );

$default = $all_title.' '.$vars->ID;
$productname = $feed->child_variable_cdata($h_name,$default,$meta,'custom_product_name',$dom,'PRODUCTNAME');    

//PRODUCT   
$default = $all_title; 
$product = $feed->child_variable_cdata($h_title,$default,$meta,'custom_product_title',$dom,'PRODUCT'); 
    
//Přidat výběr description a excerpt  
//DESCRIPTION
if( $feed->use_excerpt == 'excerpt' ){
  $content = $product_item->post_excerpt;
}else{
  $content = $product_item->post_content;
}
$description = $feed->create_child_cdata_element($content,$dom,'DESCRIPTION');

//URL
$link = get_permalink($product_item->ID);
$url = $feed->create_child_text_element($link.'?varianta='.$vars->ID,$dom,'URL');

//IMGURL
$img = $feed->create_variation_img_url($vars->ID,$product_item->ID,$dom,'IMGURL');
    
//IMGURL_ALTERNATIVE
$img_var = $feed->variation_heureka_image_alternative($vars->ID,$dom);    
    
//VIDEO_URL
$video_url = $feed->simple_cdata_only_postmeta_element('_variation_video_url',$vars->ID,$dom,'VIDEO_URL');     
     
//PRICEVAT
$pricevat = $feed->create_child_cdata_element($var_product->get_price(),$dom,'PRICE_VAT');
    
//ITEM_TYPE
$item_type = $feed->simple_item_type($meta,$dom,'ITEM_TYPE');
    
//MANUFACTURER
$manufacturer = $feed->simple_cdata_meta_element('manufacturer',$meta,$feed->manufacturer_global,$dom,'MANUFACTURER');   
      
//CATEGORYTEXT
$categorytext = $feed->variation_heureka_category($meta,$vars->ID,$product_item->ID,$dom);    
    
//EAN
$ean = $feed->simple_cdata_only_meta_element('_variation_ean',$meta,$dom,'EAN');   

//ISBN
$ean = $feed->simple_cdata_only_meta_element('_variation_isbn',$meta,$dom,'ISBN'); 
    
//HEUREKA_CPC
$default = get_option('heureka-cpc_sk');
$v_cpc = get_post_meta( $vars->ID, '_variation_heureka_cpc_sk',true ); 
$heureka_cpc = $feed->child_variable_cdata($v_cpc,$default,$meta,'heureka_cpc',$dom,'HEUREKA_CPC');


//DELIVERY_DATE
$stock = get_post_meta( $vars->ID, '_stock', true );
if(empty($stock) || $stock < '1' ){
  $delivery_date = $feed->variation_delivery_date($vars->ID,$meta,$dom,'DELIVERY_DATE');    
}else{
  $dom->startElement('DELIVERY_DATE');
  $dom->text('0');
  $dom->endElement();
}    
    
//DELIVERY
$delivery = $feed->global_delivery($dom);        

//ACCESSORY
$accessory = $feed->simple_text_only_meta_element('accessory',$meta,$dom,'ACCESSORY');
//DUES
$dues = $feed->simple_text_only_meta_element('dues',$meta,$dom,'DUES');
    
//PARAMS          
$params = $feed->variation_heureka_params($vars->ID,$product_item->ID,$dom);    
   
//Custom PARAMS
$custom_params = $feed->simple_heureka_custom_params($product_item->ID,$dom);
    
//ITEMGROUP    
$itemgroup = $feed->create_child_text_element($product_item->ID,$dom,'ITEMGROUP_ID'); 


//End SHOPITEM
$dom->endElement();
      }
    }

}


}else{   

//Produkt bez variant   

$my_product = new WC_Product($product_item);

//Select all post meta
$meta = get_post_meta($product_item->ID);
 
$backorders = get_post_meta($product_item->ID,'_backorders', true);
$in_stock   = get_post_meta($product_item->ID,'_stock', true);
$show_product = true;
if(empty($backorders) || $backorders == 'no'){ $show_back = false; }else{ $show_back = true; }
if(empty($in_stock) || $in_stock == '0'){ $show_stock = false; }else{ $show_stock = true; }
if($show_stock == true ){ $show_product = true; }
elseif($show_stock == false && $show_back == true ){ $show_product = true; }else{
  $show_product = false;
}


if($show_product){


$bazar = get_option( 'heureka_hide_bazar' );
$show_item = false;
if(!empty($meta['heureka_item_type'][0]) && $meta['heureka_item_type'][0] == 'bazar'){
  if(!empty($bazar) && $bazar == 'ne'){ 
    $show_item = true; 
  }

}else{ $show_item = true; }

if($show_item){

//SHOPITEM
$dom->startElement( 'SHOPITEM' );

//ITEM_ID
$itemid = $feed->create_child_text_element($product_item->ID,$dom,'ITEM_ID' );
    
//PRODUCT NAME    
$default = $product_item->post_title;
if(!empty($meta['custom_product_name'][0])){ $title = $meta['custom_product_name'][0]; }else{ $title = ''; }
$productname = $feed->child_simple_cdata($title,$default,$dom,'PRODUCTNAME');

//PRODUCT
$default = $product_item->post_title;    
if(!empty($meta['custom_product_title'][0])){ $title = $meta['custom_product_title'][0]; }else{ $title = ''; }
$productname = $feed->child_simple_cdata($title,$default,$dom,'PRODUCT');

//DESCRIPTION
if( $feed->use_excerpt == 'excerpt' ){
  $content = $product_item->post_excerpt;
}else{
  $content = $product_item->post_content;
}
$description = $feed->create_child_cdata_element($content,$dom,'DESCRIPTION');
    
//URL
$url = $feed->create_child_cdata_element(get_permalink($product_item->ID),$dom,'URL' );  
    
$img = wp_get_attachment_image_src( get_post_thumbnail_id($product_item->ID), 'shop_single' ); 
if(!empty($img[0])){
//IMGURL
$imgurl = $feed->create_child_cdata_element($img[0],$dom,'IMGURL');
}
    
//IMGURL_ALTERNATIVE
if(!empty($meta['imgurl_alternative'][0])){
  $met = unserialize($meta['imgurl_alternative'][0]);
    foreach($met as $item){
      if(trim($item!='')){
        $imgurl_alternative = $feed->create_child_cdata_element($item,$dom,'IMGURL_ALTERNATIVE');
      }  
    }
}
    
//VIDEO_URL
if(!empty($meta['video_url'][0])){
  $video_url = $feed->create_child_cdata_element($meta['video_url'][0],$dom,'VIDEO_URL');      
}

//PRICEVAT
$pricevat = $feed->create_child_cdata_element($my_product->get_price(),$dom,'PRICE_VAT');    
    
//ITEM_TYPE
$item_type = $feed->simple_item_type($meta,$dom,'ITEM_TYPE');
    
//MANUFACTURER
$manufacturer = $feed->simple_cdata_meta_element('manufacturer',$meta,$feed->manufacturer_global,$dom,'MANUFACTURER'); 

//CATEGORYTEXT
$categorytext = $feed->simple_heureka_category($meta,$product_item->ID,$dom,'CATEGORYTEXT');    

//EAN
$ean = $feed->simple_cdata_only_meta_element('_ean',$meta,$dom,'EAN');

//ISBN
$isbn = $feed->simple_cdata_only_meta_element('_isbn',$meta,$dom,'ISBN');

//HEUREKA_CPC
$option_cpc = get_option('heureka-cpc_sk');

$heureka_cpc = $feed->simple_heureka_cpc($meta,$product_item->ID,$option_cpc,$dom,'HEUREKA_CPC');

//DELIVERY_DATE
$stock = get_post_meta( $product_item->ID, '_stock', true );
if(empty($stock) || $stock < '1' ){
  $delivery_date = $feed->global_delivery_date($meta,$dom,'DELIVERY_DATE');    
}else{
  $dom->startElement('DELIVERY_DATE');
  $dom->text('0');
  $dom->endElement();
}  
    
//DELIVERY
$delivery = $feed->global_delivery($dom); 
    
//ACCESSORY
$accessory = $feed->simple_text_only_meta_element('accessory',$meta,$dom,'ACCESSORY');

//DUES
$dues = $feed->simple_text_only_meta_element('dues',$meta,$dom,'DUES');  
    
//PARAMS
$params = $feed->simple_heureka_params($my_product,$dom);    
    
//Custom PARAMS
$custom_params = $feed->simple_heureka_custom_params($product_item->ID,$dom);

//End SHOPITEM
$dom->endElement();

  if($i == 100){
      file_put_contents($xmldir.'/xml/heureka-sk.xml', $dom->flush(true), FILE_APPEND); 
      $i = 0;
  } 
  $i++; 

         }
       }
     }//Konec podminky pro varianty 
  }//Konec kontroly vyloučených kategorií  
    
 }//End product loop
    

//end SHOP
$dom->endElement();

file_put_contents($xmldir.'/xml/heureka-sk.xml', $dom->flush(true), FILE_APPEND);                                 

header("Content-Type: text/html");                                   

?>