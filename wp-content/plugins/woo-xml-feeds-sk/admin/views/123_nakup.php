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


if(isset($_POST['update'])){
/**
 *
 * Save 123 Nákup delivery
 *
 */  
 if(!empty($_POST['delivery_name'])){
  $option_123_nakup_delivery = array();
  foreach($_POST['delivery_name'] as $key => $item){
  
    $option_123_nakup_delivery[$key]['delivery_name']  = $_POST['delivery_name'][$key];
    $option_123_nakup_delivery[$key]['delivery_price'] = $_POST['delivery_price'][$key];

  
  }
  update_option( 'woo_123_nakup_delivery', $option_123_nakup_delivery );
  }

  /**
 * Save 123 Nákup category
 */  
  $nakup_assing_categories = array();
  
  $nakup_excluded_categories = get_option( 'woo_123_nakup_excluded_categories');
    if(empty($nakup_excluded_categories)){
      $nakup_excluded_categories = array();
    }
    
    foreach($_POST['termid'] as $key => $item){
      $nakup_assing_categories[$_POST['termid'][$key]] = $_POST['nakupid'][$key];
    
      if(!empty($_POST['excluded'])){
        if(!empty($_POST['excluded'][$item])){
          $nakup_excluded_categories[$item] = $_POST['termid'][$key];
        }else{
          if(!empty($nakup_excluded_categories[$item])){
            unset($nakup_excluded_categories[$item]);
          }
        }
      }else{
        delete_option( 'woo_123_nakup_excluded_categories');
        $nakup_excluded_categories = false;
      }
      
    
    }
  update_option( 'woo_123_nakup_assing_categories', $nakup_assing_categories );
  update_option( 'woo_123_nakup_excluded_categories', $nakup_excluded_categories );

  wp_redirect(admin_url().'admin.php?page=page_123_nakup');
} 



$option_123_nakup_delivery = get_option( 'woo_123_nakup_delivery' );
$nakup_assing_categories   = get_option( 'woo_123_nakup_assing_categories');
$nakup_excluded_categories = get_option( 'woo_123_nakup_excluded_categories');


$catTerms = get_terms('product_cat', array('hide_empty' => 0, 'orderby' => 'ASC')); 
$cat_list = custom_taxonomy_walker('product_cat');

?>
<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
<form method="post" action="">	


  

<!-- 123 Nákup doprava -->
 <div class="t-col-12">
    <div class="toret-box box-info">
      <div class="box-header">
        <h3 class="box-title"><?php _e('Nastavení dopravy','woo-xml-feeds'); ?></h3>
      </div>
      <div class="box-body">
   <p><?php _e('Globální nastavení cen dopravy, dle seznamu dopravy, podporovaných 123 Nákup. <a href="http://www.123-nakup.sk/lists-export/payment-options">Seznam</a>.','woo-xml-feeds'); ?></p>   
      	
   <table class="table-bordered" id="tabulka-doprava-123">
    <tr>
      <th><?php _e('Název dopravce','woo-xml-feeds'); ?></th>
      <th><?php _e('Cena za dopravu (vč. DPH)','woo-xml-feeds'); ?></th>
      <th><?php _e('Smazat','woo-xml-feeds'); ?></th>
    </tr>
    <?php 
    if(!empty($option_123_nakup_delivery)){
    foreach($option_123_nakup_delivery as $k => $item){ ?>
    <tr>
      <td><input type="text" name="delivery_name[]" value="<?php if(!empty($option_123_nakup_delivery[$k]['delivery_name'])){  echo $option_123_nakup_delivery[$k]['delivery_name'];} ?>" /></td>
      <td><input type="text" name="delivery_price[]" value="<?php if(!empty($option_123_nakup_delivery[$k]['delivery_price'])){  echo $option_123_nakup_delivery[$k]['delivery_price'];} ?>" /></td>
      <td class="td_center"><span class="btn btn-danger btn-sm remove-tr" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove"><i class="fa fa-times"></i></span></td>
    </tr> 
    <?php } 
    }
    
    ?>
   </table>
   <span class="button" id="pridatdopravu_123"><?php _e('Přidat dopravu','woo-xml-feeds'); ?></span>
   <div class="clear"></div>
      </div>
    </div>
  </div>
  
  
  
  <!-- 123 Nákup kategorie -->
<div class="t-col-12">
    <div class="toret-box box-info">
      <div class="box-header">
        <h3 class="box-title"><?php _e('Přiřazení kategorií','woo-xml-feeds'); ?></h3>
      </div>
      <div class="box-body">
      <p><?php _e('Zde můžete přiřadit všechny kategorie ve vašem obchodu, k jednotlivým kategoriím 123 Nákup. Seznam kategorií pro vložení, najdete na','woo-xml-feeds'); ?> <a href="http://www.123-nakup.sk/lists-export/product-categories" target="_blank"><?php _e('123 Nákup.','woo-xml-feeds'); ?></a></p>
      
      <p><?php _e('Pokud nechcete zobrazovat zboží z určité kategorie v XML feedu, zaškrtněte "Vyloučit kategorii".','woo-xml-feeds'); ?></p>
      
      
        <table class="table-bordered">
    <tr>
      <th style="width:100px;"><?php _e('Vyloučit kategorii', 'woo-xml-feeds'); ?></th>
      <th><?php _e('Kategorie obchodu', 'woo-xml-feeds'); ?></th>
      <th><?php _e('Kategorie na 123 Nákup', 'woo-xml-feeds'); ?></th>
    </tr>
   <?php 
   $i=1;
   $catTerms = explode(',',$cat_list);
   foreach($catTerms as $c_item) : 
   if(!empty($c_item)){
   ?>
    <tr>
        <td class="td_center"><input class="icheck_red" type="checkbox" name="excluded[<?php echo $c_item; ?>]" <?php if(!empty($nakup_excluded_categories[$c_item])){ echo 'checked="checked"'; } ?> ></td>
        <td>
          <?php
          $catTerm = get_term_by( 'id', $c_item, 'product_cat' );
          if(!empty($catTerm->parent)){
          $p_name = get_term_by( 'id', $catTerm->parent, 'product_cat' );
          echo $p_name->name.' >> ';
          
          } 
          
          ?> <?php echo $catTerm->name; ?>
          <input type="hidden" name="termid[]" value="<?php echo $catTerm->term_id; ?>" />
        </td>
        <td>
          <input type="text" name="nakupid[]" id="nakup<?php echo $i; ?>" value="<?php if(!empty($nakup_assing_categories[$catTerm->term_id])){ echo $nakup_assing_categories[$catTerm->term_id]; } ?>" style="width:100%;display:inline-block;">
        </td>
    </tr>
   <?php 
   $i++;
   }
   endforeach; 
   ?> 
   </table>
   
      </div>
    </div>
  </div>
  
  <div class="clear"></div>
      <input type="hidden" name="update" value="ok" />
      <input type="submit" class="btn btn-lg btn-warning" value="<?php _e('Uložit nastavení','woo-xml-feeds'); ?>" />

</form>

  
</div>
