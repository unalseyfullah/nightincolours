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

/**
 * Get term road 
 * since 1.1.0
 */  
function get_term_road($term_id,$string){
  $item = get_term( $term_id, 'product_cat');
   $string = $item->name.' > '.$string;
   if(empty($item->parent)){
   $finale = $string;

   }
  if(!empty($item->parent)):
   $item = get_term_road($item->parent,$string);
  endif;
  
  return $finale;
    
}

global $post;
global $woocommerce;
$xmldir =  dirname(__FILE__);
file_put_contents($xmldir.'/xml/pricemania-sk.xml', '');        

/*
$licence_status = get_option('wooshop-xml-feeds-licence');
  if ( empty( $licence_status ) ) {
    return false;
  } 
*/  
  
update_option('active_feed','pricemania');                              
$feed = new Create_Dom();
$dom = $feed->dom_document;
$dom->openMemory();
$dom->startDocument( '1.0', 'utf-8' );    
//products
$dom->startElement( 'products' );


//Display all active products    
$args = array( 
      'post_type' => 'product', 
      'posts_per_page' => -1, 
      'post_status' => 'publish' 
);

//$wp_query = new WP_Query( $args );

//while ( $wp_query->have_posts() ) : $wp_query->the_post();  


global $wpdb;
$products = $wpdb->get_results( 
	 "SELECT * FROM ".$wpdb->prefix."posts WHERE post_status='publish' AND post_type='product'"
    );

$i = 1;
foreach($products as $product_item){  

//Kontrola vyloučené kategorie
$check_terms = wc_get_product_terms($product_item->ID,'product_cat',array('orderby' => 'parent','fields'=>'ids'));

if(!empty($check_terms) && empty($feed->pricemania_excluded_categories[$check_terms[0]])){






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

//Společná meta nadřazeného produktu
$meta = get_post_meta($product_item->ID);

$var_product = new WC_Product($vars);
$test = new WC_Product_Variable($vars);


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



//product
$dom->startElement( 'product' );

//Product id
$itemid = $feed->create_child_text_element($vars->ID,$dom,'id' );

//name
$h_title = get_post_meta( $vars->ID, '_variation_heureka_title',true );
if(!empty($meta['manufacturer'][0])){
  $text = $meta['manufacturer'][0].' ';
}else{
  $text = '';
}
$default = $text.$all_title.' '.$vars->ID;
$productname = $feed->child_variable_cdata($h_title,$default,$meta,'custom_product_title',$dom,'name');    

//Přidat výběr description a excerpt  
//description
if( $feed->use_excerpt == 'excerpt' ){
  $content = $product_item->post_excerpt;
}else{
  $content = $product_item->post_content;
}
$description = $feed->create_child_cdata_element($content,$dom,'description');

//url
$link = get_permalink($product_item->ID);
$url = $feed->create_child_text_element($link.'?varianta='.$vars->ID,$dom,'url');

//picture
$img = $feed->create_variation_img_url($vars->ID,$product_item->ID,$dom,'picture');
    
//price
$pricevat = $feed->create_child_cdata_element($var_product->get_price(),$dom,'price');
   
//manufacturer
$manufacturer = $feed->simple_cdata_meta_element('manufacturer',$meta,$feed->manufacturer_global,$dom,'manufacturer');     


//category
$kat_id = $feed->get_lowest_category($product_item->ID);

$categorytext = $feed->get_variation_pricemania_category($kat_id,$vars->ID,$meta,$dom); 


    
//ean
$ean = $feed->simple_cdata_only_meta_element('_ean',$meta,$dom,'ean');   


//shipping
$shipping = $feed->simple_cdata_meta_element('pricemania_shipping',$meta,$feed->pricemania_shipping,$dom,'shipping');

//availability
$stock = get_post_meta( $vars->ID, '_stock', true );
if(empty($stock) || $stock < '1' ){
  $delivery_date = $feed->variation_delivery_date($vars->ID,$meta,$dom,'availability');    
}else{
  $dom->startElement('availability');
  $dom->text('0');
  $dom->endElement();
}    
    
    
//params          
$params = $feed->variation_pricemania_params($vars->ID,$product_item->ID,$dom);    
   

//End SHOPITEM
$dom->endElement();   

  }
}



}else{   



//Produkt bez variant   

$my_product = new WC_Product($product_item->ID);

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


//product
$dom->startElement( 'product' );

//Product id
$itemid = $feed->create_child_text_element($product_item->ID,$dom,'id' );

//name    
$default = $product_item->post_title;
if(!empty($meta['custom_product_title'][0])){ $title = $meta['custom_product_title'][0]; }else{ $title = ''; }
$productname = $feed->child_simple_cdata($title,$default,$dom,'name');


//description
if( $feed->use_excerpt == 'excerpt' ){
  $content = $product_item->post_excerpt;
}else{
  $content = $product_item->post_content;
}
$description = $feed->create_child_cdata_element($content,$dom,'description');

//url
$url = $feed->create_child_cdata_element(get_permalink($product_item->ID),$dom,'url' );   
    
//picture
$img = wp_get_attachment_image_src( get_post_thumbnail_id($product_item->ID), 'shop_single' ); 
if(!empty($img[0])){
$imgurl = $feed->create_child_cdata_element($img[0],$dom,'picture');
}
    
//price
$pricevat = $feed->create_child_cdata_element($my_product->get_price(),$dom,'price');
    
//manufacturer
$manufacturer = $feed->simple_cdata_meta_element('manufacturer',$meta,$feed->manufacturer_global,$dom,'manufacturer'); 
 
 
//upravit    
//category
$kat_id = $feed->get_lowest_category($product_item->ID);
$categorytext = $feed->get_simple_pricemania_category($kat_id,$meta,$dom);

//ean
$ean = $feed->simple_cdata_only_meta_element('_ean',$meta,$dom,'ean');


//shipping
$shipping = $feed->simple_cdata_meta_element('pricemania_shipping',$meta,$feed->pricemania_shipping,$dom,'shipping');

//availability
$stock = get_post_meta( $product_item->ID, '_stock', true );
if(empty($stock) || $stock < '1' ){
  $delivery_date = $feed->global_delivery_date($meta,$dom,'availability');    
}else{
  $dom->startElement('availability');
  $dom->text('0');
  $dom->endElement();
}  


//param    
$params = $feed->simple_pricemania_params($my_product,$dom);


//End SHOPITEM
$dom->endElement();
  
  if($i == 200){
      file_put_contents($xmldir.'/xml/pricemania-sk.xml', $dom->flush(true), FILE_APPEND); 
      $i = 0;
  } 
  $i++; 
  
    
      }    

    
    }//Konec podminky pro varianty
  }//Konec kontroly vyloučených kategorií  
    
  //endwhile;
  
  }
    

//end SHOP
$dom->endElement();

file_put_contents($xmldir.'/xml/pricemania-sk.xml', $dom->flush(true), FILE_APPEND);                                   

header("Content-Type: text/html");                                    
