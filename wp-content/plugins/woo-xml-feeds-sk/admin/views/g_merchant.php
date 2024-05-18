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


/**
 * Save eshop data
 *
 */  
 if(!empty($_POST['google-jmeno-eshopu'])){
      update_option('google-jmeno-eshopu',$_POST['google-jmeno-eshopu']);
 }else{
      delete_option('google-jmeno-eshopu');
 }
 if(!empty($_POST['google-link-eshopu'])){
      update_option('google-link-eshopu',$_POST['google-link-eshopu']);
 }else{
      delete_option('google-link-eshopu');
 }
 if(!empty($_POST['google-popis-eshopu'])){
      update_option('google-popis-eshopu',$_POST['google-popis-eshopu']);
 }else{
      delete_option('google-popis-eshopu');
 }
 if(!empty($_POST['google-varianty'])){
      update_option('google-varianty',esc_attr($_POST['google-varianty']));
 }else{
      delete_option('google-varianty');
 }

/**
 * Save Google category
 */  
  $google_assing_categories = get_option('woo_google_assing_categories');
    if(empty($google_assing_categories)){ $google_assing_categories = array(); }
  
  
  $google_excluded_categories = get_option( 'woo_google_excluded_categories');
    if(empty($google_excluded_categories)){ $google_excluded_categories = array(); }
    
    
    foreach($_POST['termid'] as $key => $item){
    
    
    if(!empty($_POST['googleid'])){
    if(!empty($_POST['googleid'][$key])){
      $google_assing_categories[$_POST['termid'][$key]] = $_POST['googleid'][$key];
    }else{
      if(!empty($google_assing_categories[$_POST['termid'][$key]])){
         unset($google_assing_categories[$_POST['termid'][$key]]);
      }
    }
    }
    
    if(!empty($_POST['excluded'][$key])){
      $google_excluded_categories[$_POST['termid'][$key]] = $_POST['termid'][$key];
    }else{
      if(!empty($google_excluded_categories[$_POST['termid'][$key]])){
         unset($google_excluded_categories[$_POST['termid'][$key]]);
      }
    }
    
    }

  
  update_option( 'woo_google_assing_categories', $google_assing_categories );
  update_option( 'woo_google_excluded_categories', $google_excluded_categories );
  
  
  if(isset($_GET['catoffset'])){
    wp_redirect(admin_url().'admin.php?page=google-nakupy&catoffset='.$_GET['catoffset']);
  }else{
    wp_redirect(admin_url().'admin.php?page=google-nakupy');
  }
  
} 



//Get Google categories 
$google_categories = $this->get_google_categories();

$google_assing_categories   = get_option( 'woo_google_assing_categories');
$google_excluded_categories = get_option( 'woo_google_excluded_categories');
 
 

$catTerms = get_terms('product_cat', array('hide_empty' => 0, 'orderby' => 'ASC')); 
$cat_list = custom_taxonomy_walker('product_cat');

$use_select2 = get_option( 'woo_xml_feed_use_select2' );

/**
 * Cat pagging
 *
 */  
$limit = 50;
if(isset($_GET['catoffset'])){
  $catstart = (($_GET['catoffset'] * $limit) - $limit)+1;
  $catend = $_GET['catoffset'] * $limit;
}else{ 
  $catstart = 1;
  $catend = $limit; 
} 

$i = 1;
$ii = 1;


?>




<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
<form method="post" action="">	


<?php
  //Include Google nákupy setting
  include('modules/google_nakupy_setting.php');
  
  
  //Display category setting paggination
  echo tax_pagination($cat_list); 
  
  
  ?>

<!-- Google kategorie -->
<div class="t-col-12">
    <div class="toret-box box-info">
      <div class="box-header">
        <h3 class="box-title"><?php _e('Přiřazení kategorií','woo-xml-feeds'); ?></h3>
      </div>
      <div class="box-body">
      <p><?php _e('Zde můžete přiřadit všechny kategorie ve vašem obchodu, k jednotlivým kategoriím Google Nákupy. Pokud máte v eshopu větší množství kategorií, buďte prosím trpěliví, načtení může chvíli trvat. Po rozkliknutí výběru Google nákupy kategorie, můžete do řádku zapsat počáteční písmena hledané kategorie a použít našeptávač.','woo-xml-feeds'); ?></p>
      
      <p><?php _e('Pokud nechcete zobrazovat zboží z určité kategorie v XML feedu, zaškrtněte "Vyloučit kategorii".','woo-xml-feeds'); ?></p>
      
      
        <table class="table-bordered">
          <tr>
            <th><?php _e('Vyloučit kategorii', 'woo-xml-feeds'); ?></th>
            <th><?php _e('Kategorie obchodu', 'woo-xml-feeds'); ?></th>
            <th><?php _e('Kategorie na Google nákupy', 'woo-xml-feeds'); ?></th>
          </tr>
   <?php 
   
   $catTerms = explode(',',$cat_list);
   $aa = 0; 
   foreach($catTerms as $c_item) : 
   if(!empty($c_item) && $i >= $catstart){
   if($i > $catend){ break; }
   ?>
    <tr>
        <td class="td_center"><input class="icheck_red" type="checkbox" name="excluded[<?php echo $aa; ?>]" <?php if(!empty($google_excluded_categories[$c_item])){ echo 'checked="checked"'; } ?> value="<?php echo $c_item; ?>" ></td>
        <td>
          <?php
          $aa++;
          $catTerm = get_term_by( 'id', $c_item, 'product_cat' );
          if(!empty($catTerm->parent)){
          $p_name = get_term_by( 'id', $catTerm->parent, 'product_cat' );
          echo $p_name->name.' >> ';
          
          } 
          
          ?> <?php echo $catTerm->name; ?>
          <input type="hidden" name="termid[]" value="<?php echo $catTerm->term_id; ?>" />
        </td>
        <td>
        <?php 
          if(!empty($use_select2) && $use_select2 == 'no'){}else{ 
        ?>
          <script>
            jQuery(document).ready(function() { jQuery("#google<?php echo $i; ?>").select2(); });
          </script>
        <?php } ?>  
        <style>#s2id_google<?php echo $i; ?>{min-width:800px;}</style>
          <select name="googleid[]" id="google<?php echo $i; ?>">
          <option value="default"></option>
            <?php
              foreach($google_categories as $key => $item){ 
              if(!empty($item['category_fullname'])){
              ?> 
            <option <?php if(!empty($google_assing_categories[$catTerm->term_id]) && $google_assing_categories[$catTerm->term_id]==$key){ echo 'selected="selected"'; }; ?>value="<?php echo $key; ?>"><?php echo $item['category_fullname']; ?></option>  
            <?php  }
            }
            ?>
          </select>
        </td>
    </tr>
   <?php 
   }
   $i++;
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
