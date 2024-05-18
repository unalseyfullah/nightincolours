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
 

if(isset($_POST['update'])){
  $zbozi_assing_categories = array();
  
  $zbozi_excluded_categories = get_option( 'woo_zbozi_excluded_categories');
  if(empty($zbozi_excluded_categories)){
    $zbozi_excluded_categories = array();
  }
  foreach($_POST['termid'] as $key => $item){
    $zbozi_assing_categories[$_POST['termid'][$key]] = $_POST['zboziid'][$key];
    
    if(!empty($_POST['excluded'])){
      if(!empty($_POST['excluded'][$item])){
        $zbozi_excluded_categories[$item] = $_POST['termid'][$key];
      }else{
        if(!empty($zbozi_excluded_categories[$item])){
          unset($zbozi_excluded_categories[$item]);
        }
      }
    }else{
      delete_option( 'woo_zbozi_excluded_categories');
      $zbozi_excluded_categories = false;
    }
    
    if(!empty($_POST['category_cpc'])){
        if(!empty($_POST['category_cpc'][$key])){
          $zbozi_categories_cpc[$_POST['termid'][$key]] = $_POST['category_cpc'][$key];
        }else{
          if(!empty($zbozi_categories_cpc[$key])){
            unset($zbozi_categories_cpc[$key]);
          }
        }
      }else{
        delete_option( 'woo_zbozi_categories_cpc');
      }
    
    
  }
  update_option( 'woo_zbozi_assing_categories', $zbozi_assing_categories );
  update_option( 'woo_zbozi_excluded_categories', $zbozi_excluded_categories );
  update_option( 'woo_zbozi_categories_cpc', $zbozi_categories_cpc );
  
  
  //CPC
  if(!empty($_POST['zbozi-cpc'])){
    $zbozi_cpc = sanitize_text_field($_POST['zbozi-cpc']);
    update_option( 'zbozi-cpc', $zbozi_cpc );
  }else{
    delete_option( 'zbozi-cpc' );
  }
  if(!empty($_POST['zbozi_unfeatured'])){
    $zbozi_unfeatured = sanitize_text_field($_POST['zbozi_unfeatured']);
    update_option( 'zbozi_unfeatured', $zbozi_unfeatured );
  }else{
    delete_option( 'zbozi_unfeatured' );
  }
  
  
  
  
  wp_redirect(home_url().'/wp-admin/admin.php?page=zbozi');
} 
 
$zbozi_assing_categories   = get_option( 'woo_zbozi_assing_categories');
$zbozi_excluded_categories = get_option( 'woo_zbozi_excluded_categories');
$zbozi_unfeatured          = get_option( 'zbozi_unfeatured'); 
$zbozi_cpc                 = get_option( 'zbozi-cpc');
$zbozi_categories_cpc      = get_option( 'woo_zbozi_categories_cpc');
 
$catTerms = get_terms('product_cat', array('hide_empty' => 0, 'orderby' => 'ASC')); 

$testTerms = get_terms('product_cat', array('hide_empty' => 0, 'orderby' => 'ASC', 'hierarchy' => true)); 

 $cat_list = custom_taxonomy_walker('product_cat');
 

?>




<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
<form method="post" action="">	


<!-- Zboží MAX CPC -->
  <div class="t-col-12">
    <div class="toret-box box-info">
      <div class="box-header">
        <h3 class="box-title"><?php _e('Globální nastavení','woo-xml-feeds'); ?></h3>
      </div>
      <div class="box-body">
      <p><?php _e('Nastavení maximální hodnoty CPC pro všechny produkty. Hodnoty pro jednotlivé produkty můžete upravit v detailu produktu.','woo-xml-feeds'); ?></p>
        <table class="table-bordered">
          <tr>
            <td><?php _e('Zboží MAX CPC','woo-xml-feeds'); ?><span class="help-ico fa fa-question"><span class="help-tooltip"><?php _e('Tagem nastavujete maximální cenu, kterou jste ochotni za proklik nabídnout. Desetinná místa oddělujte desetinnou čárkou. Maximální cena za klik je 100 Kč.','woo-xml-feeds'); ?></span></span></td>
            <td colspan="3"><input type="text" name="zbozi-cpc" value="<?php if(!empty($zbozi_cpc)){ echo $zbozi_cpc; } ?>"></td>
          </tr>
          <tr>
            <td><?php _e('Přednostní výpis','woo-xml-feeds'); ?><span class="help-ico fa fa-question"><span class="help-tooltip"><?php _e('Vyberte, zda se budou vaše produkty zobrazovat jako přednostní.','woo-xml-feeds'); ?></span></span></td>
            <td colspan="3">
            <select name="zbozi_unfeatured" id="zbozi_unfeatured">
              <option <?php if(!empty($zbozi_unfeatured) && $zbozi_unfeatured=='ano'){ echo 'selected="selected"'; }; ?> value="ano"><?php _e('Upřednostněné','woo-xml-feeds'); ?></option>
              <option <?php if(!empty($zbozi_unfeatured) && $zbozi_unfeatured=='ne'){ echo 'selected="selected"'; }; ?> value="ne"><?php _e('Neupřednostněné','woo-xml-feeds'); ?></option>
            </select>
            
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div> 

<div class="t-col-12">
    <div class="toret-box box-info">
      <div class="box-header">
        <h3 class="box-title"><?php _e('Přiřazení kategorií','woo-xml-feeds'); ?></h3>
      </div>
      <div class="box-body">
      <p><?php _e('Zde můžete přiřadit všechny kategorie ve vašem obchodu, k jednotlivým kategoriím Zboží. Vyberte kategorii a vepište ji do políčka u odpovídající kategorie vašeho obchodu.','woo-xml-feeds'); ?></p>
      
      <p><?php _e('Pokud nechcete zobrazovat zboží z určité kategorie v XML feedu, zaškrtněte "Vyloučit kategorii".','woo-xml-feeds'); ?></p>
        <table class="table-bordered">
    <tr>
      <th><?php _e('Vyloučit kategorii', 'woo-xml-feeds'); ?></th>
      <th><?php _e('Kategorie obchodu', 'woo-xml-feeds'); ?></th>
      <th style="min-width:600px;"><?php _e('Kategorie na Zboží.cz', 'woo-xml-feeds'); ?></th>
      <th><?php _e('CPC kategorie', 'woo-xml-feeds'); ?></th>
    </tr>
   <?php 
   $i=1;
   $catTerms = explode(',',$cat_list);
   foreach($catTerms as $c_item) : 
   if(!empty($c_item)){
   ?>
    <tr>
        <td class="td_center"><input class="icheck" type="checkbox" name="excluded[<?php echo $c_item; ?>]" <?php if(!empty($zbozi_excluded_categories[$c_item])){ echo 'checked="checked"'; } ?> ></td>
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
          <input style="width:100%;" type="text" name="zboziid[]" placeholder="<?php _e('Vepište kategorii ve tvaru: Foto | Paměťové karty | Compact Flash','woo-xml-feeds'); ?>" id="zbozi<?php echo $i; ?>" value="<?php echo $zbozi_assing_categories[$catTerm->term_id] ?>">
        </td>
        <td><input type="text" name="category_cpc[]" value="<?php if(!empty($zbozi_categories_cpc[$c_item])){ echo $zbozi_categories_cpc[$c_item]; } ?>" style="width:40px;" /></td>
    </tr>
   <?php 
   $i++;
   }
   endforeach; 
   ?> 
   </table>
   
   <input type="hidden" name="update" value="ok" />
   <input type="submit" class="btn btn-lg btn-warning" value="<?php _e('Uložit nastavení','woo-xml-feeds'); ?>" />

      </div>
    </div>
  </div>
   
</form>

  
</div>
