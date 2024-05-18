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

$najnakup_shipping = get_option( 'woo_najnakup_delivery');
$najnakup_availability = get_option( 'woo_najnakup_availability');

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

$xmldir =  dirname(__FILE__);
file_put_contents($xmldir.'/xml/najnakup.xml', '');  
  
update_option('active_feed','najnakup');                              
$feed = new Create_Dom();
$dom = $feed->dom_document;
$dom->openMemory();
$dom->startDocument( '1.0', 'utf-8' );    
//SHOP
$dom->startElement( 'SHOP' );



global $post;
global $woocommerce;

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
//var_dump($product_item);
//Kontrola vyloučené kategorie
$check_terms = wc_get_product_terms($product_item->ID,'product_cat',array('orderby' => 'parent','fields'=>'ids'));

if(!empty($check_terms) && empty($feed->najnakup_excluded_categories[$check_terms[0]])){






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


//SHOPITEM
$dom->startElement( 'SHOPITEM' );

//Product id
$itemid = $feed->create_child_text_element($vars->ID,$dom,'CODE' );

//name
//Smae meta for all
$h_title = get_post_meta( $vars->ID, '_variation_heureka_title',true );
if(!empty($meta['manufacturer'][0])){
  $text = $meta['manufacturer'][0].' ';
}else{
  $text = '';
}
$default = $text.$all_title.' '.$vars->ID;
$productname = $feed->child_variable_cdata($h_title,$default,$meta,'custom_product_title',$dom,'NAME');    

//Přidat výběr description a excerpt  
//description
if( $feed->use_excerpt == 'excerpt' ){
  $content = $product_item->post_excerpt;
}else{
  $content = $product_item->post_content;
}
$description = $feed->create_child_cdata_element($content,$dom,'DESCRIPTION');

//url
$link = get_permalink($product_item->ID);
$url = $feed->create_child_text_element($link.'?varianta='.$vars->ID,$dom,'PRODUCT_URL');

//picture
$img = $feed->create_variation_img_url($vars->ID,$product_item->ID,$dom,'IMAGE_URL');
    
//price
$pricevat = $feed->create_child_cdata_element($var_product->get_price(),$dom,'PRICE');
   
//manufacturer
$manufacturer = $feed->simple_cdata_meta_element('manufacturer',$meta,$feed->manufacturer_global,$dom,'MANUFACTURER');     


//category
$kat_id = $feed->get_lowest_category($product_item->ID);
//$categorytext = $feed->get_variation_najnakup_category($kat_id,$vars->ID,$meta,$dom); 
$term = get_term( $kat_id, 'product_cat');
if(!empty($term->parent)):
   $string = $term->name;
   //var_dump($string);
   //var_dump($term->parent);
   $kategorie = get_term_road($term->parent,$string);
else:
   $kategorie = $term->name;
endif; 
    //var_dump($kategorie);
    //CATEGORYTEXT
    $dom->startElement('CATEGORY');
    $dom->text($kategorie);
    $dom->endElement();


    
//ean
$ean = $feed->simple_cdata_only_meta_element('_ean',$meta,$dom,'EAN');   

//shipping
$shipping = $feed->simple_cdata_meta_element('najnakup_shipping',$meta,$feed->najnakup_shipping,$dom,'SHIPPING');



//AVAILABILITY
    if(!empty($najnakup_availability)){
      $dom->startElement('AVAILABILITY');
      $dom->writeCData($najnakup_availability);
      $dom->endElement();
    }    
    
//param    
//$params = $feed->simple_najnakup_params($my_product,$dom);
if($product->has_attributes()){

    $attributes = $product->get_attributes();
    
    foreach ( $attributes as $attribute ) :
		
		$att_label =  wc_attribute_label( $attribute['name'] ); 
		
    		if ( $attribute['is_taxonomy'] ) {
	            $values = wc_get_product_terms( $product->id, $attribute['name'], array( 'fields' => 'names' ) );
    		} else {
    			$values = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
		     }
         
        foreach ( $values as $value ) :
     
          //PARAM
          $dom->startElement('PARAM');
          
          //PARAM_NAME
          $dom->startElement('PARAM_NAME');
          $dom->text($att_label);
          $dom->endElement();
          
          //VAL
          $dom->startElement('VAL');
          $dom->text($value);
          $dom->endElement();

          $dom->endElement();

        endforeach;    
         
	   endforeach; 
}   

   
//End SHOPITEM
$dom->endElement();

}



}else{   

//Produkt bez variant   

$my_product = new WC_Product($product_item->ID);

//Select all post meta
$meta = get_post_meta($product_item->ID);


//SHOPITEM
$dom->startElement( 'SHOPITEM' );

//Product id
$itemid = $feed->create_child_text_element($product_item->ID,$dom,'CODE' );

//name    
$default = $product_item->post_title;
if(!empty($meta['custom_product_title'][0])){ $title = $meta['custom_product_title'][0]; }else{ $title = ''; }
$productname = $feed->child_simple_cdata($title,$default,$dom,'NAME');


//description
if( $feed->use_excerpt == 'excerpt' ){
  $content = $product_item->post_excerpt;
}else{
  $content = $product_item->post_content;
}
$description = $feed->create_child_cdata_element($content,$dom,'DESCRIPTION');

//url
$url = $feed->create_child_cdata_element(get_permalink($product_item->ID),$dom,'PRODUCT_URL' );   
    
//picture
$img = wp_get_attachment_image_src( get_post_thumbnail_id($product_item->ID), 'shop_single' ); 
if(!empty($img[0])){
$imgurl = $feed->create_child_cdata_element($img[0],$dom,'IMAGE_URL');
}
    
//price
$pricevat = $feed->create_child_cdata_element($my_product->get_price(),$dom,'PRICE');
    
//manufacturer
$manufacturer = $feed->simple_cdata_meta_element('manufacturer',$meta,$feed->manufacturer_global,$dom,'MANUFACTURER'); 
 
 
//upravit    
//category
$kat_id = $feed->get_lowest_category($product_item->ID);
//$categorytext = $feed->get_simple_najnakup_category($kat_id,$meta,$dom);
$term = get_term( $kat_id, 'product_cat');

$string = $term->name;

if($term->parent!=0){

for($c=1; $c<10; $c++){
  if(!$f){
$term = get_term( $term->parent, 'product_cat');

    if($term->parent!=0){
      
      $string = $term->name.' > '.$string;
    
    }else{
      $kategorie = $term->name.' > '.$string;
      $f = true;
    }
  }
}
  //$kategorie = $string;
}else{
  $kategorie = $term->name;
}
//echo '<p>'.$kategorie.'</p>';

unset($term);
unset($parent);
unset($child);
unset($string);

$f=false;

    
 
    //CATEGORYTEXT
    $dom->startElement( 'CATEGORY' );
    $dom->writeCData( $kategorie );
    $dom->endElement();

unset($kategorie);








//ean
$ean = $feed->simple_cdata_only_meta_element('_ean',$meta,$dom,'EAN');


//shipping
$shipping = $feed->simple_cdata_meta_element('najnakup_shipping',$meta,$feed->pricemania_shipping,$dom,'SHIPPING');

    //AVAILABILITY
    if(!empty($najnakup_availability)){
      $dom->startElement('AVAILABILITY');
      $dom->writeCData($najnakup_availability);
      $dom->endElement();
    }



//param    
//$params = $feed->simple_najnakup_params($my_product,$dom);
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
          $dom->text($att_label);
          $dom->endElement();
          
          //VAL
          $dom->startElement('VAL');
          $dom->text($value);
          $dom->endElement();

          $dom->endElement();

        endforeach;    
         
	   endforeach; 
    
    }


//End SHOPITEM
$dom->endElement();
  
  if($i == 200){
      file_put_contents($xmldir.'/xml/najnakup.xml', $dom->flush(true), FILE_APPEND); 
      $i = 0;
  } 
  $i++; 
    

    
    }//Konec podminky pro varianty
  }//Konec kontroly vyloučených kategorií  
    
  }
    

//end SHOP
$dom->endElement();

file_put_contents($xmldir.'/xml/najnakup.xml', $dom->flush(true), FILE_APPEND);                                   

header("Content-Type: text/html");                                   
                                   

?>