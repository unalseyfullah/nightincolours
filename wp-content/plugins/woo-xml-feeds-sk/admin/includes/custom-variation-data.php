<?php

global $woocommerce, $post;
$variation_data = get_post_meta($variation->ID);

    echo '<div class="options_group heureka-option">';
    echo '<p class="heureka-title"><strong>'.__('Heureka XML data produktu','woocommerce').'</strong></p>';
   
   if(!empty($variation_data['_variation_heureka_title'][0])){ $value = $variation_data['_variation_heureka_title'][0];  }else{ $value = ''; }
   woocommerce_wp_text_input( 
	   array( 
		'id'                => '_variation_heureka_title['.$loop.']', 
		'label'             => __( 'Product', 'woocommerce' ), 
		'placeholder'       => 'title produktu pro xml feed', 
    'desc_tip'          => 'true',
		'description'       => __( 'Vložte jméno produktu pro xml feed (PRODUCT), může se lišit od názvu produktu.', 'woocommerce' ),
    'value'             => $value
	   )
   );
   if(!empty($variation_data['_variation_heureka_name'][0])){ $value = $variation_data['_variation_heureka_name'][0];  }else{ $value = ''; }
   
   woocommerce_wp_text_input( 
	   array( 
		'id'                => '_variation_heureka_name['.$loop.']', 
		'label'             => __( 'Product name', 'woocommerce' ), 
		'placeholder'       => 'jméno produktu pro xml feed', 
    'desc_tip'          => 'true',
		'description'       => __( 'Vložte jméno produktu pro xml feed (PRODUCTNAME), může se lišit od názvu produktu.', 'woocommerce' ),
    'value'             => $value
	   )
   );
    //Product variation Ean
 if(!empty($variation_data['_variation_ean'][0])){ $value = $variation_data['_variation_ean'][0];  }else{ $value = ''; }  
    woocommerce_wp_text_input( 
 	   array( 
		  'id'                => '_variation_ean['.$loop.']', 
		  'label'             => __( 'EAN kód varianty', 'woocommerce' ), 
		  'placeholder'       => '', 
      'desc_tip'          => 'true',
		  'description'       => __( 'EAN položky' ),
      'value'             => $value 
 	   )
 );
 //Product variation Isbn
 if(!empty($variation_data['_variation_isbn'][0])){ $value = $variation_data['_variation_isbn'][0];  }else{ $value = ''; }  
    woocommerce_wp_text_input( 
 	   array( 
		  'id'                => '_variation_isbn['.$loop.']', 
		  'label'             => __( 'ISBN kód varianty', 'woocommerce' ), 
		  'placeholder'       => '', 
      'desc_tip'          => 'true',
		  'description'       => __( 'ISBN položky' ),
      'value'             => $value 
 	   )
 ); 
   if(!empty($variation_data['_variation_heureka_category'][0])){ $value = $variation_data['_variation_heureka_category'][0];  }else{ $value = ''; }
   woocommerce_wp_text_input( 
	   array( 
		'id'                => '_variation_heureka_category['.$loop.']', 
		'label'             => __( 'Kategorie varianty produktu Heureka', 'woocommerce' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Enter custom category for this product.', 'woocommerce' ),
    'value'             => $value 
	   )
   );
   if(!empty($variation_data['_variation_pricemania_category'][0])){ $value = $variation_data['_variation_pricemania_category'][0];  }else{ $value = ''; }
   woocommerce_wp_text_input( 
	   array( 
		'id'                => '_variation_pricemania_category['.$loop.']', 
		'label'             => __( 'Kategorie varianty produktu Pricemania', 'woocommerce' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Enter custom category for this product.', 'woocommerce' ),
    'value'             => $value 
	   )
   );
   if(!empty($variation_data['_variation_najnakup_category'][0])){ $value = $variation_data['_variation_najnakup_category'][0];  }else{ $value = ''; }
   woocommerce_wp_text_input( 
	   array( 
		'id'                => '_variation_najnakup_category['.$loop.']', 
		'label'             => __( 'Kategorie varianty produktu Najnakup', 'woocommerce' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Enter custom category for this product.', 'woocommerce' ),
    'value'             => $value 
	   )
   );
    
   if(!empty($variation_data['_variation_imgurl_alternative'][0])){ $img_alternative = $variation_data['_variation_imgurl_alternative'][0];  }else{ $img_alternative = false; }
   
   echo '<div class="altimg-wrap'.$loop.' altimg-wrap">';
   if(!empty($img_alternative)){
  $img_alt = unserialize($img_alternative);
    $i=1;
    foreach($img_alt as $key => $item){
      echo '<p class="form-field imgurl_alternative_field">
          <label for="imgurl_alternative">' .  __( 'Alternativní obrázek', 'woocommerce' ) . '</label>
          <input type="text" class="imgurl_alternative" id="alt'.$loop.''.$i.'" data-id="'.$i.'" name="_variation_imgurl_alternative['.$loop.'][]" value="' . esc_attr( $item ) . '" />';
      echo '<input type="button" class="btn btn-info btn-mini alt-image-button" value="Nahrej obrázek" style="width:auto;" data-loop="'.$loop.'" />';    
      echo '<span class="btn btn-danger btn-mini remove-altimg" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove"><i class="fa fa-times"></i></span>';    
      echo '</p>';  
      $i++;  
      }
   }else{
      echo '<p class="form-field imgurl_alternative_field">
          <label for="_variation_imgurl_alternative">' .  __( 'Alternativní obrázek', 'woocommerce' ) . '</label>
          <input type="text" class="imgurl_alternative" id="alt'.$loop.'1" data-id="1" name="_variation_imgurl_alternative['.$loop.'][]" value="" />';
      echo '<input type="button" class="btn btn-info btn-mini alt-image-button" value="Nahrej obrázek" style="width:auto;" data-loop="'.$loop.'" />';    
      echo '</p>';
   }
   echo '</div>';
   echo '<div class="clear"></div><p><span class="btn btn-info btn-sm pridataltimg" data-loop="'.$loop.'">Přidat obrázek</span></p><div class="clear"></div>';
   
   
   if(!empty($variation_data['_variation_video_url'][0])){ $value = $variation_data['_variation_video_url'][0];  }else{ $value = ''; }
   woocommerce_wp_text_input( 
	   array( 
		'id'                => '_variation_video_url['.$loop.']', 
		'label'             => __( 'Variation Video url', 'woocommerce' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Odkaz na videorecenzi produktu. Lze uvádět pouze odkazy na videa umístěná na serveru www.youtube.com.', 'woocommerce' ),
    'value'             => $value 
	   )
   );
  if(!empty($variation_data['_variation_video_name'][0])){ $value = $variation_data['_variation_video_name'][0];  }else{ $value = ''; }
   woocommerce_wp_text_input( 
	   array( 
		'id'                => '_variation_video_name['.$loop.']', 
		'label'             => __( 'Variation Název Videa', 'woocommerce' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Název videa.', 'woocommerce' ),
    'value'             => $value 
	   )
   ); 
  if(!empty($variation_data['_variation_heureka_cpc_sk'][0])){ $value = $variation_data['_variation_heureka_cpc_sk'][0];  }else{ $value = ''; }
    woocommerce_wp_text_input( 
 	   array( 
  		'id'                => '_variation_heureka_cpc_sk['.$loop.']', 
	  	'label'             => __( 'Heuréka CPC', 'woocommerce' ), 
 		  'placeholder'       => 'Heuréka CPC', 
      'desc_tip'          => 'true',
  		'description'       => __( 'Cena za proklik, kterou jste ochotni nabídnout za tento produkt.', 'woocommerce' ),
      'value'             => $value 
 	   )
    ); 
  if(!empty($variation_data['_variation_delivery_date'][0])){ $value = $variation_data['_variation_delivery_date'][0];  }else{ $value = ''; } 
  woocommerce_wp_text_input( 
	   array( 
		'id'                => '_variation_delivery_date['.$loop.']', 
		'label'             => __( 'Variation Datum doručení', 'woocommerce' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Zadejte dobu doručení v dnech.<br />Pro zboží skladem <br />zadejte: skladom, nebo 0<br />Dodání do 3 dnů, <br />zadejte:  1 - 3<br />Dodání do týdne, <br />zadejte:  4 - 7<br />Dodání do 2 týdnů, <br />zadejte:  8 - 14<br />Dodání do měsíce, <br />zadejte:  15 - 30<br />Více jak měsíc, <br />zadejte:  31 a více<br />Dodání neznámé, nechte pole prázdné' ),
    'value'             => $value 
	   )
   );
   if(!empty($variation_data['_variation_accessory'][0])){ $value = $variation_data['_variation_accessory'][0];  }else{ $value = ''; }  
   woocommerce_wp_text_input( 
	   array( 
		'id'                => '_variation_accessory['.$loop.']', 
		'label'             => __( 'Id příslušenství', 'woocommerce' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Obsahuje ITEM_ID položky, která je příslušenstvím k tomuto produktu, např. nabíječka, pouzdro, apod.' ),
    'value'             => $value 
	   )
   ); 
   
   if(!empty($variation_data['_variation_dues'][0])){ $value = $variation_data['_variation_dues'][0];  }else{ $value = ''; } 
   woocommerce_wp_text_input( 
	   array( 
		'id'                => '_variation_dues['.$loop.']', 
		'label'             => __( 'Poplatky', 'woocommerce' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Součet veškerých poplatků (pokud již nejsou obsaženy v konečné ceně produktu), které je nutné zaplatit při zakoupení produktu (cena uvedena vč. DPH, nezahrnuje dopravu a balné). Poplatek je poté automaticky připočten k ceně produktu s DPH.' ),
    'value'             => $value 
	   )
   ); 
   
   
      echo '<p>Google Nákupy</p>';
   if(!empty($variation_data['_variation_google_mnp'][0])){ $value = $variation_data['_variation_google_mnp'][0];  }else{ $value = ''; }
  woocommerce_wp_text_input( 
	   array( 
		'id'                => '_variation_google_mnp['.$loop.']', 
		'label'             => __( 'Číslo dílu výrobce', 'woo-xml-feeds' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Číslo dílu výrobce (MPN) je alfanumerický kód vytvořený výrobcem, který daný produkt jedinečným způsobem odlišuje od ostatních produktů daného výrobce.','woo-xml-feeds' ), 
	  'value'             => $value
     )
   );
   
   
   if(!empty($variation_data['_variation_google_identifikator_exists'][0])){ $value = $variation_data['_variation_google_identifikator_exists'][0];  }else{ $value = ''; }
   $args = array();
   $args['id']          = '_variation_google_identifikator_exists['.$loop.']'; 
   $args['value']       = $value;
	 $args['label']       = __( 'Identifikator existuje', 'woo-xml-feeds' );
   $args['desc_tip']    = 'true';
   $args['class']    = 'select short';
	 
   $cat_options = array();
   $cat_options['true'] = 'True';
   $cat_options['false'] = 'False';
   
   $args['options'] = $cat_options;
   
   woocommerce_wp_select($args);
   
   
   
   echo '<p><strong>Štíkty produktu</strong></p>';
   echo '<p><strong>První štítek</strong></p>';
   if(!empty($variation_data['_variation_google_stitek_value_1'][0])){ $value = $variation_data['_variation_google_stitek_value_1'][0];  }else{ $value = ''; }
  woocommerce_wp_text_input( 
	   array( 
		'id'                => '_variation_google_stitek_value_1['.$loop.']', 
		'label'             => __( 'Hodnota', 'woo-xml-feeds' ), 
		'value'             => $value
     )
   );
   
   echo '<p><strong>Druhý štítek</strong></p>';
   if(!empty($variation_data['_variation_google_stitek_value_2'][0])){ $value = $variation_data['_variation_google_stitek_value_2'][0];  }else{ $value = ''; }
  woocommerce_wp_text_input( 
	   array( 
		'id'                => '_variation_google_stitek_value_2['.$loop.']', 
		'label'             => __( 'Hodnota', 'woo-xml-feeds' ), 
		'value'             => $value
     )
   );
   
   echo '<p><strong>Třetí štítek</strong></p>';
   if(!empty($variation_data['_variation_google_stitek_value_3'][0])){ $value = $variation_data['_variation_google_stitek_value_3'][0];  }else{ $value = ''; }
  woocommerce_wp_text_input( 
	   array( 
		'id'                => '_variation_google_stitek_value_3['.$loop.']', 
		'label'             => __( 'Hodnota', 'woo-xml-feeds' ), 
		'value'             => $value
     )
   );
   
   echo '<p><strong>Čtvrtý štítek</strong></p>';
   if(!empty($variation_data['_variation_google_stitek_value_4'][0])){ $value = $variation_data['_variation_google_stitek_value_4'][0];  }else{ $value = ''; }
  woocommerce_wp_text_input( 
	   array( 
		'id'                => '_variation_google_stitek_value_4['.$loop.']', 
		'label'             => __( 'Hodnota', 'woo-xml-feeds' ), 
		'value'             => $value
     )
   );
   
   echo '<p><strong>Pátý štítek</strong></p>';
   if(!empty($variation_data['_variation_google_stitek_value_5'][0])){ $value = $variation_data['_variation_google_stitek_value_5'][0];  }else{ $value = ''; }
  woocommerce_wp_text_input( 
	   array( 
		'id'                => '_variation_google_stitek_value_5['.$loop.']', 
		'label'             => __( 'Hodnota', 'woo-xml-feeds' ), 
		'value'             => $value
     )
   );
   
   
   echo '<p>123 Nákup</p>'; 
   
   if(!empty($variation_data['_variation_123_nakup_category'][0])){ $value = $variation_data['_variation_123_nakup_category'][0];  }else{ $value = ''; }
   woocommerce_wp_textarea_input( 
	   array( 
		'id'                => '_variation_123_nakup_category['.$loop.']', 
		'label'             => __( '123 Nákup kategorie', 'woo-xml-feeds' ), 
		'desc_tip'          => 'true',
		'description'       => __( 'Seznam kategorií najdete na http://www.123-nakup.sk/lists-export/product-categories, jednotlivé kategorie oddělte čárkou.', 'woo-xml-feeds' ),
    'value'             => $value
	   )
   );
   
   if(!empty($variation_data['_variation_seo_title'][0])){ $value = $variation_data['_variation_seo_title'][0];  }else{ $value = ''; }
   woocommerce_wp_textarea_input( 
	   array( 
		'id'                => '_variation_seo_title['.$loop.']', 
		'label'             => __( 'SEO Title', 'woo-xml-feeds' ), 
		'desc_tip'          => 'true',
		'description'       => __( 'SEO hlavička produktu.', 'woo-xml-feeds' ),
    'value'             => $value
	   )
   );
   
   if(!empty($variation_data['_variation_seo_keywords'][0])){ $value = $variation_data['_variation_seo_keywords'][0];  }else{ $value = ''; }
   woocommerce_wp_textarea_input( 
	   array( 
		'id'                => '_variation_seo_keywords['.$loop.']', 
		'label'             => __( 'SEO Keywords', 'woo-xml-feeds' ), 
		'desc_tip'          => 'true',
		'description'       => __( 'SEO klíčová slova produktu.', 'woo-xml-feeds' ),
    'value'             => $value
	   )
   );
   
   if(!empty($variation_data['_variation_seo_description'][0])){ $value = $variation_data['_variation_seo_description'][0];  }else{ $value = ''; }
   woocommerce_wp_textarea_input( 
	   array( 
		'id'                => '_variation_seo_description['.$loop.']', 
		'label'             => __( 'SEO popis produktu.', 'woo-xml-feeds' ), 
		'desc_tip'          => 'true',
		'description'       => __( 'SEO popis produktu.', 'woo-xml-feeds' ),
    'value'             => $value
	   )
   );

        
   
   
   
   
  echo '</div>';

?>