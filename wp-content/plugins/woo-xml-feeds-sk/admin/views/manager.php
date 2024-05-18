<?php
/**
 *
 * @package   woo_xml_feeds
 * @author    Vladislav Musilek
 * @license   GPL-2.0+
 * @link      http://musilda.cz
 * @copyright 2014 Vladislav Musilek
 *
 * Version 1.0.0
 *  
 */
$manager = $this->get_manager();

?>

<style>.table-bordered tr th{padding:0px 6px!important;}</style>
<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
 <form method="post" action="" style="position:relative;">
    <div class="lineloader"></div>  
      <table class="table-bordered">
        
      <?php $products = $manager->get_products(); ?>
      <?php foreach( $products as $item ){ 
        $product_meta = get_post_meta($item->ID);
        $item_product = get_product($item->ID);
        $product_type = $item_product->product_type;
      ?>
        <tr>
          <th><?php _e('ID','woo-xml-feed'); ?></th>
          <th><?php _e('SKU','woo-xml-feed'); ?></th>
          <th><?php _e('Name','woo-xml-feed'); ?></th>
          <th><?php _e('Product type','woo-xml-feed'); ?></th>
          <th><?php _e('Parent ID','woo-xml-feed'); ?></th>
          <th><?php _e('Vlastní titulek','woo-xml-feed'); ?></th>
          <th><?php _e('EAN','woo-xml-feed'); ?></th>
          <th><?php _e('ISBN','woo-xml-feed'); ?></th>
          <th><?php _e('Heureka CPC','woo-xml-feed'); ?></th>
          <th><?php _e('Heureka CPC(&euro;)','woo-xml-feed'); ?></th>
          <th style="width:100px;"><?php _e('Save','woo-xml-feed'); ?></th>
        </tr>
        <tr>
          <input type="hidden" name="product_id[<?php echo $item->ID; ?>]" value="<?php echo $item->ID; ?>" />
          <td rowspan="5" class="td_center"><?php echo $item->ID; ?></td>
          <td><?php if(!empty($product_meta['_sku'][0])){ echo $product_meta['_sku'][0]; } ?></td>
          <td><?php echo $item->post_title; ?></td>
          <td class="td_center">
            <?php if($product_type == 'variable'){
              echo '<span class="btn btn-info btn-sm show-variable" data-variable="'.$item->ID.'">'.__('Show wariables','stock-manager').'</span>';
            }else{ 
              echo $product_type; 
            } ?>
          </td>
          <td></td>
          <td>
            <input type="text" name="custom_product_title[<?php echo $item->ID; ?>]" value="<?php if(!empty($product_meta['custom_product_title'][0])){ echo $product_meta['custom_product_title'][0]; } ?>" class="custom_product_title<?php echo $item->ID; ?>" />
          </td>
          <td style="width:40px;">
            <input type="text" name="ean[<?php echo $item->ID; ?>]" value="<?php if(!empty($product_meta['_ean'][0])){ echo $product_meta['_ean'][0]; } ?>" class="ean<?php echo $item->ID; ?>" style="width:40px;" />
          </td>
          <td style="width:40px;">
            <input type="text" name="isbn[<?php echo $item->ID; ?>]" value="<?php if(!empty($product_meta['_isbn'][0])){ echo $product_meta['_isbn'][0]; } ?>" class="isbn<?php echo $item->ID; ?>" style="width:40px;" />
          </td>
          <td style="width:40px;">
            <input type="text" name="heureka_cpc[<?php echo $item->ID; ?>]" value="<?php if(!empty($product_meta['heureka_cpc'][0])){ echo $product_meta['heureka_cpc'][0]; } ?>" class="heureka_cpc<?php echo $item->ID; ?>" style="width:40px;" />
          </td>
          <td style="width:40px;">
            <input type="text" name="heureka_cpc_sk[<?php echo $item->ID; ?>]" value="<?php if(!empty($product_meta['heureka_cpc_sk'][0])){ echo $product_meta['heureka_cpc_sk'][0]; } ?>" class="heureka_cpc_sk<?php echo $item->ID; ?>" style="width:60px;" />
          </td>
          <td rowspan="5" class="td_center"><span class="btn btn-primary btn-sm save-product-data" data-product="<?php echo $item->ID; ?>"><?php _e('Save','stock-manager'); ?></span></td>
        </tr>
        <tr>
          <th><?php _e('ID příslušenství','woo-xml-feed'); ?></th>
          <th><?php _e('Heureka.cz kategorie','woo-xml-feed'); ?></th>
          <th><?php _e('Heureka.sk kategorie','woo-xml-feed'); ?></th>
          <th><?php _e('Datum Expedice','woo-xml-feed'); ?></th>
          <th><?php _e('Zboží.cz kategorie','woo-xml-feed'); ?></th>
          <th><?php _e('Poplatky','woo-xml-feed'); ?></th>
          <th><?php _e('Typ zboží','woo-xml-feed'); ?></th>
          <th><?php _e('Zboží.cz zobrazování','woo-xml-feed'); ?></th>
          <th><?php _e('Zboží.cz doplňková zpráva','woo-xml-feed'); ?></th>
        </tr>
        <tr>
          <td style="width:40px;">
            <input type="text" name="accessory[<?php echo $item->ID; ?>]" value="<?php if(!empty($product_meta['accessory'][0])){ echo $product_meta['accessory'][0]; } ?>" class="accessory<?php echo $item->ID; ?>" style="width:75px;" />
          </td>
          <td>
            <input type="text" name="heureka_category[<?php echo $item->ID; ?>]" value="<?php if(!empty($product_meta['heureka_category'][0])){ echo $product_meta['heureka_category'][0]; } ?>" class="heureka_category<?php echo $item->ID; ?>" />
          </td>
          <td>
            <input type="text" name="heureka_category_sk[<?php echo $item->ID; ?>]" value="<?php if(!empty($product_meta['heureka_category_sk'][0])){ echo $product_meta['heureka_category_sk'][0]; } ?>" class="heureka_category_sk<?php echo $item->ID; ?>" />
          </td>
          <td style="width:40px;">
            <input type="text" name="delivery_date[<?php echo $item->ID; ?>]" value="<?php if(!empty($product_meta['delivery_date'][0])){ echo $product_meta['delivery_date'][0]; } ?>" class="delivery_date<?php echo $item->ID; ?>" style="width:40px;" />
          </td>
          <td>
            <input type="text" name="zbozi_category[<?php echo $item->ID; ?>]" value="<?php if(!empty($product_meta['zbozi_category'][0])){ echo $product_meta['zbozi_category'][0]; } ?>" class="zbozi_category<?php echo $item->ID; ?>" />
          </td>
          <td>
            <input type="text" name="dues[<?php echo $item->ID; ?>]" value="<?php if(!empty($product_meta['dues'][0])){ echo $product_meta['dues'][0]; } ?>" class="dues<?php echo $item->ID; ?>" style="width:40px;" />
          </td>
          <td>
            <select name="heureka_item_type[<?php echo $item->ID; ?>]" class="heureka_item_type<?php echo $item->ID; ?>">
              <option value="nove" <?php if(!empty($product_meta['heureka_item_type'][0]) && $product_meta['heureka_item_type'][0] == 'nove'){ echo 'selected="selected"'; } ?>><?php _e('Nové','stock-manager'); ?></option>
              <option value="bazar" <?php if(!empty($product_meta['heureka_item_type'][0]) && $product_meta['heureka_item_type'][0] == 'bazar'){ echo 'selected="selected"'; } ?>><?php _e('Bazarové','stock-manager'); ?></option>
            </select>
          </td>
          <td style="width:40px;">
            <select name="zbozi_unfeatured[<?php echo $item->ID; ?>]" class="zbozi_unfeatured<?php echo $item->ID; ?>">
              <option value="ano" <?php if(!empty($product_meta['zbozi_unfeatured'][0]) && $product_meta['zbozi_unfeatured'][0] == 'ano'){ echo 'selected="selected"'; } ?>><?php _e('Zobrazovat','stock-manager'); ?></option>
              <option value="ne" <?php if(!empty($product_meta['zbozi_unfeatured'][0]) && $product_meta['zbozi_unfeatured'][0] == 'ne'){ echo 'selected="selected"'; } ?>><?php _e('Nezobrazovat','stock-manager'); ?></option>
            </select>
          </td>
          <td style="width:40px;">
            <select name="zbozi_extra_message[<?php echo $item->ID; ?>]" class="zbozi_extra_message<?php echo $item->ID; ?>">
              <option value="default" <?php if(!empty($product_meta['zbozi_extra_message'][0]) && $product_meta['zbozi_extra_message'][0] == 'default'){ echo 'selected="selected"'; } ?>>Žádná zpráva</option>
              <option value="extended_warranty" <?php if(!empty($product_meta['zbozi_extra_message'][0]) && $product_meta['zbozi_extra_message'][0] == 'extended_warranty'){ echo 'selected="selected"'; } ?>>Prodloužená záruka</option>
              <option value="free_accessories" <?php if(!empty($product_meta['zbozi_extra_message'][0]) && $product_meta['zbozi_extra_message'][0] == 'free_accessories'){ echo 'selected="selected"'; } ?>>Příslušenství zdarma</option>
              <option value="free_case" <?php if(!empty($product_meta['zbozi_extra_message'][0]) && $product_meta['zbozi_extra_message'][0] == 'free_case'){ echo 'selected="selected"'; } ?>>Pouzdro zdarma</option>
              <option value="free_delivery" <?php if(!empty($product_meta['zbozi_extra_message'][0]) && $product_meta['zbozi_extra_message'][0] == 'free_delivery'){ echo 'selected="selected"'; } ?>>Doprava zdarma</option>
              <option value="free_gift" <?php if(!empty($product_meta['zbozi_extra_message'][0]) && $product_meta['zbozi_extra_message'][0] == 'free_gift'){ echo 'selected="selected"'; } ?>>Dárek zdarma</option>
              <option value="free_installation" <?php if(!empty($product_meta['zbozi_extra_message'][0]) && $product_meta['zbozi_extra_message'][0] == 'free_installation'){ echo 'selected="selected"'; } ?>>Montáž zdarma</option>
              <option value="free_store_pickup" <?php if(!empty($product_meta['zbozi_extra_message'][0]) && $product_meta['zbozi_extra_message'][0] == 'free_store_pickup'){ echo 'selected="selected"'; } ?>>Osobní odběr zdarma</option>
            </select>
          </td>
        </tr>
        <tr>
          <th><?php _e('Čas expedice balíků','woo-xml-feed'); ?></th>
          <th><?php _e('Doba do dodání','woo-xml-feed'); ?></th>
          <th><?php _e('Video url','woo-xml-feed'); ?></th>
          <th></th>
          <th><?php _e('Výrobce','woo-xml-feed'); ?></th>
          <th><?php _e('Zboží CPC','woo-xml-feed'); ?></th>
          <th><?php _e('Srovnáme TOLL','woo-xml-feed'); ?></th>
          <th colspan="2"><?php _e('Pricemania cena dopravy','woo-xml-feed'); ?></th>
        </tr>
        <tr style="border-bottom:solid 3px #000000;">
          <td>
            <input type="text" name="product_deadline_time[<?php echo $item->ID; ?>]" value="<?php if(!empty($product_meta['product_deadline_time'][0])){ echo $product_meta['product_deadline_time'][0]; } ?>" class="product_deadline_time<?php echo $item->ID; ?>" style="width:75px;" />
          </td>
          <td>
            <input type="text" name="product_delivery_time[<?php echo $item->ID; ?>]" value="<?php if(!empty($product_meta['product_delivery_time'][0])){ echo $product_meta['product_delivery_time'][0]; } ?>" class="product_delivery_time<?php echo $item->ID; ?>" />
          </td>
          <td>
            <input type="text" name="video_url[<?php echo $item->ID; ?>]" value="<?php if(!empty($product_meta['video_url'][0])){ echo $product_meta['video_url'][0]; } ?>" class="video_url<?php echo $item->ID; ?>" />
          </td>
          <td></td>
          <td>
            <input type="text" name="manufacturer[<?php echo $item->ID; ?>]" value="<?php if(!empty($product_meta['manufacturer'][0])){ echo $product_meta['manufacturer'][0]; } ?>" class="manufacturer<?php echo $item->ID; ?>" />
          </td>
          <td style="width:40px;">
            <input type="text" name="zbozi_cpc[<?php echo $item->ID; ?>]" value="<?php if(!empty($product_meta['zbozi_cpc'][0])){ echo $product_meta['zbozi_cpc'][0]; } ?>" class="zbozi_cpc<?php echo $item->ID; ?>" style="width:60px;" />
          </td>
          <td style="width:40px;">
            <input type="text" name="srovname_toll[<?php echo $item->ID; ?>]" value="<?php if(!empty($product_meta['srovname_toll'][0])){ echo $product_meta['srovname_toll'][0]; } ?>" class="srovname_toll<?php echo $item->ID; ?>" style="width:60px;" />
          </td>
          <td colspan="2">
            <input type="text" name="pricemania_shipping[<?php echo $item->ID; ?>]" value="<?php if(!empty($product_meta['pricemania_shipping'][0])){ echo $product_meta['pricemania_shipping'][0]; } ?>" class="pricemania_shipping<?php echo $item->ID; ?>" />
          </td>
        </tr>
        
        <?php 
            if($product_type == 'variable'){
                $args = array(
	               'post_parent' => $item->ID,
	               'post_type'   => 'product_variation', 
	               'numberposts' => -1,
	               'post_status' => 'publish' 
                ); 
                $variations_array = get_children( $args );
                foreach($variations_array as $vars){
             
        $product_meta = get_post_meta($vars->ID);
        $item_product = get_product($vars->ID);
        $product_type = 'product variation' ;
      ?>
        
        <tr class="variation-line variation-item-<?php echo $item->ID; ?>">
          <th style="background-color:#cbe1ec;border:solid 1px #ffffff;"><?php _e('ID','woo-xml-feed'); ?></th>
          <th style="background-color:#cbe1ec;border:solid 1px #ffffff;"><?php _e('SKU','woo-xml-feed'); ?></th>
          <th style="background-color:#cbe1ec;border:solid 1px #ffffff;"><?php _e('Name','woo-xml-feed'); ?></th>
          <th style="background-color:#cbe1ec;border:solid 1px #ffffff;"><?php _e('Product type','woo-xml-feed'); ?></th>
          <th style="background-color:#cbe1ec;border:solid 1px #ffffff;"><?php _e('Parent ID','woo-xml-feed'); ?></th>
          <th style="background-color:#cbe1ec;border:solid 1px #ffffff;" colspan="3"><?php _e('Vlastní titulek','woo-xml-feed'); ?></th>
          <th style="background-color:#cbe1ec;border:solid 1px #ffffff;" colspan="2"><?php _e('Kategorie varianty produktu','woo-xml-feed'); ?></th>
          <th style="background-color:#cbe1ec;border:solid 1px #ffffff;" style="width:100px;"><?php _e('Save','woo-xml-feed'); ?></th>
        </tr>
        <tr class="variation-line variation-item-<?php echo $item->ID; ?>">
          <input type="hidden" name="product_id[<?php echo $vars->ID; ?>]" value="<?php echo $vars->ID; ?>" />
          <td rowspan="3" class="td_center"><?php echo $vars->ID; ?></td>
          <td><?php if(!empty($product_meta['_sku'][0])){ echo $product_meta['_sku'][0]; } ?></td>
          <td><?php echo $vars->post_title; ?></td>
          <td><?php echo $product_type; ?></td>
          <td><?php echo $item->ID; ?></td>
          <td colspan="3" style="width:200px;">
            <input type="text" name="variation_heureka_title[<?php echo $vars->ID; ?>]" value="<?php if(!empty($product_meta['_variation_heureka_title'][0])){ echo $product_meta['_variation_heureka_title'][0]; } ?>" class="variation_heureka_title<?php echo $vars->ID; ?>" />  
          </td>
          <td colspan="2">
            <input type="text" name="variation_heureka_category[<?php echo $vars->ID; ?>]" value="<?php if(!empty($product_meta['_variation_heureka_category'][0])){ echo $product_meta['_variation_heureka_category'][0]; } ?>" class="variation_heureka_category<?php echo $vars->ID; ?>" />
          </td>
          <td rowspan="3" class="td_center"><span class="btn btn-primary btn-sm save-product-variation" data-product="<?php echo $vars->ID; ?>"><?php _e('Save','stock-manager'); ?></span></td>
        </tr>   
        <tr class="variation-line variation-item-<?php echo $item->ID; ?>">
          <th style="background-color:#cbe1ec;border:solid 1px #ffffff;"></th>
          <th style="background-color:#cbe1ec;border:solid 1px #ffffff;"><?php _e('Variation Video url','woo-xml-feed'); ?></th>
          <th style="background-color:#cbe1ec;border:solid 1px #ffffff;"><?php _e('Variation Datum doručení','woo-xml-feed'); ?></th>
          <th style="background-color:#cbe1ec;border:solid 1px #ffffff;"></th>
          <th style="background-color:#cbe1ec;border:solid 1px #ffffff;"><?php _e('Id příslušenství','woo-xml-feed'); ?></th>
          <th colspan="2" style="background-color:#cbe1ec;border:solid 1px #ffffff;"><?php _e('Poplatky','woo-xml-feed'); ?></th>
          <th style="background-color:#cbe1ec;border:solid 1px #ffffff;"></th>
          <th style="background-color:#cbe1ec;border:solid 1px #ffffff;"></th>
        </tr> 
        <tr class="variation-line variation-item-<?php echo $item->ID; ?>" style="border-bottom:solid 3px #000000;">
          <td></td>
          <td><input type="text" name="variation_video_url[<?php echo $vars->ID; ?>]" value="<?php if(!empty($product_meta['_variation_video_url'][0])){ echo $product_meta['_variation_video_url'][0]; } ?>" class="variation_video_url<?php echo $vars->ID; ?>" /></td>
          <td><input type="text" name="variation_delivery_date[<?php echo $vars->ID; ?>]" value="<?php if(!empty($product_meta['_variation_delivery_date'][0])){ echo $product_meta['_variation_delivery_date'][0]; } ?>" class="variation_delivery_date<?php echo $vars->ID; ?>" /></td>
          <td></td>
          <td><input type="text" name="variation_accessory[<?php echo $vars->ID; ?>]" value="<?php if(!empty($product_meta['_variation_accessory'][0])){ echo $product_meta['_variation_accessory'][0]; } ?>" class="variation_accessory<?php echo $vars->ID; ?>" /></td>
          <td colspan="2"><input type="text" name="variation_dues[<?php echo $vars->ID; ?>]" value="<?php if(!empty($product_meta['_variation_dues'][0])){ echo $product_meta['_variation_dues'][0]; } ?>" class="variation_dues<?php echo $vars->ID; ?>" /></td>
          <td></td>
          <td></td>
        </tr>   
        <?php        
                }
            }
        ?>
        
      <?php } ?>
      
      </table>
      <!--<input type="submit" name="save-all" class="btn btn-danger" value="<?php //_e('Save all','stock-manager') ?>" />-->
      </form>
      <?php echo $manager->pagination(); ?>

  
</div>
