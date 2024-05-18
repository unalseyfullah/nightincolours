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

//Set namespace url
$nsUrl = 'http://base.google.com/ns/1.0';


global $post;
global $woocommerce;
$xmldir =  dirname(__FILE__);
file_put_contents($xmldir.'/xml/google.xml', '');        

$licence_status = get_option('wooshop-xml-feeds-licence');
  if ( empty( $licence_status ) ) {
    return false;
  } 
update_option('active_feed','google');

$google_jmeno_eshopu = get_option( 'google-jmeno-eshopu' );
if(empty($google_jmeno_eshopu)){ $google_jmeno_eshopu = ''; }

$google_link_eshopu = get_option( 'google-link-eshopu' );
if(empty($google_link_eshopu)){ $google_link_eshopu = ''; }

$google_popis_eshopu = get_option( 'google-popis-eshopu' );
if(empty($google_popis_eshopu)){ $google_popis_eshopu = ''; }

                         
                         
$feed = new Create_Dom();
$dom = $feed->dom_document;
$dom->openMemory();
$dom->startDocument( '1.0', 'utf-8' );    

//rss
$dom->startElement('rss');
$dom->writeAttribute('version', '2.0');
$dom->writeAttribute('xmlns:g', 'http://base.google.com/ns/1.0');

//chanel
$dom->startElement( 'channel' );

    $dom->startElement( 'title' );
    $dom->text( $google_jmeno_eshopu );
    $dom->endElement();

    $dom->startElement( 'link' );
    $dom->text( $google_link_eshopu );
    $dom->endElement();    

    $dom->startElement( 'description' );
    $dom->text( $google_popis_eshopu );
    $dom->endElement();

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
if(!empty($check_terms) && empty($feed->google_excluded_categories[$check_terms[0]])){


$google_varianty = get_option( 'google-varianty' );
if(!empty($google_varianty) && $google_varianty == 'ok' ){ 

    include('includes/google-nakupy-simple.php');//Feed bez variant

}else{

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


$var_product = new WC_Product($vars);
$test = new WC_Product_Variable($vars);


//item
$dom->startElement( 'item' );

//id
//$dom->writeElementNS('g', 'id', null, $vars->ID);
$dom->writeElementNS('g', 'id', null, $product_item->ID);


//title
$h_title = get_post_meta( $vars->ID, '_variation_heureka_title',true );
$default = $all_title.' '.$vars->ID;
$productname = $feed->child_variable_nsdata($h_title,$default,$meta,'custom_product_title',$dom,'title');    


//description
if( $feed->use_excerpt == 'excerpt' ){
  $content = $product_item->post_excerpt;
}else{
  $content = $product_item->post_content;
}

$dom->writeElementNS('g', 'description', null, $content);

//link
$link = get_permalink($product_item->ID);
$dom->writeElementNS('g', 'link', null, $link.'?varianta='.$vars->ID);

//image_link
$img = $feed->ns_variation_img_url($vars->ID,$product_item->ID,$dom,'image_link');
    
//additional_image_link
$img_var = $feed->variation_google_image_alternative($vars->ID,$dom);    
  
//condition  
$dom->writeElementNS('g', 'condition', null, 'new');    
  
//availability
$stock = get_post_meta( $vars->ID, '_stock', true );
if(empty($stock) || $stock < '1' ){
  $dom->writeElementNS('g', 'availability', null, 'out of stock');    
}else{
  $dom->writeElementNS('g', 'availability', null, 'in stock');
}  
    
//price
$nproduct = new WC_Product($product_item->ID);
$price = $var_product->get_price();
if(!empty($price)){
  $dom->writeElementNS('g', 'price', null, $var_product->get_price());
}else{
  $dom->writeElementNS('g', 'price', null, $nproduct->get_price());    
}

   
//gtin - ean
$ean = get_post_meta( $vars->ID, '_variation_ean',true );
$feed->child_variable_simple_nsdata($ean,$meta,'_ean',$dom,'gtin');

//gtin - isbn
$isbn = get_post_meta( $vars->ID, '_variation_isbn',true );
$feed->child_variable_simple_nsdata($isbn,$meta,'_isbn',$dom,'gtin');
    
//brand
$feed->ns_data_meta_element('manufacturer',$meta,$feed->manufacturer_global,$dom,'brand');     

//mnp
$mnp = get_post_meta( $vars->ID, '_variation_google_mnp',true );
$feed->child_variable_simple_nsdata($ean,$meta,'google_mnp',$dom,'mpn');
    
//CATEGORYTEXT
$categorytext = $feed->variation_google_category($meta,$vars->ID,$product_item->ID,$dom);    
    
$i_e = get_post_meta( $vars->ID, '_variation_google_identifikator_exists',true );    
if(!empty($i_e) && $i_e == 'false'){
$dom->writeElementNS('g', 'identifier_exists', null, 'FALSE');
}   
$var_meta = get_post_meta( $vars->ID );    
if(!empty($var_meta['_variation_google_stitek_value_1'][0])){
$dom->writeElementNS('g', 'custom_label_0', null, $var_meta['_variation_google_stitek_value_1'][0]);
}
if(!empty($var_meta['_variation_google_stitek_value_2'][0])){
$dom->writeElementNS('g', 'custom_label_1', null, $var_meta['_variation_google_stitek_value_2'][0]);
}
if(!empty($var_meta['_variation_google_stitek_value_3'][0])){
$dom->writeElementNS('g', 'custom_label_2', null, $var_meta['_variation_google_stitek_value_3'][0]);
}
if(!empty($var_meta['_variation_google_stitek_value_4'][0])){
$dom->writeElementNS('g', 'custom_label_3', null, $var_meta['_variation_google_stitek_value_4'][0]);
}
if(!empty($var_meta['_variation_google_stitek_value_5'][0])){
$dom->writeElementNS('g', 'custom_label_4', null, $var_meta['_variation_google_stitek_value_5'][0]);
}
    
    
//item_group_id
//Dočasně potlačeno, nutno dodělat přiřazení podporovaných atributů    
//$dom->writeElementNS('g', 'item_group_id', null, $product_item->ID);   
   
//End item
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


//item
$dom->startElement( 'item' );

//id
$feed->create_child_ns_element($product_item->ID,$dom,'id' );



//title    
$default = $product_item->post_title;
if(!empty($meta['custom_product_title'][0])){ $title = $meta['custom_product_title'][0]; }else{ $title = ''; }
$feed->child_ns_data($title,$default,$dom,'title');


//description
if( $feed->use_excerpt == 'excerpt' ){
  $content = $product_item->post_excerpt;
}else{
  $content = $product_item->post_content;
}
$dom->writeElementNS('g', 'description', null, $content);


//link
$dom->writeElementNS('g', 'link', null, get_permalink($product_item->ID));    


$img = wp_get_attachment_image_src( get_post_thumbnail_id($product_item->ID), 'shop_single' ); 
if(!empty($img[0])){
//image_link
$dom->writeElementNS('g', 'image_link', null, $img[0]);
}
    
//additional_image_link
if(!empty($meta['imgurl_alternative'][0])){
  $met = unserialize($meta['imgurl_alternative'][0]);
    foreach($met as $item){
      if(trim($item!='')){
        $dom->writeElementNS('g', 'additional_image_link', null, $item);
      }  
    }
}
    
//condition  
$dom->writeElementNS('g', 'condition', null, 'new');    
    
    
//availability
$stock = get_post_meta( $product_item->ID, '_stock', true );
if(empty($stock) || $stock < '1' ){
  $dom->writeElementNS('g', 'availability', null, 'out of stock');    
}else{
  $dom->writeElementNS('g', 'availability', null, 'in stock');
}    

//price
$price = $my_product->get_price();
if(!empty($price)){
  $dom->writeElementNS('g', 'price', null, $my_product->get_price());
}


//gtin - ean
$feed->ns_cdata_only_meta_element('_ean',$meta,$dom,'gtin');

//gtin - isbn
$feed->ns_cdata_only_meta_element('_isbn',$meta,$dom,'gtin');

//mpn
$feed->ns_cdata_only_meta_element('google_mnp',$meta,$dom,'mpn');
    
//brand
$feed->ns_data_meta_element('manufacturer',$meta,$feed->manufacturer_global,$dom,'brand'); 

$feed->simple_google_category($meta,$product_item->ID,$dom,'google_product_category');    

if(!empty($meta['google_identifikator_exists'][0]) && $meta['google_identifikator_exists'][0] == 'false'){
$dom->writeElementNS('g', 'identifikator_exists', null, 'FALSE');
}

if(!empty($meta['google_stitek_value_1'][0])){
$dom->writeElementNS('g', 'custom_label_0', null, $meta['google_stitek_value_1'][0]);
}
if(!empty($meta['google_stitek_value_2'][0])){
$dom->writeElementNS('g', 'custom_label_1', null, $meta['google_stitek_value_2'][0]);
}
if(!empty($meta['google_stitek_value_3'][0])){
$dom->writeElementNS('g', 'custom_label_2', null, $meta['google_stitek_value_3'][0]);
}
if(!empty($meta['google_stitek_value_4'][0])){
$dom->writeElementNS('g', 'custom_label_3', null, $meta['google_stitek_value_4'][0]);
}
if(!empty($meta['google_stitek_value_5'][0])){
$dom->writeElementNS('g', 'custom_label_4', null, $meta['google_stitek_value_5'][0]);
}


//End item
$dom->endElement();
  
  if($i == 200){
      file_put_contents($xmldir.'/xml/google.xml', $dom->flush(true), FILE_APPEND); 
      $i = 0;
  } 
  $i++; 
  
          
      }
    
    }//Konec podminky pro varianty
    
   }//Zobrazení feedu s variantami 
    
  }//Konec kontroly vyloučených kategorií  
   
  
    
  }//Konec foreach    

//end chanel
$dom->endElement();
//end rss
$dom->endElement();

file_put_contents($xmldir.'/xml/google.xml', $dom->flush(true), FILE_APPEND);                                   

header("Content-Type: text/html");                                   

?>