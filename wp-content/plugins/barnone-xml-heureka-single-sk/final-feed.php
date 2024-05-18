<?php
header ("Content-Type:text/xml");
ini_set('display_errors',1);
require_once( ABSPATH . 'wp-load.php');

global $wpdb, $woocommerce, $post;
$posts_table = $wpdb->prefix . 'posts';
    $sql = "
            SELECT $wpdb->posts.ID, $wpdb->posts.post_title, $wpdb->posts.post_content, $wpdb->posts.post_name, $wpdb->posts.post_excerpt
            FROM $wpdb->posts
            LEFT JOIN $wpdb->term_relationships ON
            ($wpdb->posts.ID = $wpdb->term_relationships.object_id)
            LEFT JOIN $wpdb->term_taxonomy ON
            ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
            WHERE $wpdb->posts.post_status = 'publish'
            AND $wpdb->posts.post_type = 'product'
            AND $wpdb->term_taxonomy.taxonomy = 'product_cat'
            ORDER BY id DESC
    		";
    $products = $wpdb->get_results($sql);

echo '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
echo '<SHOP>'.PHP_EOL;
// Displays the amount of memory being used as soon as the script runs
$limit = ini_get('memory_limit')."B";
$bytes = memory_get_usage();
function bytesToSize($bytes, $precision = 2)
{
	$kilobyte = 1024;
	$megabyte = $kilobyte * 1024;
	$gigabyte = $megabyte * 1024;
	$terabyte = $gigabyte * 1024;

	if (($bytes >= 0) && ($bytes < $kilobyte)) {
		return $bytes . ' B';

	} elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
		return round($bytes / $kilobyte, $precision) . 'KB';

	} elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
		return round($bytes / $megabyte, $precision) . 'MB';

	} elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
		return round($bytes / $gigabyte, $precision) . 'GB';

	} elseif ($bytes >= $terabyte) {
		return round($bytes / $terabyte, $precision) . 'TB';
	} else {
		return $bytes . 'B';
	}
}
$start = bytesToSize($bytes);
global  $end;
echo "<!-- Využito ";
echo $start;
echo " z celkových ";
echo $limit;
echo " PHP paměti -->";
//------------------------------------------------------------------------------

//price and sale function
function get_numerics ($str)
{
preg_match_all('/\d+,+\d+/', $str, $matches);
//       /\d+/
return $matches[0];
}

  foreach (array_unique($products, SORT_REGULAR) as $prod) {
  $product = get_product($prod->ID);
  $producttype = get_product($prod->product_type);
  $attributes = $product->get_attributes();
  $attrname = array();
  $terms = get_the_terms( $prod->ID, 'product_cat' );
  foreach ($terms as $term) {
  $product_cat_id = $term->term_id;
  $termname = $term->object_id;
  }
  if($attributes){
  $i=0;
  foreach ($attributes as $attribute){
  //$attrname[$i] = $woocommerce->attribute_label( $attribute['name'] );
  $attrname[$i] = wc_attribute_label( $attribute['name'] );
  $i++;
  }
  }
// 2 variations
  if(count($attrname)==2 AND $product->is_type('variable' ) ){
            $i=0;
            $prodvariations = $product->get_available_variations();
            foreach ($prodvariations as $prodvariation) {
            $imagecolor =  $prodvariation['image_link'];
            $varid = $prodvariation['variation_id'];
            $varprice = $prodvariation['price_html'];
            $varpricetotal= strip_tags($varprice);
            $varpricetotaltrim= str_replace(' ','',$varpricetotal);
            $konecnacena = get_numerics($varpricetotaltrim);
            $attrvalues = $prodvariation['attributes'];
            $attrvalue = array_values($attrvalues);
            $taxonomy1 =  strtolower('pa_'.remove_accents($attrname[0]));
            $taxonomy1 = str_replace(' ', '-', $taxonomy1);
            $taxonomy2 =  strtolower('pa_'.remove_accents($attrname[1]));
            $taxonomy2 = str_replace(' ', '-', $taxonomy2);
            $myterm1 = get_term_by('slug', $attrvalue[0], $taxonomy1);
            $name1 = $myterm1->name;
            $myterm2 = get_term_by('slug', $attrvalue[1], $taxonomy2);
            $name2 = $myterm2->name;
            $postmeta = get_post_meta($prod->ID, '_bar_ean_kod' );
            $taxrate = get_option('barnone_sk_tax_rate');
            $taxratecount = $taxrate / 100 + 1;
            $avail = $product->is_in_stock();
              echo '<SHOPITEM>'.PHP_EOL;
                echo '	<ITEM_ID>'.$prod->ID.'17'.$varid.'</ITEM_ID>'.PHP_EOL;
                //echo '	<PRODUCTNAME><![CDATA['.$postmetaproductname[0].']]></PRODUCTNAME>'.PHP_EOL;
		//START UPRAV
		$nazov=str_replace("Night In Colours - ","NightInColours ",$prod->post_title);
		$nazov=str_replace(" - "," ",$nazov);
		//rozmery
		$pozicia1=strpos($prod->post_content,"Rozmery:")+7;
		$pozicia2=strpos($prod->post_content,PHP_EOL);
		$vycuc=substr($prod->post_content,$pozicia1,$pozicia2-$pozicia1);
		$nazov=$nazov . $vycuc;
		//KONIEC UPRAV
		echo '	<PRODUCTNAME><![CDATA['.$nazov.']]></PRODUCTNAME>'.PHP_EOL;
                //echo '	<PRODUCT><![CDATA['.$prod->post_title.']]></PRODUCT>'.PHP_EOL;
		echo '	<PRODUCT><![CDATA['.$nazov.']]></PRODUCT>'.PHP_EOL;
                if (get_option('barnone_sk_product_descr') == 'content')
                {
        	      echo '	<DESCRIPTION><![CDATA['.strip_tags($prod->post_content).']]></DESCRIPTION>'.PHP_EOL;
                }
                else
                {
                echo '	<DESCRIPTION><![CDATA['.strip_tags($prod->post_excerpt).']]></DESCRIPTION>'.PHP_EOL;
                }
                echo '	<URL>'.get_permalink($prod->ID).'?1'.$varid.'</URL>'.PHP_EOL;
                $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($prod->ID), 'small-feature' );
        	      $imgurl = $thumb['0'];
                if ($imagecolor == 0) {
                echo '	<IMGURL>'.$imgurl.'?19'.$varid.'</IMGURL>'.PHP_EOL;
                }
                else
                {
                echo '	<IMGURL>'.$imagecolor.'?19'.$varid.'</IMGURL>'.PHP_EOL;
                }
                echo '	<VIDEOURL><![CDATA['.$postmetayoutube[0].']]></VIDEOURL>'.PHP_EOL;
                echo '	<MANUFACTURER><![CDATA['.get_option('barnone_sk_product_brand').']]></MANUFACTURER>'.PHP_EOL;
                echo '	<CATEGORYTEXT><![CDATA['.get_option('barnone_sk_categorytest'.$product_cat_id).']]></CATEGORYTEXT>'.PHP_EOL;
                if ($konecnacena[1] > 0)
                {
                echo '	<PRICE_VAT>'.$konecnacena[1] * $taxratecount.'</PRICE_VAT>'.PHP_EOL;
                }
                else if ($konecnacena[0] == 0)
                {
                echo '	<PRICE_VAT>'.$product->get_price_including_tax().'</PRICE_VAT>'.PHP_EOL;
                }
                else
                {
                echo '	<PRICE_VAT>'.$konecnacena[0] * $taxratecount.'</PRICE_VAT>'.PHP_EOL;
                }
                echo '	<HEUREKA_CPC>'.get_option('barnone_sk_heureka_cpc').'</HEUREKA_CPC>'.PHP_EOL;
                if (get_option('barnone_sk_product_condition') == 'bazar') {
        	      echo '	<ITEM_TYPE>'.get_option('barnone_sk_product_condition').'</ITEM_TYPE>'.PHP_EOL;
                }
                if ($postmetaeankod[0] != 0) {
                echo '	<EAN>'.$postmetaeankod[0].'</EAN>'.PHP_EOL;
                }
                if ($avail == FALSE) {
                //echo '	<DELIVERY_DATE>'.get_option('barnone_sk_delivery_date_out').'</DELIVERY_DATE>'.PHP_EOL;
		echo '	<DELIVERY_DATE>1</DELIVERY_DATE>'.PHP_EOL;
                }
                else {
                //echo '	<DELIVERY_DATE>'.get_option('barnone_sk_delivery_date').'</DELIVERY_DATE>'.PHP_EOL;
                echo '	<DELIVERY_DATE>1</DELIVERY_DATE>'.PHP_EOL;
		}
                echo '	<DELIVERY>'.PHP_EOL;
                echo '          <DELIVERY_ID>'.get_option('barnone_sk_delivery_partner').'</DELIVERY_ID>'.PHP_EOL;
                echo '          <DELIVERY_PRICE>'.get_option('barnone_sk_delivery_price').'</DELIVERY_PRICE>'.PHP_EOL;
                echo '          <DELIVERY_PRICE_COD>'.get_option('barnone_sk_delivery_price_cod').'</DELIVERY_PRICE_COD>'.PHP_EOL;
                echo '  </DELIVERY>'.PHP_EOL;
                echo '        <PARAM>'.PHP_EOL;
                echo '           <PARAM_NAME><![CDATA['.$attrname[0].']]></PARAM_NAME>'.PHP_EOL;
			          echo '           <VAL><![CDATA['.$name1.']]></VAL>'.PHP_EOL;
                echo '        </PARAM>'.PHP_EOL;
                echo '        <PARAM>'.PHP_EOL;
                echo '           <PARAM_NAME><![CDATA['.$attrname[1].']]></PARAM_NAME>'.PHP_EOL;
			          echo '           <VAL><![CDATA['.$name2.']]></VAL>'.PHP_EOL;
                echo '        </PARAM>'.PHP_EOL;
                echo '	<ITEMGROUP_ID>PRODGROUP'.$prod->ID.'</ITEMGROUP_ID>'.PHP_EOL;
        				echo '</SHOPITEM>'.PHP_EOL;
                $i++;
// 1 variation
          }}else if(count($attrname)==1 AND $product->is_type('variable' ) ){
              $i=0;
              $prodvariations = $product->get_available_variations();
              foreach ($prodvariations as $prodvariation) {
              $postmetaproductname = get_post_meta($prod->ID, '_bar_productname_kod' );
	      $postmetayoutube = get_post_meta($prod->ID, '_bar_youtube_kod' );
              $postmetaeankod = get_post_meta($prod->ID, '_bar_ean_kod' );
              $imagecolor1 =  $prodvariation['image_link'];
              $varid = $prodvariation['variation_id'];
              $varprice = $prodvariation['price_html'];
              $varpricetotal= strip_tags($varprice);
              $varpricetotaltrim= str_replace(' ','',$varpricetotal);
              $konecnacena = get_numerics($varpricetotaltrim);
              $attrvalues = $prodvariation['attributes'];
              $attrvalue = array_values($attrvalues);
              $taxonomy1 =  strtolower('pa_'.remove_accents($attrname[0]));
              $taxonomy1 = str_replace(' ', '-', $taxonomy1);
              $myterm1 = get_term_by('slug', $attrvalue[0], $taxonomy1);
              $name1 = $myterm1->name;
              $postmeta = get_post_meta($prod->ID, '_bar_ean_kod' );
              $taxrate = get_option('barnone_sk_tax_rate');
              $taxratecount = $taxrate / 100 + 1;
              $avail = $product->is_in_stock();
              $i++;
              echo '<SHOPITEM>'.PHP_EOL;
                echo '	<ITEM_ID>'.$prod->ID.'12'.$varid.'</ITEM_ID>'.PHP_EOL;
                //echo '	<PRODUCTNAME><![CDATA['.$postmetaproductname[0].']]></PRODUCTNAME>'.PHP_EOL;
		//START UPRAV
		$nazov=str_replace("Night In Colours - ","NightInColours ",$prod->post_title);
		$nazov=str_replace(" - "," ",$nazov);
		//rozmery
		$pozicia1=strpos($prod->post_content,"Rozmery:")+7;
		$pozicia2=strpos($prod->post_content,PHP_EOL);
		$vycuc=substr($prod->post_content,$pozicia1,$pozicia2-$pozicia1);
		$nazov=$nazov . $vycuc;
		//KONIEC UPRAV
		echo '	<PRODUCTNAME><![CDATA['.$nazov.']]></PRODUCTNAME>'.PHP_EOL;
                //echo '	<PRODUCT><![CDATA['.$prod->post_title.']]></PRODUCT>'.PHP_EOL;
		echo '	<PRODUCT><![CDATA['.$nazov.']]></PRODUCT>'.PHP_EOL;
                if (get_option('barnone_sk_product_descr') == 'content')
                {
        	      echo '	<DESCRIPTION><![CDATA['.strip_tags($prod->post_content).']]></DESCRIPTION>'.PHP_EOL;
                }
                else
                {
                echo '	<DESCRIPTION><![CDATA['.strip_tags($prod->post_excerpt).']]></DESCRIPTION>'.PHP_EOL;
                }
                echo '	<URL>'.get_permalink($prod->ID).'?364'.$varid.'</URL>'.PHP_EOL;
                $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($prod->ID), 'small-feature' );
        	      $imgurl = $thumb['0'];
                if ($imagecolor == 0) {
                echo '	<IMGURL>'.$imgurl.'?233'.$varid.'</IMGURL>'.PHP_EOL;
                }
                else
                {
                echo '	<IMGURL>'.$imagecolor1.'?151'.$varid.'</IMGURL>'.PHP_EOL;
                }
                echo '	<VIDEOURL><![CDATA['.$postmetayoutube[0].']]></VIDEOURL>'.PHP_EOL;
                echo '	<MANUFACTURER><![CDATA['.get_option('barnone_sk_product_brand').']]></MANUFACTURER>'.PHP_EOL;
                echo '	<CATEGORYTEXT><![CDATA['.get_option('barnone_sk_categorytest'.$product_cat_id).']]></CATEGORYTEXT>'.PHP_EOL;
                if ($konecnacena[1] > 0)
                {
                echo '	<PRICE_VAT>'.$konecnacena[1] * $taxratecount.'</PRICE_VAT>'.PHP_EOL;
                }
                else if ($konecnacena[0] == 0)
                {

                echo '	<PRICE_VAT>'.$product->get_price_including_tax().'</PRICE_VAT>'.PHP_EOL;
                }
                else
                {
                echo '	<PRICE_VAT>'.$konecnacena[0] * $taxratecount.'</PRICE_VAT>'.PHP_EOL;
                }
                echo '	<HEUREKA_CPC>'.get_option('barnone_sk_heureka_cpc').'</HEUREKA_CPC>'.PHP_EOL;
                if (get_option('barnone_sk_product_condition') == 'bazar') {
        	      echo '	<ITEM_TYPE>'.get_option('barnone_sk_product_condition').'</ITEM_TYPE>'.PHP_EOL;
                }
                if ($postmetaeankod[0] != 0) {
                echo '	<EAN>'.$postmetaeankod[0].'</EAN>'.PHP_EOL;
                }
                if ($avail == FALSE) {
                //echo '	<DELIVERY_DATE>'.get_option('barnone_sk_delivery_date_out').'</DELIVERY_DATE>'.PHP_EOL;
                echo '	<DELIVERY_DATE>1</DELIVERY_DATE>'.PHP_EOL;
		}
                else {
                //echo '	<DELIVERY_DATE>'.get_option('barnone_sk_delivery_date').'</DELIVERY_DATE>'.PHP_EOL;
                echo '	<DELIVERY_DATE>1</DELIVERY_DATE>'.PHP_EOL;
		}
                echo '	<DELIVERY>'.PHP_EOL;
                echo '        <DELIVERY_ID>'.get_option('barnone_sk_delivery_partner').'</DELIVERY_ID>'.PHP_EOL;
                echo '        <DELIVERY_PRICE>'.get_option('barnone_sk_delivery_price').'</DELIVERY_PRICE>'.PHP_EOL;
                echo '        <DELIVERY_PRICE_COD>'.get_option('barnone_sk_delivery_price_cod').'</DELIVERY_PRICE_COD>'.PHP_EOL;
                echo '  </DELIVERY>'.PHP_EOL;
                echo '        <PARAM>'.PHP_EOL;
                echo '        <PARAM_NAME><![CDATA['.$attrname[0].']]></PARAM_NAME>'.PHP_EOL;
                echo '            <VAL><![CDATA['.$name1.']]></VAL>'.PHP_EOL;
                echo '            </PARAM>'.PHP_EOL;
                echo '	<ITEMGROUP_ID>PRODGROUP'.$prod->ID.'</ITEMGROUP_ID>'.PHP_EOL;
        				echo '</SHOPITEM>'.PHP_EOL;
// 0 variations
        }}else{
        $postmetaproductname = get_post_meta($prod->ID, '_bar_productname_kod' );
        $postmetayoutube = get_post_meta($prod->ID, '_bar_youtube_kod' );
        $postmetaeankod = get_post_meta($prod->ID, '_bar_ean_kod' );
        //product gallery
        $attachment_ids = $product->get_gallery_attachment_ids();
        $taxrate = get_option('barnone_sk_tax_rate');
        $taxratecount = $taxrate / 100 + 1;
        $avail = $product->is_in_stock();
        echo '<SHOPITEM>'.PHP_EOL;
          echo '	<ITEM_ID>'.$prod->ID.'26'.$varid.'</ITEM_ID>'.PHP_EOL;
          //echo '	<PRODUCTNAME><![CDATA['.$postmetaproductname[0].']]></PRODUCTNAME>'.PHP_EOL;
		//START UPRAV
		$nazov=str_replace("Night In Colours - ","NightInColours ",$prod->post_title);
		$nazov=str_replace(" - "," ",$nazov);
		//rozmery
if (strpos($nazov,"obliečky")>0){

		$pozicia1=strpos($prod->post_content,"Rozmer")+8;
		$pozicia2=strpos($prod->post_content,"cm");
		$pozicia2=strpos($prod->post_content,"cm",$pozicia2+1);
		$vycuc=substr($prod->post_content,$pozicia1,$pozicia2-$pozicia1);
		$vycuc=str_replace(" x ","x",$vycuc);
		$vycuc=str_replace(","," ",$vycuc);
		$vycuc=str_replace(" cm","",$vycuc);
		$nazov=$nazov . $vycuc;
		
} else {
	$pozicia1=strpos($prod->post_content,"Rozmer")+7;
		$pozicia2=strpos($prod->post_content,"cm");
		$vycuc=substr($prod->post_content,$pozicia1,$pozicia2-$pozicia1);
		$vycuc=str_replace(" x ","x",$vycuc);
		$vycuc=str_replace(","," ",$vycuc);
		$vycuc=str_replace(" cm","",$vycuc);
		//$nazov=$nazov . $vycuc;
		if (strpos($nazov,"InColours")>0){
		} else {
		$nazov="NightInColours " . $nazov . " Vaflové";
		}
}
		//KONIEC UPRAV
	  echo '	<PRODUCTNAME><![CDATA['.$nazov.']]></PRODUCTNAME>'.PHP_EOL;
          //echo '	<PRODUCT><![CDATA['.$prod->post_title.']]></PRODUCT>'.PHP_EOL;
	  echo '	<PRODUCT><![CDATA['.$nazov.']]></PRODUCT>'.PHP_EOL;
          if (get_option('barnone_sk_product_descr') == 'content')
          {
        	echo '	<DESCRIPTION><![CDATA['.strip_tags($prod->post_content).']]></DESCRIPTION>'.PHP_EOL;
          }
          else
          {
          echo '	<DESCRIPTION><![CDATA['.strip_tags($prod->post_excerpt).']]></DESCRIPTION>'.PHP_EOL;
          }
          echo '	<URL>'.get_permalink($prod->ID).'?169'.$varid.'</URL>'.PHP_EOL;
          $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($prod->ID), 'small-feature' );
        	$imgurl = $thumb['0'];
          echo '	<IMGURL>'.$imgurl.'?65'.$varid.'</IMGURL>'.PHP_EOL;
          foreach ( $attachment_ids as $attachment_id ) {
          $image_link = wp_get_attachment_url( $attachment_id );
          echo '	<IMGURL_ALTERNATIVE>'.$image_link.'?144'.$varid.'</IMGURL_ALTERNATIVE>'.PHP_EOL;
          }
          echo '	<VIDEOURL><![CDATA['.$postmetayoutube[0].']]></VIDEOURL>'.PHP_EOL;
          echo '	<MANUFACTURER><![CDATA['.get_option('barnone_sk_product_brand').']]></MANUFACTURER>'.PHP_EOL;
          echo '	<CATEGORYTEXT><![CDATA['.get_option('barnone_sk_categorytest'.$product_cat_id).']]></CATEGORYTEXT>'.PHP_EOL;
          echo '	<PRICE_VAT>'.$product->get_price_including_tax().'</PRICE_VAT>'.PHP_EOL;
          echo '	<HEUREKA_CPC>'.get_option('barnone_sk_heureka_cpc').'</HEUREKA_CPC>'.PHP_EOL;
          if (get_option('barnone_sk_product_condition') == 'bazar') {
        	echo '	<ITEM_TYPE>'.get_option('barnone_sk_product_condition').'</ITEM_TYPE>'.PHP_EOL;
          }
          if ($postmetaeankod[0] != 0) {
          echo '	<EAN>'.$postmetaeankod[0].'</EAN>'.PHP_EOL;
          }
          if ($avail == FALSE) {
          //echo '	<DELIVERY_DATE>'.get_option('barnone_sk_delivery_date_out').'</DELIVERY_DATE>'.PHP_EOL;
          echo '	<DELIVERY_DATE>1</DELIVERY_DATE>'.PHP_EOL;
	  }
          else {
          //echo '	<DELIVERY_DATE>'.get_option('barnone_sk_delivery_date').'</DELIVERY_DATE>'.PHP_EOL;
          echo '	<DELIVERY_DATE>1</DELIVERY_DATE>'.PHP_EOL;  
	  }
          echo '	<DELIVERY>'.PHP_EOL;
          echo '        <DELIVERY_ID>'.get_option('barnone_sk_delivery_partner').'</DELIVERY_ID>'.PHP_EOL;
          echo '        <DELIVERY_PRICE>'.get_option('barnone_sk_delivery_price').'</DELIVERY_PRICE>'.PHP_EOL;
          echo '        <DELIVERY_PRICE_COD>'.get_option('barnone_sk_delivery_price_cod').'</DELIVERY_PRICE_COD>'.PHP_EOL;
          echo '  </DELIVERY>'.PHP_EOL;
		    echo '</SHOPITEM>'.PHP_EOL;
      }
    }
// Displays the amount of memory being used by your code
$limit = ini_get('memory_limit')."B";
$bytes = memory_get_usage();
function bytesToSizeEnd($bytes, $precision = 2)
{
	$kilobyte = 1024;
	$megabyte = $kilobyte * 1024;
	$gigabyte = $megabyte * 1024;
	$terabyte = $gigabyte * 1024;

	if (($bytes >= 0) && ($bytes < $kilobyte)) {
		return $bytes . ' B';

	} elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
		return round($bytes / $kilobyte, $precision) . 'KB';

	} elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
		return round($bytes / $megabyte, $precision) . 'MB';

	} elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
		return round($bytes / $gigabyte, $precision) . 'GB';

	} elseif ($bytes >= $terabyte) {
		return round($bytes / $terabyte, $precision) . 'TB';
	} else {
		return $bytes . 'B';
	}
}
$start = bytesToSizeEnd($bytes);
echo "<!-- Využito ";
echo $start;
echo " z celkových ";
echo $limit;
echo " PHP paměti -->";
echo '</SHOP>';


?>