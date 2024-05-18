<?php

global $woocommerce, $post;
    echo '<div class="options_group heureka-option">';
    echo '<p class="heureka-title"><strong>'.__('Specifikace pro XML feed','woo-xml-feeds').'</strong></p>';
  
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'custom_product_name', 
		'label'             => __( 'Product name', 'woo-xml-feeds' ), 
		'placeholder'       => 'jméno produktu pro xml feed', 
    'desc_tip'          => 'true',
		'description'       => __( 'Vložte jméno produktu pro xml feed, může se lišit od názvu produktu.', 'woo-xml-feeds' ) 
	   )
   );
   woocommerce_wp_text_input(
   array( 
		'id'                => 'custom_product_title', 
		'label'             => __( 'Product', 'woo-xml-feeds' ), 
		'placeholder'       => 'jméno produktu pro xml feed', 
    'desc_tip'          => 'true',
		'description'       => __( 'Vložte pojmenování produktu pro xml feed, může se lišit od názvu produktu.', 'woo-xml-feeds' ) 
	   )
   );
   woocommerce_wp_text_input( 
	   array( 
		'id'                => '_ean', 
		'label'             => __( 'EAN number', 'woo-xml-feeds' ), 
		'placeholder'       => 'EAN', 
    'desc_tip'          => 'true',
		'description'       => __( 'EAN kód je používán k označování jednotlivých druhů zboží. Podporujeme formát EAN 13. Neuvádějte však interní čísla produktů, ale oficiální kódy zboží!', 'woo-xml-feeds' ) 
		 
	   )
   );
   
   woocommerce_wp_text_input( 
	   array( 
		'id'                => '_isbn', 
		'label'             => __( 'ISBN', 'woo-xml-feeds' ), 
		'placeholder'       => 'ISBN', 
    'desc_tip'          => 'true',
		'description'       => __( 'Alfanumerický kód určený pro jednoznačnou identifikaci knižních vydání. Podporujeme formáty ISBN-10 a ISBN-13. Čísla v ISBN kódu se oddělují pomlčkou, například "978-0-123456-47-2".', 'woo-xml-feeds' ) 
		 
	   )
   );
   
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'manufacturer', 
		'label'             => __( 'Výrobce', 'woo-xml-feeds' ), 
		'placeholder'       => 'značka', 
    'desc_tip'          => 'true',
		'description'       => __( 'Vložte název výrobce produktu.', 'woo-xml-feeds' ) 
	   )
   );
   woocommerce_wp_text_input( 
	   array( 
		'id'                => '123_nakup_manufacturer', 
		'label'             => __( 'Výrobce dle 123 Nákup', 'woo-xml-feeds' ), 
		'placeholder'       => 'značka', 
    'desc_tip'          => 'true',
		'description'       => __( 'Vložte název výrobce produktu dle číselníku http://www.123-nakup.sk/lists-export/manufacturers.', 'woo-xml-feeds' ) 
	   )
   );
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'heureka_cpc_sk', 
		'label'             => __( 'Heureka CPC', 'woo-xml-feeds' ), 
		'placeholder'       => 'Heureka CPC', 
    'desc_tip'          => 'true',
		'description'       => __( 'Cena za proklik, kterou jste ochotni nabídnout pro tento produkt.', 'woo-xml-feeds' ) 
	   )
   );
   //apply_filters( 'wooshop_xml_product_data_after_cpc', $value );
   
     
   $img_alternative = get_post_meta( $post->ID, 'imgurl_alternative',true );
   
   echo '<div id="altimg-wrap">';
   if(!empty($img_alternative)){
  // $img_alt = unserialize($img_alternative);
  $img_alt = $img_alternative;
    $i=1;
    foreach($img_alt as $key => $item){
      echo '<p class="form-field imgurl_alternative_field">
          <label for="imgurl_alternative">' .  __( 'Alternativní obrázek', 'woo-xml-feeds' ) . '</label>
          <input type="text" class="imgurl_alternative" id="alt'.$i.'" data-id="'.$i.'" name="imgurl_alternative[]" value="' . esc_attr( $item ) . '" />';
      echo '<input type="button" class="btn btn-info btn-mini alt-image-button" value="Nahrej obrázek" style="width:auto;margin-left:10px;" />';    
      echo '<span class="btn btn-danger btn-mini remove-altimg" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove"><i class="fa fa-times"></i></span>';    
      echo '</p>';  
      $i++;  
      }
   }else{
      echo '<p class="form-field imgurl_alternative_field">
          <label for="imgurl_alternative">' .  __( 'Alternativní obrázek', 'woo-xml-feeds' ) . '</label>
          <input type="text" class="imgurl_alternative" id="alt1" data-id="1" name="imgurl_alternative[]" value="" />';
      echo '<input type="button" class="btn btn-info btn-mini alt-image-button" value="Nahrej obrázek" style="width:auto;margin-left:10px;" />';    
      echo '</p>';
   }
   echo '</div>';
   echo '<p><span class="btn btn-info btn-sm" id="pridataltimg">Přidat obrázek</span></p>';
   
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'video_url', 
		'label'             => __( 'Video url', 'woo-xml-feeds' ), 
		'placeholder'       => 'www.youtube.com', 
    'desc_tip'          => 'true',
		'description'       => __( 'Odkaz na videorecenzi produktu. Lze uvádět pouze odkazy na videa umístěná na serveru www.youtube.com.', 'woo-xml-feeds' ) 
	   )
   );
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'video_name', 
		'label'             => __( 'Jméno videa', 'woo-xml-feeds' ), 
		'placeholder'       => __( 'Jméno videa', 'woo-xml-feeds' ), 
    'desc_tip'          => 'true',
		'description'       => __( 'Jméno videa.', 'woo-xml-feeds' ) 
	   )
   );
  
   echo '<script>
            jQuery(document).ready(function() { jQuery("#heureka_category_sk").select2(); });
          </script>';
   $heureka_categories_sk = get_option( 'woo_heureka_categories_sk');
   $args = array();
   $args['id']          = 'heureka_category_sk'; 
	 $args['label']       = __( 'Heureka kategorie produktu', 'woo-xml-feeds' );
   $args['desc_tip']    = 'true';
   $args['class']    = 'select long_select';
	 $args['description'] = __( 'Enter custom category for this product.', 'woo-xml-feeds' );
   $cat_options = array();
   $cat_options['default'] = 'Vyber Heureka kategorii';
   if(!empty($heureka_categories_sk)){
    foreach($heureka_categories_sk as $key => $item){
      $cat_options[$key] = $item['category_fullname'];
    }
   }
   $args['options'] = $cat_options;
   woocommerce_wp_select($args);
    
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'pricemania_category', 
		'label'             => __( 'Pricemania kategorie produktu', 'woo-xml-feeds' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Vložte vlastní kategorii, pro tento produkt.' ) 
	   )
   ); 
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'najnakup_category', 
		'label'             => __( 'Najnakup kategorie produktu', 'woo-xml-feeds' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Vložte vlastní kategorii, pro tento produkt.' ) 
	   )
   );  
   
   
   
  $heureka_item_type = get_post_meta( $post->ID, 'heureka_item_type',true );
  echo '<p class="form-field-radio"><span>Typ zboží</span><label>Nové zboží</label><input type="radio" name="heureka_item_type" class="icheck" value="nove"';
  if(!empty($heureka_item_type) && $heureka_item_type == 'nove'){ echo 'checked="checked"'; } 
  echo '><label>Bazarové zboží</label><input type="radio" name="heureka_item_type" value="bazar" class="icheck"';
  if(!empty($heureka_item_type) && $heureka_item_type == 'bazar'){ echo 'checked="checked"'; } 
  echo '></p>';
   
  woocommerce_wp_text_input( 
	   array( 
		'id'                => 'delivery_date', 
		'label'             => __( 'Datum expedice', 'woo-xml-feeds' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Zadejte dobu doručení v dnech.<br />Pro zboží skladem <br />zadejte: skladom, nebo 0<br />Dodání do 3 dnů, <br />zadejte:  1 - 3<br />Dodání do týdne, <br />zadejte:  4 - 7<br />Dodání do 2 týdnů, <br />zadejte:  8 - 14<br />Dodání do měsíce, <br />zadejte:  15 - 30<br />Více jak měsíc, <br />zadejte:  31 a více<br />Dodání neznámé, nechte pole prázdné' ) 
	   )
   );
    
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'accessory', 
		'label'             => __( 'Id příslušenství', 'woo-xml-feeds' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Obsahuje ITEM_ID položky, která je příslušenstvím k tomuto produktu, např. nabíječka, pouzdro, apod.' ) 
	   )
   ); 
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'dues', 
		'label'             => __( 'Poplatky', 'woo-xml-feeds' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Součet veškerých poplatků (pokud již nejsou obsaženy v konečné ceně produktu), které je nutné zaplatit při zakoupení produktu (cena uvedena vč. DPH, nezahrnuje dopravu a balné). Poplatek je poté automaticky připočten k ceně produktu s DPH.' ) 
	   )
   ); 
   
   
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'pricemania_shipping', 
		'label'             => __( 'Pricemania cena za dopravu', 'woo-xml-feeds' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Cena za poštovné, 0 znamená poštovné zdarma' ) 
	   )
   );
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'najnakup_shipping', 
		'label'             => __( 'Najnakup cena za dopravu', 'woo-xml-feeds' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Cena za poštovné.' ) 
	   )
   );
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'najnakup_availability', 
		'label'             => __( 'Najnakup dostupnost', 'woo-xml-feeds' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Zadávejte text, např. "skladom"' ) 
	   )
   );
   
  
  echo '<p><strong>Nastavení pro Heuréka dostupnostní feed</strong></p>';
  
  woocommerce_wp_text_input( 
	   array( 
		'id'                => 'product_deadline_time', 
		'label'             => __( 'Čas expedice balíků', 'woo-xml-feeds' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Určuje čas, do kterého je nutné provést objednávku, aby byla doručena dle uvedené doby dodání. Zadávejte ve tvaru 12:00' ) 
	   )
   ); 
  woocommerce_wp_text_input( 
	   array( 
		'id'                => 'product_delivery_time', 
		'label'             => __( 'Doba do dodání', 'woo-xml-feeds' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Aby bylo možné považovat produkt za "skladový", nesmí doba mezi objednáním (orderDeadline) a doručením přesáhnout 7 dnů. Produkty, které nelze dodat do 7 dnů od objednání, do dostupnostního XML souboru neuvádějte.','woo-xml-feeds' ) 
	   )
   );  
   
    echo '<p><strong>Google Nákupy</strong></p>';
  
  woocommerce_wp_text_input( 
	   array( 
		'id'                => 'google_mnp', 
		'label'             => __( 'Číslo dílu výrobce', 'woo-xml-feeds' ), 
		'placeholder'       => '', 
    'desc_tip'          => 'true',
		'description'       => __( 'Číslo dílu výrobce (MPN) je alfanumerický kód vytvořený výrobcem, který daný produkt jedinečným způsobem odlišuje od ostatních produktů daného výrobce.','woo-xml-feeds' ) 
	   )
   );
   
   
   $args = array();
   $args['id']           = 'google_identifikator_exists'; 
	 $args['label']        = __( 'Identifikator existuje', 'woo-xml-feeds' );
   $args['desc_tip']     = 'true';
   $args['class']        = 'select long_select';
	 
   $cat_options = array();
   $cat_options['true']  = 'True';
   $cat_options['false'] = 'False';
   
   $args['options'] = $cat_options;
   
   woocommerce_wp_select($args);
   
  echo '<p><strong>Štíkty produktu</strong></p>';
   
  echo '<p><strong>První štítek</strong></p>'; 
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'google_stitek_value_1', 
		'label'             => __( 'Hodnota', 'woo-xml-feeds' )
	   )
   );
  echo '<p><strong>Druhý štítek</strong></p>'; 
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'google_stitek_value_2', 
		'label'             => __( 'Hodnota', 'woo-xml-feeds' )
	   )
   ); 
   echo '<p><strong>Třetí štítek</strong></p>'; 
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'google_stitek_value_3', 
		'label'             => __( 'Hodnota', 'woo-xml-feeds' )
	   )
   );
   echo '<p><strong>Čtvrtý štítek</strong></p>'; 
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'google_stitek_value_4', 
		'label'             => __( 'Hodnota', 'woo-xml-feeds' )
	   )
   );
   echo '<p><strong>Pátý štítek</strong></p>'; 
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'google_stitek_value_5', 
		'label'             => __( 'Hodnota', 'woo-xml-feeds' )
	   )
   ); 
   
   
   echo '<p><strong>123 Nákup</strong></p>'; 
   
   woocommerce_wp_textarea_input( 
	   array( 
		'id'                => '123_nakup_category', 
		'label'             => __( '123 Nákup kategorie', 'woo-xml-feeds' ), 
		'desc_tip'          => 'true',
		'description'       => __( 'Seznam kategorií najdete na http://www.123-nakup.sk/lists-export/product-categories, jednotlivé kategorie oddělte čárkou.', 'woo-xml-feeds' )
	   )
   );
   
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'price_type', 
		'label'             => __( 'Jednotka produktu', 'woo-xml-feeds' ),
    'desc_tip'          => 'true',
		'description'       => __( 'Určuje, v jakých jednotkách je produkt prodáván. Výchozí je ks.', 'woo-xml-feeds' ),
	   )
   );
   
   $is_handmate = get_post_meta( $post->ID, 'is_handmate',true );
   echo '<p class="form-field-radio"><span>Produkt je </span><label>handmate</label><input type="radio" name="is_handmate" class="icheck" value="is"';
   if(!empty($is_handmate) && $is_handmate == 'is'){ echo 'checked="checked"'; } 
   echo '><span>Produkt není </span><label>handmate</label><input type="radio" name="is_handmate" value="not" class="icheck"';
   if(!empty($is_handmate) && $is_handmate == 'not'){ echo 'checked="checked"'; } 
   echo '></p>';
   
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'seo_title', 
		'label'             => __( 'SEO Title', 'woo-xml-feeds' ),
    'desc_tip'          => 'true',
		'description'       => __( 'SEO hlavička produktu', 'woo-xml-feeds' ),
	   )
   );
   
   woocommerce_wp_text_input( 
	   array( 
		'id'                => 'seo_keywords', 
		'label'             => __( 'SEO Keywords', 'woo-xml-feeds' ),
    'desc_tip'          => 'true',
		'description'       => __( 'SEO klíčová slova produktu', 'woo-xml-feeds' ),
	   )
   );
   
   woocommerce_wp_textarea_input( 
	   array( 
		'id'                => 'seo_description', 
		'label'             => __( 'SEO popis produktu', 'woo-xml-feeds' ), 
		'desc_tip'          => 'true',
		'description'       => __( 'SEO popis produktu', 'woo-xml-feeds' )
	   )
   );   
   
   
   
   
   
  echo '</div>';

?>