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

    global $post;
    global $woocommerce;
    $xmldir =  dirname(__FILE__);
    file_put_contents($xmldir.'/xml/123-nakup.xml', '');

$i = 1;
update_option('active_feed','123-nakup');


$feed = new Create_Dom();
$dom = $feed->dom_document;
$dom->openMemory();
$dom->startDocument( '1.0', 'utf-8' );   

$dom->startElement('SHOP');



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

$show_item = true;
if($show_item){




$var_product = new WC_Product($vars);
$test = new WC_Product_Variable($vars);

//SHOPITEM
$dom->startElement( 'SHOPITEM' );

//ITEM_ID
$feed->create_child_text_element($vars->ID,$dom,'ITEM_ID' );

//EAN
$ean = $feed->simple_cdata_only_meta_element('_variation_ean',$meta,$dom,'EAN');

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

//MANUFACTURER
$manufacturer = $feed->simple_cdata_meta_element('manufacturer',$meta,$feed->manufacturer_global,$dom,'MANUFACTURER');

//URL
$link = get_permalink($product_item->ID);
$url = $feed->create_child_text_element($link.'?varianta='.$vars->ID,$dom,'URL');

//IMGURL
$img = $feed->create_variation_img_url($vars->ID,$product_item->ID,$dom,'IMGURL');
    
//IMGURL_ALTERNATIVE
$img_var = $feed->variation_heureka_image_alternative($vars->ID,$dom);    
    
//VIDEO_URL
$video_url = $feed->simple_cdata_only_postmeta_element('_variation_video_url',$vars->ID,$dom,'VIDEO_URL');     

//CATEGORYTEXT
$categorytext = $feed->variation_nakup_category($meta,$vars->ID,$product_item->ID,$dom); 
          
//PRICEVAT
$pricevat = $feed->create_child_cdata_element($var_product->get_price(),$dom,'PRICE_VAT');
//PRICE_TYPE
if(!empty($meta['price_type'][0])){
  $feed->create_child_cdata_element($meta['price_type'][0],$dom,'PRICE_TYPE');      
}
    
//IS_HANDMATE   
if(!empty($meta['is_handmate'][0]) && $meta['is_handmate'][0] == 1){
  $dom->startElement('IS_HANDMADE');
  $dom->text('1');
  $dom->endElement();
}else{
  $dom->startElement('IS_HANDMADE');
  $dom->text('0');
  $dom->endElement();
}   

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
$delivery = $feed->nakup_global_delivery($dom);        

//PARAMS          
$params = $feed->variation_heureka_params($vars->ID,$product_item->ID,$dom);    
   
//SEO_TITLE
$feed->simple_cdata_only_postmeta_element('_variation_seo_title',$vars->ID,$dom,'SEO_TITLE');

//SEO_KEYWORDS
$feed->simple_cdata_only_postmeta_element('_variation_seo_keywords',$vars->ID,$dom,'SEO_KEYWORDS');

//SEO_DESCRIPTION
$feed->simple_cdata_only_postmeta_element('_variation_seo_description',$vars->ID,$dom,'SEO_DESCRIPTION');   
   



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


$show_item = true;
if($show_item){

//SHOPITEM
$dom->startElement( 'SHOPITEM' );

//ITEM_ID
$itemid = $feed->create_child_text_element($product_item->ID,$dom,'ITEM_ID' );

//EAN
$ean = $feed->simple_cdata_only_meta_element('_ean',$meta,$dom,'EAN');
    
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

//MANUFACTURER
$manufacturer = $feed->simple_cdata_meta_element('manufacturer',$meta,$feed->manufacturer_global,$dom,'MANUFACTURER'); 
    
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

//CATEGORYTEXT
$categorytext = $feed->simple_nakup_category($meta,$product_item->ID,$dom,'CATEGORYTEXT');


//PRICEVAT
$pricevat = $feed->create_child_cdata_element($my_product->get_price(),$dom,'PRICE_VAT');    

//PRICE_TYPE
if(!empty($meta['price_type'][0])){
  $feed->create_child_cdata_element($meta['price_type'][0],$dom,'PRICE_TYPE');      
}
//IS_HANDMATE   
if(!empty($meta['is_handmate'][0]) && $meta['is_handmate'][0] == 1){
  $dom->startElement('IS_HANDMADE');
  $dom->text('1');
  $dom->endElement();
}else{
  $dom->startElement('IS_HANDMADE');
  $dom->text('0');
  $dom->endElement();
}    

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
$delivery = $feed->nakup_global_delivery($dom); 
        
//PARAMS
$params = $feed->simple_heureka_params($my_product,$dom);    
 
//SEO_TITLE
if(!empty($meta['seo_title'][0])){
  $video_url = $feed->create_child_cdata_element($meta['seo_title'][0],$dom,'SEO_TITLE');      
}

//SEO_KEYWORDS
if(!empty($meta['seo_keywords'][0])){
  $video_url = $feed->create_child_cdata_element($meta['seo_keywords'][0],$dom,'SEO_KEYWORDS');      
}

//SEO_DESCRIPTION
if(!empty($meta['seo_description'][0])){
  $video_url = $feed->create_child_cdata_element($meta['seo_description'][0],$dom,'SEO_DESCRIPTION');      
} 
    
//End SHOPITEM
$dom->endElement();

  if($i == 100){
      file_put_contents($xmldir.'/xml/123-nakup.xml', $dom->flush(true), FILE_APPEND); 
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

file_put_contents($xmldir.'/xml/123-nakup.xml', $dom->flush(true), FILE_APPEND);                                 

header("Content-Type: text/html");                                   

?>