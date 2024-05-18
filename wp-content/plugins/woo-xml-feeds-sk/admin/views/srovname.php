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
 * Save Heureka category
 */  
  $srovname_categories_cpc = get_option( 'woo_srovname_categories_cpc');
    if(empty($srovname_categories_cpc)){
      $srovname_categories_cpc = array();
    }  

    foreach($_POST['termid'] as $key => $item){
      
      if(!empty($_POST['category_cpc'])){
        if(!empty($_POST['category_cpc'][$key])){
          $srovname_categories_cpc[$_POST['termid'][$key]] = $_POST['category_cpc'][$key];
        }else{
          if(!empty($srovname_categories_cpc[$key])){
            unset($srovname_categories_cpc[$key]);
          }
        }
      }else{
        delete_option( 'woo_srovname_categories_cpc');
      }
    
    }
  update_option( 'woo_srovname_categories_cpc', $srovname_categories_cpc );
  
  
    $cat_params = array();
  foreach($_POST['termvar'] as $key => $item){
    if(!empty($_POST['nazev_parametru_'.$item])){
      foreach($_POST['nazev_parametru_'.$item] as $lit => $var){
        
        $cat_params[$item][$lit]['nazev_parametru'] = $_POST['nazev_parametru_'.$item][$lit];
        $cat_params[$item][$lit]['hodnota_parametru'] = $_POST['hodnota_parametru_'.$item][$lit];
        
      }
    }
  }
  
  
  update_option( 'woo_srovname_cat_params', $cat_params );
  
  
  wp_redirect(home_url().'/wp-admin/admin.php?page=srovname');
} 



$srovname_categories_cpc      = get_option( 'woo_srovname_categories_cpc');

$cat_params = get_option( 'woo_srovname_cat_params');

$catTerms = get_terms('product_cat', array('hide_empty' => 0, 'orderby' => 'ASC')); 
$cat_list = custom_taxonomy_walker('product_cat'); 
?>




<div class="wrap">
<form method="post" action="">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

  <div class="t-col-12">
    <div class="toret-box box-info">
      <div class="box-header">
        <h3 class="box-title"><?php _e('Srovnáme.cz','woo-xml-feeds'); ?></h3>
      </div>
      <div class="box-body">
      <p><?php _e('Data pro XML soubor, který se generuje pro srovnávač Srovnáme.cz, jsou stejná, jako pro Heuréku.','woo-xml-feeds'); ?></p>
      <p><?php _e('Pokud využíváte Heuréku, nemusíte dále nic nastavovat a můžete rovnou vygenerovat a použít XML feed. Jedinou vyjímkou je cena za proklik.','woo-xml-feeds'); ?></p>
      <p><?php _e('V případě, že Heuréku nevyužíváte, využijte její nastavení a použijte vygenerovaný feed pro Srovnáme.cz ','woo-xml-feeds'); ?></p>
      </div>
    </div>
  </div> 
  
  
  
  <!-- Heureka kategorie -->
<div class="t-col-12">
    <div class="toret-box box-info" style="width:auto;">
      <div class="box-header">
        <h3 class="box-title"><?php _e('Cena za proklik, pro jednotlivé kategorie','woo-xml-feeds'); ?></h3>
      </div>
      <div class="box-body">
      <p><?php _e('Zde můžete přiřadit cenu za proklik k jednotlivým kategoriím.','woo-xml-feeds'); ?></p>
      
  <table class="table-bordered" style="width:auto;">
    <tr>
      <th><?php _e('Kategorie obchodu', 'woo-xml-feeds'); ?></th>
      <th><?php _e('TOLL pro kategorii', 'woo-xml-feeds'); ?></th>
    </tr>
   <?php 
   $i=1;
   $catTerms = explode(',',$cat_list);
   foreach($catTerms as $c_item) : 
   if(!empty($c_item)){
   ?>
    <tr>
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
        <td><input type="text" name="category_cpc[]" value="<?php if(!empty($srovname_categories_cpc[$c_item])){ echo $srovname_categories_cpc[$c_item]; } ?>" style="width:40px;" /></td>
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



  <!-- Heureka kategorie parametry -->
  <div class="t-col-12">
    <div class="toret-box box-info">
      <div class="box-header">
        <h3 class="box-title"><?php _e('Vlastní parametry pro kategorie','woo-xml-feeds'); ?></h3>
      </div>
      <div class="box-body">
        <table class="table-bordered">
          <tr>
            <th><?php _e('Kategorie eshop', 'woo-xml-feeds'); ?></th>
            <th style="width:80%;"><?php _e('Vlastní parametry', 'woo-xml-feeds'); ?></th>
          </tr>
   <?php 
   $i=1;
   $catTerms = explode(',',$cat_list);
   foreach($catTerms as $c_item) : 
   if(!empty($c_item)){
   ?>
    <tr>
        <td>
          <?php
          $catTerm = get_term_by( 'id', $c_item, 'product_cat' );
          if(!empty($catTerm->parent)){
          $p_name = get_term_by( 'id', $catTerm->parent, 'product_cat' );
          echo $p_name->name.' >> ';
          
          } 
          
          ?> <?php echo $catTerm->name; ?>
          <input type="hidden" name="termvar[]" value="<?php echo $catTerm->term_id; ?>" />
        </td>
        <td class="category_params">
          <?php 
            if(!empty($cat_params[$catTerm->term_id])){
              foreach($cat_params[$catTerm->term_id] as $lit => $var){ ?>
                 <fieldset>
                  <input type="text" name="nazev_parametru_<?php echo $catTerm->term_id; ?>[]" placeholder="Název parametru" value="<?php echo $var['nazev_parametru']; ?>" />
                  <input type="text" name="hodnota_parametru_<?php echo $catTerm->term_id; ?>[]" placeholder="Hodnota parametru" value="<?php echo $var['hodnota_parametru']; ?>"/>
                  <span class="btn btn-danger btn-sm remove-param"><i class="fa fa-times"></i></span>
                 </fieldset>
            <?php }
            }
          ?>
          <div class="clear"></div>
          <span class="btn btn-danger btn-sm add-param"data-par="<?php echo $catTerm->term_id; ?>">Přidat parametr</span>
        </td>
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
