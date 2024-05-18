<?php 

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
    
