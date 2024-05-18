<?php
/**
 *
 * @package   heureka_xml
 * @author    Vladislav Musilek
 * @license   GPL-2.0+
 * @link      http://musilda.cz
 * @copyright 2014 Vladislav Musilek
 *
 * Version 1.0.0
 *  
 */
?>

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
  <p><?php _e('Control feed with 10 products, to Heureka category.','heureka-xml'); ?></p>
<?php 
if(isset($_GET['check'])){
ini_set('max_execution_time', 0);

    global $post;
    global $woocommerce;
    
$heureka_doba_doruceni     = get_option( 'woo_heureka_doba_doruceni');    
$heureka_assing_categories = get_option( 'woo_heureka_assing_categories');
$heureka_categories        = get_option( 'woo_heureka_categories');
$heureka_delivery          = get_option( 'woo_heureka_delivery');

//Display all active products    
$args = array( 
      'post_type'      => 'product', 
      'posts_per_page' => 10, 
      'post_status'    => 'publish' 
);
$wp_query = new WP_Query( $args );
while ( $wp_query->have_posts() ) : $wp_query->the_post();  
 

/* Product variants */
$args = array(
	'post_parent' => $wp_query->post->ID,
	'post_type'   => 'product_variation', 
	'numberposts' => -1,
	'post_status' => 'publish' ); 
$variations_array = get_children( $args );

if(!empty($variations_array)){ echo '<p>Produkt s variantami!</p>'; 


foreach($variations_array as $vars){

$var_product = new WC_Product($vars);
$test = new WC_Product_Variable($vars);
$l = new WC_Product_Variation($vars);
//var_dump($l->get_variation_attributes());
//Select all post meta
$meta = get_post_meta($wp_query->post->ID);
    /* Control stock status */
    if( $meta['_stock_status'][0] == 'instock' ){
    echo 'ITEM_ID '.$vars->ID.'<br/>';
    
     $h_title = get_post_meta( $vars->ID, '_variation_heureka_title' );
    if(!empty($h_title)){
      echo 'PRODUCTNAME '.$h_title[0].'<br/>';
    }else{
      if(!empty($meta['manufacturer'][0])){
        echo 'PRODUCTNAME '.$meta['manufacturer'][0].' | '.$vars->post_title.'<br/>';
      }else{
        echo 'PRODUCTNAME '.$vars->post_title.'<br/>';
      }
    }
    echo 'PRODUCT '.$vars->post_title.'<br/>';
    echo 'DESCRIPTION '.$wp_query->post->post_excerpt.'<br/>';
    echo 'URL '.get_permalink($wp_query->post->ID).'<br/>';
   
    $img = wp_get_attachment_image_src( get_post_thumbnail_id($vars->ID), 'shop_single' ); 
    if(!empty($img[0])){
    echo 'IMGURL '.$img[0].'<br/>';
    }
    echo 'PRICEVAT '.$var_product->get_price().'<br/>';
    
    if(!empty($meta['heureka_cpc'][0])){
    echo 'HEUREKA_CPC '.$meta['heureka_cpc'][0].'<br/>';
    }
    if(!empty($meta['manufacturer'][0])){
    echo 'MANUFACTURER '.$meta['manufacturer'][0].'<br/>';
    }else{
    echo 'MANUFACTURER '.strip_tags($my_product->get_categories( )).'<br/>';
    }
    
    if(!empty($meta['heureka_category'][0])){
    echo 'CATEGORYTEXT '.$meta['heureka_category'][0].'<br/>';
    }else{
    $terms = wp_get_post_terms($wp_query->post->ID,'product_cat',array('fields'=>'ids'));
    echo 'CATEGORYTEXT '.$heureka_categories[$heureka_assing_categories[$terms[0]]]['category_fullname'].'<br/>';
    }
    
    if(!empty($meta['_ean'][0])){
      echo 'EAN '.$meta['_ean'][0].'<br/>';
    }
    
    if(!empty($heureka_doba_doruceni)){
      echo 'DELIVERY_DATE '.$heureka_doba_doruceni.'<br/>';    
    }
    
    
    
    $attributes = maybe_unserialize( get_post_meta( $wp_query->post->ID, '_product_attributes', true ) );
    
    foreach ( $attributes as $attribute ) :
		$variation_data = get_post_meta( $vars->ID );
    
    $variation_selected_value = isset( $variation_data[ 'attribute_' . sanitize_title( $attribute['name'] ) ][0] ) ? $variation_data[ 'attribute_' . sanitize_title( $attribute['name'] ) ][0] : '';
    
    $att_label =  wc_attribute_label( $attribute['name'] ); 
		
    		if ( $attribute['is_taxonomy'] ) { 
        
	          $values = wc_get_product_terms( $wp_query->post->ID, $attribute['name'] );         
           
            foreach ( $values as $term ) {
						if($variation_selected_value==$term->slug){ $valuu = $term->name;}
					   }
             
    		} else {
    			$values = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
          foreach ( $values as $option ) {
						if($variation_selected_value==sanitize_title( $option )){ $valuu = esc_html( apply_filters( 'woocommerce_variation_option_name', $option ));}
          }
          
		     }
         
        
          echo 'PARAM<br />';
          echo 'PARAM_NAME '.$att_label.'<br />';
          echo 'VAL '.$valuu.'<br />';
          echo '/PARAM<br />';
     
         
	   endforeach; 
    
    
   echo 'ITEMGROUP_ID GR'.$wp_query->post->ID.'<br/><br/>';
  }

}












}else{ 

echo '<p>Produkt bez variant!</p>';   

$my_product = new WC_Product($wp_query->post);

//Select all post meta
$meta = get_post_meta($wp_query->post->ID);
    /* Control stock status */
    if( $meta['_stock_status'][0] == 'instock' ){
    echo 'ITEM_ID '.$wp_query->post->ID.'<br/>';
    
    
    if(!empty($meta['custom_product_title'][0])){
      echo 'PRODUCTNAME '.$meta['custom_product_title'][0].'<br/>';
    }else{
      if(!empty($meta['manufacturer'][0])){
        echo 'PRODUCTNAME '.$meta['manufacturer'][0].' | '.$wp_query->post->post_title.'<br/>';
      }else{
        echo 'PRODUCTNAME '.$wp_query->post->post_title.'<br/>';
      }
    }
    
    echo 'PRODUCT '.$wp_query->post->post_title.'<br/>';
    echo 'DESCRIPTION '.$wp_query->post->post_excerpt.'<br/>';
    echo 'URL '.get_permalink($wp_query->post->ID).'<br/>';
   
    $img = wp_get_attachment_image_src( get_post_thumbnail_id($wp_query->post->ID), 'shop_single' ); 
    if(!empty($img[0])){
    echo 'IMGURL '.$img[0].'<br/>';
    }
    echo 'PRICEVAT '.$my_product->get_price().'<br/>';
    
    if(!empty($meta['heureka_cpc'][0])){
    echo 'HEUREKA_CPC '.$meta['heureka_cpc'][0].'<br/>';
    }
    if(!empty($meta['manufacturer'][0])){
    echo 'MANUFACTURER '.$meta['manufacturer'][0].'<br/>';
    }else{
    echo 'MANUFACTURER '.strip_tags($my_product->get_categories( )).'<br/>';
    }
    
    if(!empty($meta['heureka_category'][0])){
    echo 'CATEGORYTEXT '.$meta['heureka_category'][0].'<br/>';
    }else{
    $terms = wp_get_post_terms($wp_query->post->ID,'product_cat',array('fields'=>'ids'));
    echo 'CATEGORYTEXT '.$heureka_categories[$heureka_assing_categories[$terms[0]]]['category_fullname'].'<br/>';
    }
    
    if(!empty($meta['_ean'][0])){
      echo 'EAN '.$meta['_ean'][0].'<br/>';
    }
    
    if(!empty($heureka_doba_doruceni)){
      echo 'DELIVERY_DATE '.$heureka_doba_doruceni.'<br/>';    
    }
    
    
    
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
     
          echo 'PARAM<br />';
          echo 'PARAM_NAME '.$att_label.'<br />';
          echo 'VAL '.$value.'<br />';
          echo '/PARAM<br />';
     
        endforeach;    
         
	   endforeach; 
    
    }

   
    if(count($heureka_delivery)>0){
       foreach ( $heureka_delivery as $item ) :
    
         echo 'DELIVERY<br />';
          echo 'DELIVERY_ID '.$item['id'].'<br />';
          echo 'DELIVERY_PRICE '.$item['delivery_price'].'<br />';
          echo 'DELIVERY_PRICE_COD '.$item['delivery_price_cod'].'<br />';
         echo '/DELIVERY<br />';
    
       endforeach;
    
    }


    
    
    
    
    
     }
  }

    endwhile;
}
?>
   
  <a href="<?php echo home_url().'/wp-admin/admin.php?page=feed-check&check=ok'; ?>" class="button"><?php _e('Check feed','heureka-xml'); ?></a>
   
</form>

  
</div>
