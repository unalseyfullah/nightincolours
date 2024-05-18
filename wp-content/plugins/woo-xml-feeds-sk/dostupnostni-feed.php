<?php
 
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

$dir =  dirname(dirname(dirname(dirname(__FILE__))));
require_once($dir.'/wp-load.php');   
require_once($dir.'/wp-includes/option.php');

/*
$licence_status = get_option('wooshop-xml-feeds-licence');
  if ( empty( $licence_status ) ) {
    return false;
  } 
*/

$i=1;

$dom = new DOMDocument("1.0", "UTF-8");

$root = $dom->createElement('item_list');
$dom->appendChild($root);

ini_set('max_execution_time', 0);

    global $post;
    global $woocommerce;
    
$heureka_doba_doruceni = get_option( 'delivery_date');
$deadline_time = get_option( 'deadline_time' );
$delivery_time = get_option( 'delivery_time' );    

//Display all active products    
$args = array( 
      'post_type' => 'product', 
      'posts_per_page' => -1, 
      'post_status' => 'publish' 
);

$products = $wpdb->get_results( 
	 "SELECT * FROM ".$wpdb->prefix."posts WHERE post_status='publish' AND post_type='product'"
    );

    foreach($products as $product_item){

//$wp_query = new WP_Query( $args );
//while ( $wp_query->have_posts() ) : $wp_query->the_post();  

//Kontrola vylou en  kategorie
$check_terms = wc_get_product_terms($product_item->ID,'product_cat',array('orderby' => 'parent','fields'=>'ids'));


if(!empty($check_terms) && empty($heureka_excluded_categories[$check_terms[0]])){


/* Product variants */
/* Get product children */
$args = array(
	'post_parent' => $product_item->ID,
	'post_type'   => 'product_variation', 
	'numberposts' => -1,
	'post_status' => 'publish' ); 
$variations_array = get_children( $args );



/**
 *
 *   ást generován  feedu pro varianty
 *
 */   

/* Produkt variants exist */  
if(!empty($variations_array)){ 


//Spole n˜ titulek
$all_title = $product_item->post_title;

foreach($variations_array as $vars){

$var_product = new WC_Product($vars);
$test = new WC_Product_Variable($vars);

//Spole ná meta nad°azen ho produktu
$meta = get_post_meta($product_item->ID);

    /* Control stock number */
$stock = get_post_meta( $vars->ID, '_stock', true );    
    if( !empty($stock) && $stock > '0' ){

$stock = round($stock);

//SHOP ITEM
$node = $dom->createElement('item');
$node->setAttribute('id',$vars->ID);
$root->appendChild($node);
    
    //Stock qauntity
    $stock_quantity = $dom->createElement('stock_quantity');
    $node->appendChild($stock_quantity);
    $text = $dom->createTextNode($stock);
    $stock_quantity->appendChild($text);
    

//Set deadline by meta or global setting
if( !empty($meta['product_deadline_time'][0]) ){
    $deadline = $meta['product_deadline_time'][0];
}else{
    $deadline = $deadline_time;
}

//Set delivery by meta or global setting
if( !empty($meta['delivery_time'][0]) ){
    $delivery = $meta['delivery_time'][0];
}else{
    $delivery = $delivery_time;
}



date_default_timezone_set('Europe/Prague');
$today = date('Y-m-d');
$time  = strtotime(date('H:i'));
$deadlines = strtotime($deadline);

if($time > $deadlines){
  $delivery_date = date('Y-m-d', strtotime($today. ' + 1 days'));
}else{
  $delivery_date = $today;
}
$correct_delivery_time = $delivery_date.' '.$deadline_time;

$correct_deadline = date('Y-m-d', strtotime($delivery_date. ' + '.$delivery_time.' days')).' '.$deadline;
    //delivery
    $d = $dom->createElement('delivery_time');
    $node->appendChild($d);
    $text = $dom->createTextNode($correct_deadline);    
    $d->setAttribute('orderDeadline',$correct_delivery_time);
    $d->appendChild($text);    
   
   
  }

}


}else{   

//Produkt bez variant   

$my_product = new WC_Product($product_item->ID);
//Select all post meta
$meta = get_post_meta($product_item->ID);
    /* Control stock number */
    if( !empty($meta['_stock'][0]) && $meta['_stock'][0] > '0' ){

    $stock = round($meta['_stock'][0]);

//SHOP ITEM
$node = $dom->createElement('item');
$node->setAttribute('id',$product_item->ID);
$root->appendChild($node);


    //Stock qauntity
    $stock_quantity = $dom->createElement('stock_quantity');
    $node->appendChild($stock_quantity);
    $text = $dom->createTextNode($stock);
    $stock_quantity->appendChild($text);
 
if( !empty($meta['product_deadline_time'][0]) ){
    $deadline = $meta['product_deadline_time'][0];
}else{
    $deadline = $deadline_time;
}

if( !empty($meta['delivery_time'][0]) ){
    $delivery = $meta['delivery_time'][0];
}else{
    $delivery = $delivery_time;
}

date_default_timezone_set('Europe/Prague');
$today = date('Y-m-d');
$time  = strtotime(date('H:i'));
$deadlines = strtotime($deadline);

if($time > $deadlines){
  $delivery_date = date('Y-m-d', strtotime($today. ' + 1 days'));
}else{
  $delivery_date = $today;
}

$correct_delivery_time = $delivery_date.' '.$deadline_time;

$correct_deadline = date('Y-m-d', strtotime($delivery_date. ' + '.$delivery_time.' days')).' '.$deadline;
    //delivery
    $d = $dom->createElement('delivery_time');
    $node->appendChild($d);
    $text = $dom->createTextNode($correct_deadline);    
    $d->setAttribute('orderDeadline',$correct_delivery_time);
    $d->appendChild($text);
    
    
     } 
  }

  }//Konec kontroly vylou en˜ch kategori   
    
    //endwhile;
  }  

$dom->formatOutput = true;

$xmldir =  dirname(__FILE__);
$dom->save($xmldir.'/xml/dostupnostni-feed.xml');
                                   

header("Content-Type: text/html");                                   

?>