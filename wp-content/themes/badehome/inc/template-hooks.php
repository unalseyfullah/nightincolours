<?php
add_action('after_setup_theme', 'badeh_remove_hooks');

function badeh_remove_hooks() {
	remove_action('woo_main_before','woo_display_breadcrumbs');
}


// wrap breadcrumbs
add_action('badeh_before_content', 'badeh_breadcrumbs', 1);

function badeh_breadcrumbs() {
	global $woo_options;
	if ( isset( $woo_options['woo_breadcrumbs_show'] ) && $woo_options['woo_breadcrumbs_show'] == 'true' && ! is_home() ) {
	echo '<section class="breadcrumbs-wrap"><div class="col-full">';
		woo_breadcrumbs();
	echo '</div></section><!--/#breadcrumbs-wrap -->';
	}
}

// Show manufacturer on archive
function badeh_manufacturer() {
	global $post;
	$attribute_names = array( 'pa_vyrobca' ); // Insert attribute names here
 	echo '<div class="vyrobcovia">';
	foreach ( $attribute_names as $attribute_name ) {
		$taxonomy = get_taxonomy( $attribute_name );
 
		if ( $taxonomy && ! is_wp_error( $taxonomy ) ) {
			$terms = wp_get_post_terms( $post->ID, $attribute_name );
			$terms_array = array();
 
	        if ( ! empty( $terms ) ) {
		        foreach ( $terms as $term ) {
			       $archive_link = get_term_link( $term->slug, $attribute_name );
			       $full_line = '<a href="' . $archive_link . '">'. $term->name . '</a>';
			       array_push( $terms_array, $full_line );
		        }
 
		        // echo $taxonomy->labels->name . ' ' . implode( $terms_array, ', ' );
		        echo implode( $terms_array, ', ' );
	        }
    	}
    }
    echo '</div>';
}

add_action('woocommerce_after_shop_loop_item', 'badeh_manufacturer', 2);

// remove price 
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price');

// details table
add_action('woocommerce_single_product_summary', 'badeh_details_table');
function badeh_details_table() {
	global $product;
	$sku = $product->get_sku();
	?>
	<table class="shop_attributes">
		<tbody>
			<?php 
			$price = $product->get_price_html();
			$price_notax = woocommerce_price($product->get_price_excluding_tax());
			$niceprice = $price . ' <small>(' . $price_notax . ' bez DPH)</small>';
			echo badeh_get_properties_row(__('Price', 'badehome'), $niceprice ); ?>
			<?php 
				if ($sku) {
					echo badeh_get_properties_row(__('SKU', 'badehome'), $sku );
				}
			?>
		</tbody>
	</table>
	<?php
	$product->list_attributes();
}

function badeh_get_properties_row($name, $value) {
	$out = '<tr>';
	$out .= '	<th>'.$name.'</th>';
	$out .= '	<td>'.$value.'</td>';
	$out .= '</tr>';
	return $out;
}

add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 34 );


// remove reviews summary
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 32 );

// move reviews
remove_action( 'woocommerce_after_single_product_summary', 'superstore_product_reviews', 17 );
add_action( 'woocommerce_after_single_product_summary', 'superstore_product_reviews', 20 );

// rearrange price display
add_filter( 'woocommerce_get_price_html', 'badeh_get_price_html' );
function badeh_get_price_html($price) {
	if ( is_int(strpos($price, '</del> <ins>')) ) {
		$price = explode('</del> <ins>', $price);
		$price = '<ins>'.$price[1].' '.$price[0].'</del>';
	}
	return $price;
}

// change add to cart text
add_filter( 'woocommerce_product_single_add_to_cart_text', 'badeh_single_add_to_cart_text', 11, 2 );
add_filter( 'woocommerce_product_add_to_cart_text', 'badeh_add_to_cart_text', 11, 2 );
 
function badeh_single_add_to_cart_text($text, $product) {
	return __('Add to cart', 'badehome');
}

function badeh_add_to_cart_text($text, $product) {
	$result = __('Add', 'badehome');;
	if( $product->product_type == 'variable' ) {
		$result = __('Info', 'badehome');;
	}
	return $result;
}

// modify user fields in checkout

add_filter( 'woocommerce_billing_fields' , 'webikon_override_billing_fields' );

function webikon_override_billing_fields($fields) {
	$new_fields['billing_country'] = $fields['billing_country'];
    $new_fields['billing_first_name'] = $fields['billing_first_name'];
    $new_fields['billing_last_name'] = $fields['billing_last_name'];
    $new_fields['billing_company'] = $fields['billing_company'];
    $new_fields['billing_address_1'] = $fields['billing_address_1'];
    $new_fields['billing_city'] = $fields['billing_city'];
    $new_fields['billing_postcode'] = $fields['billing_postcode'];
    
    $new_fields['billing_email'] = $fields['billing_email'];
    $new_fields['billing_phone'] = $fields['billing_phone'];
    $new_fields['billing_postcode']['class'][] = 'form-row-wide';
    return $new_fields;
}

add_filter( 'woocommerce_shipping_fields' , 'webikon_override_shipping_fields' );

function webikon_override_shipping_fields($fields) {
	$new_fields['shipping_country'] = $fields['shipping_country'];
    $new_fields['shipping_first_name'] = $fields['shipping_first_name'];
    $new_fields['shipping_last_name'] = $fields['shipping_last_name'];
    $new_fields['shipping_company'] = $fields['shipping_company'];
    $new_fields['shipping_address_1'] = $fields['shipping_address_1'];
    $new_fields['shipping_city'] = $fields['shipping_city'];
    $new_fields['shipping_postcode'] = $fields['shipping_postcode'];
    
    return $new_fields;
}

add_action( 'show_user_profile', 'webikon_add_fields_to_edit_screen' );
add_action( 'edit_user_profile', 'webikon_add_fields_to_edit_screen' );

// remove deafult top bar
remove_action( 'woo_header_before', 'woo_top_nav' );

// fix review count
remove_action( 'woocommerce_after_shop_loop_item', 'superstore_product_rating_overview', 9 );
add_action( 'woocommerce_after_shop_loop_item', 'badeh_product_rating_overview', 9 );

if ( ! function_exists( 'badeh_product_rating_overview' ) ) {
	function badeh_product_rating_overview() {
		global $product;
		$review_total = get_comments_number();
		if ( $review_total > 0 && get_option( 'woocommerce_enable_review_rating' ) !== 'no' ) {
			echo '<div class="rating-wrap">';
				echo '<a href="' . get_permalink() . '#reviews">';
					echo $product->get_rating_html();
					echo '<span class="review-count">';
						$number = get_comments_number();
						printf( _n( '%s review', '%s reviews', $number, 'badehome' ), $number );
					echo '</span>';
				echo '</a>';
			echo '</div>';
		}
	}
}
