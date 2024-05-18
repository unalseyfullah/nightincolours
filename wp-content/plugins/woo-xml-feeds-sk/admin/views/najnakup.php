<?php
/**
 *
 * @package   najnakup_xml
 * @author    Vladislav Musilek
 * @license   GPL-2.0+
 * @link      http://toret.cz
 * @copyright 2014 Vladislav Musilek
 *
 * Version 1.1.0
 *  
 */
 

if(isset($_POST['update'])){
  
  $najnakup_excluded_categories = get_option( 'woo_najnakup_excluded_categories');
  if(empty($najnakup_excluded_categories)){
    $najnakup_excluded_categories = array();
  }
  foreach($_POST['termid'] as $key => $item){
    
    if(!empty($_POST['excluded'])){
      if(!empty($_POST['excluded'][$item])){
        $najnakup_excluded_categories[$item] = $_POST['termid'][$key];
      }else{
        if(!empty($najnakup_excluded_categories[$item])){
          unset($najnakup_excluded_categories[$item]);
        }
      }
    }else{
      delete_option( 'woo_najnakup_excluded_categories');
      $najnakup_excluded_categories = false;
    }
  }
 
 if(!empty($_POST['najnakup-shipping'])){ 
   $najnakup_shipping = $_POST['najnakup-shipping'];
 }else{
   $najnakup_shipping = '0';
 }
 if(!empty($_POST['najnakup-availability'])){ 
   $najnakup_availability = $_POST['najnakup-availability'];
 }else{
   $najnakup_availability = 'skladom';
 }
 
  update_option( 'woo_najnakup_excluded_categories', $najnakup_excluded_categories );
  update_option( 'woo_najnakup_delivery', $najnakup_shipping );
  update_option( 'woo_najnakup_availability', $najnakup_availability );
  
  wp_redirect(home_url().'/wp-admin/admin.php?page=najnakup');
} 
 
$najnakup_excluded_categories = get_option( 'woo_najnakup_excluded_categories');
$najnakup_shipping            = get_option( 'woo_najnakup_delivery');
$najnakup_availability        = get_option( 'woo_najnakup_availability'); 
 
$catTerms = get_terms('product_cat', array('hide_empty' => 0, 'orderby' => 'ASC')); 

$testTerms = get_terms('product_cat', array('hide_empty' => 0, 'orderby' => 'ASC', 'hierarchy' => true)); 

$cat_list = custom_taxonomy_walker('product_cat');
 

?>




<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
<form method="post" action="">	


<!-- najnakup shipping -->
  <div class="t-col-12">
    <div class="toret-box box-info">
      <div class="box-header">
        <h3 class="box-title"><?php _e('Globální nastavení','woo-xml-feeds'); ?></h3>
      </div>
      <div class="box-body">
      <p><?php _e('Nastavení ceny za dopravu. Tato hodota se objeví u každého produktu ve feedu. Hodnoty pro jednotlivé produkty můžete upravit v detailu produktu.','woo-xml-feeds'); ?></p>
        <table class="table-bordered">
          <tr>
            <td><?php _e('Cena za dopravu','woo-xml-feeds'); ?><span class="help-ico fa fa-question"><span class="help-tooltip"><?php _e('Tagem nastavujete cenu za dopravu Desetinná místa oddělujte desetinnou tečkou.','woo-xml-feeds'); ?></span></span></td>
            <td colspan="3"><input type="text" name="najnakup-shipping" value="<?php if(!empty($najnakup_shipping)){ echo $najnakup_shipping; }else{ echo '0'; } ?>"></td>
          </tr>
          <tr>
            <td><?php _e('Dostupnost','woo-xml-feeds'); ?><span class="help-ico fa fa-question"><span class="help-tooltip"><?php _e('Tagem nastavujete dostupnost zboží, použijte text, například "skladom".','woo-xml-feeds'); ?></span></span></td>
            <td colspan="3"><input type="text" name="najnakup-availability" value="<?php if(!empty($najnakup_availability)){ echo $najnakup_availability; }else{ echo 'skladom'; } ?>"></td>
          </tr>
        </table>
      </div>
    </div>
  </div> 

<div class="t-col-12">
    <div class="toret-box box-info">
      <div class="box-header">
        <h3 class="box-title"><?php _e('Vyloučení kategorie','woo-xml-feeds'); ?></h3>
      </div>
      <div class="box-body">
      
      <p><?php _e('Pokud nechcete zobrazovat zboží z určité kategorie v XML feedu, zaškrtněte "Vyloučit kategorii".','woo-xml-feeds'); ?></p>
        
        
  <table class="table-bordered">
    <tr>
      <th><?php _e('Vyloučit kategorii', 'woo-xml-feeds'); ?></th>
      <th><?php _e('Kategorie obchodu', 'woo-xml-feeds'); ?></th>
    </tr>
   <?php 
   $i=1;
   $catTerms = explode(',',$cat_list);
   foreach($catTerms as $c_item) : 
   if(!empty($c_item)){
   ?>
    <tr>
        <td class="td_center"><input class="icheck" type="checkbox" name="excluded[<?php echo $c_item; ?>]" <?php if(!empty($najnakup_excluded_categories[$c_item])){ echo 'checked="checked"'; } ?> ></td>
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
