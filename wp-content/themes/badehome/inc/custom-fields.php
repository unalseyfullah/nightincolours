<?php

// Display Fields
add_action( 'woocommerce_product_options_general_product_data', 'badeh_add_novinka_checkbox' );

function badeh_add_novinka_checkbox() {
    global $woocommerce, $post;
    echo '<div class="options_group">';
        woocommerce_wp_checkbox(
            array(
                'id'            => '_badeh_novinka',
                'wrapper_class' => '',
                'label'         => __('Novinka', 'woocommerce' ),
                'description'   => __( '', 'woocommerce' )
            )
        );
    echo '</div>';
    echo '<div class="options_group">';
        woocommerce_wp_checkbox(
            array(
                'id'            => '_badeh_vypredaj',
                'wrapper_class' => '',
                'label'         => __('Výpredaj', 'woocommerce' ),
                'description'   => __( '', 'woocommerce' )
            )
        );
    echo '</div>';
}

// Save Fields
add_action( 'woocommerce_process_product_meta', 'badeh_add_novinka_checkbox_save' );

function badeh_add_novinka_checkbox_save($post_id) {
    $novinka_checkbox = isset( $_POST['_badeh_novinka'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_badeh_novinka', $novinka_checkbox );
    $novinka_checkbox = isset( $_POST['_badeh_vypredaj'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_badeh_vypredaj', $novinka_checkbox );
}

function badeh_novinka_rewrites() {
    add_rewrite_rule('^novinky/?$','index.php?novinka=1&post_type=product','top');
    add_rewrite_rule('^vypredaj/?$','index.php?vypredaj=1&post_type=product','top');
}

add_action('init', 'badeh_novinka_rewrites');


function badeh_novinka_query_vars($vars) {
    $vars[] = 'novinka';
    $vars[] = 'vypredaj';
    return $vars;
}

add_action('query_vars', 'badeh_novinka_query_vars');

function badeh_novinka_loop($query) {
    if( $query->is_main_query() AND !is_admin() AND get_query_var('novinka') ) {
        $meta_q = $query->query_vars['meta_query'];
        $meta_q[] = array(
                'key' => '_badeh_novinka',
                'value' => 'yes'
        );
        $query->set('meta_query', $meta_q);
    }
    if( $query->is_main_query() AND !is_admin() AND get_query_var('vypredaj') ) {
        $meta_q = $query->query_vars['meta_query'];
        $meta_q[] = array(
                'key' => '_badeh_vypredaj',
                'value' => 'yes'
        );
        $query->set('meta_query', $meta_q);
    }
}

add_action('pre_get_posts', 'badeh_novinka_loop');

function badeh_newsflash() {
    global $post;
    global $product;
    $novinka = get_post_meta($post->ID, '_badeh_novinka', true);
    $vypredaj = get_post_meta($post->ID, '_badeh_vypredaj', true);
    $sale = $product->is_on_sale();
    if ($novinka OR $vypredaj OR $sale) {
        echo '<div class="saleflash">';
            if ($sale) {
                echo '<span class="label zlava">'.__('Discount!', 'badehome').'</span>';
            }
            if( $novinka == 'yes' ) {
                echo '<span class="label novinka">'.__('New', 'badehome').'</span>';
            }
            if( $vypredaj == 'yes' ) {
                echo '<span class="label vypredaj">'.__('Sale', 'badehome').'</span>';
            }
        if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' && ( $count = $product->get_rating_count() ) ) {
            echo '<span class="label recenzie">';
            printf( _n( '%s review', '%s reviews', $count, 'badehome' ), $count );
            echo '</span>';
        }

        echo '</div>';
    }
}

add_action('woocommerce_before_shop_loop_item_title', 'badeh_newsflash');
add_action('woocommerce_single_product_summary', 'badeh_newsflash', 6);
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash');
remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash');