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
$doprava_sk = array(); 

$doprava_sk[] = 'SLOVENSKA_POSTA';
$doprava_sk[] = 'DPD';
$doprava_sk[] = 'DHL';
$doprava_sk[] = 'EXPRES_KURIER';
$doprava_sk[] = 'GEBRUDER_WEISS';
$doprava_sk[] = 'GEIS';
$doprava_sk[] = 'GENERAL_PARCEL';
$doprava_sk[] = 'GLS';
$doprava_sk[] = 'HDS';
$doprava_sk[] = 'INTIME';
$doprava_sk[] = 'PPL';
$doprava_sk[] = 'RADIALKA';
$doprava_sk[] = 'REMAX';
$doprava_sk[] = 'TNT';
$doprava_sk[] = 'TOPTRANS';
$doprava_sk[] = 'UPS';
$doprava_sk[] = 'VLASTNA_PREPRAVA'; 

 

if(isset($_POST['update'])){
/**
 *
 * Save Heureka delivery
 *
 */  
  $heureka_delivery = array();
  foreach($_POST['delivery_id'] as $key => $item){
  
    $heureka_delivery[$_POST['delivery_id'][$item]]['id'] = $_POST['delivery_id'][$item];
    $heureka_delivery[$_POST['delivery_id'][$item]]['delivery_price'] = $_POST['delivery_price'][$item];
    $heureka_delivery[$_POST['delivery_id'][$item]]['delivery_price_cod'] = $_POST['delivery_price_cod'][$item];
  if(!empty($_POST['delivery_active'][$item])){  
    $heureka_delivery[$_POST['delivery_id'][$item]]['active'] = 'on';
  }else{
    $heureka_delivery[$_POST['delivery_id'][$item]]['active'] = 'no';
  }
  
  }
  update_option( 'woo_heureka_delivery_sk', $heureka_delivery );
  
  //CPC
  if(!empty($_POST['heureka-cpc'])){
    $heureka_cpc = sanitize_text_field($_POST['heureka-cpc']);
    update_option( 'heureka-cpc_sk', $heureka_cpc );
  }else{
    delete_option( 'heureka-cpc_sk' );
  }
  

/**
 * Save Heureka category
 */  
  $heureka_assing_categories = get_option('woo_heureka_assing_categories_sk');
    if(empty($heureka_assing_categories)){ $heureka_assing_categories = array(); }
  
  
  $heureka_excluded_categories = get_option( 'woo_heureka_excluded_categories_sk');
    if(empty($heureka_excluded_categories)){ $heureka_excluded_categories = array(); }
    
    
  $heureka_categories_cpc = get_option( 'woo_heureka_categories_cpc_sk');
    if(empty($heureka_categories_cpc)){ $heureka_categories_cpc = array(); }  
    
    
    
    foreach($_POST['termid'] as $key => $item){
    
    
    if(!empty($_POST['heurekaid'])){
    if(!empty($_POST['heurekaid'][$key])){
      $heureka_assing_categories[$_POST['termid'][$key]] = $_POST['heurekaid'][$key];
    }else{
      if(!empty($heureka_assing_categories[$_POST['termid'][$key]])){
         unset($heureka_assing_categories[$_POST['termid'][$key]]);
      }
    }
    }
    
    if(!empty($_POST['excluded'][$key])){
      $heureka_excluded_categories[$_POST['termid'][$key]] = $_POST['termid'][$key];
    }else{
      if(!empty($heureka_excluded_categories[$_POST['termid'][$key]])){
         unset($heureka_excluded_categories[$_POST['termid'][$key]]);
      }
    }
    
    
        if(!empty($_POST['category_cpc'][$key])){
          $heureka_categories_cpc[$_POST['termid'][$key]] = $_POST['category_cpc'][$key];
        }else{
          if(!empty($heureka_categories_cpc[$_POST['termid'][$key]])){
            unset($heureka_categories_cpc[$_POST['termid'][$key]]);
          }
        }
    
    
    
    
    }

  
  update_option( 'woo_heureka_assing_categories_sk', $heureka_assing_categories );
  update_option( 'woo_heureka_excluded_categories_sk', $heureka_excluded_categories );
  update_option( 'woo_heureka_categories_cpc_sk', $heureka_categories_cpc );
  
  
  $cat_params_default = get_option( 'woo_heureka_cat_params');
  $cat_params = array();
  foreach($_POST['termvar'] as $key => $item){
    if(!empty($_POST['nazev_parametru_'.$item])){
      foreach($_POST['nazev_parametru_'.$item] as $lit => $var){
        
        $cat_params[$item][$lit]['nazev_parametru'] = $_POST['nazev_parametru_'.$item][$lit];
        $cat_params[$item][$lit]['hodnota_parametru'] = $_POST['hodnota_parametru_'.$item][$lit];
        
      }
    }
  } 
  $cat_params = array_merge($cat_params_default,$cat_params);
  update_option( 'woo_heureka_cat_params_sk', $cat_params ); 
  if(isset($_GET['catoffset'])){
    wp_redirect(admin_url().'admin.php?page=heureka-sk&catoffset='.$_GET['catoffset']);
  }else{
    wp_redirect(admin_url().'admin.php?page=heureka-sk');
  }
  
  
  
  
  wp_redirect(admin_url().'admin.php?page=heureka-sk');
} 



delete_option('woo_heureka_categories_sk'); 
$heureka_categories = get_option( 'woo_heureka_categories_sk');
//var_dump($heureka_categories);
$cat_params = get_option( 'woo_heureka_cat_params_sk');
/**
 *
 *  Option is empty, load xml feed
 *
 */  
  if(empty($heureka_categories)){
global $heureka_categories;
$heureka_categories = array();

  require_once(ABSPATH.'wp-load.php');   
  require_once(ABSPATH.'wp-includes/option.php');
  /**
  *
  * Load Xml file
  *
  */ 
  //SK   
  $xml ='http://www.heureka.sk/direct/xml-export/shops/heureka-sekce.xml';
  $feed = simplexml_load_file($xml);

  foreach($feed->CATEGORY as $first){
    $first_id   = (string)$first->CATEGORY_ID;
    $first_name = (string)$first->CATEGORY_NAME;
    $heureka_categories[$first_id]['category_id'] = $first_id;
    $heureka_categories[$first_id]['category_name'] = $first_name;
    $heureka_categories[$first_id]['category_fullname'] = '';
    $this->heureka_xml_loop($first->CATEGORY,$first_id);
  }

  if ( get_option( 'woo_heureka_categories_sk' ) !== false ) {
    update_option( 'woo_heureka_categories_sk', $heureka_categories );
  } else {
    add_option( 'woo_heureka_categories_sk', $heureka_categories );
  } 
$heureka_categories = get_option( 'woo_heureka_categories_sk');
}


$heureka_delivery = get_option( 'woo_heureka_delivery_sk' );
$heureka_cpc      = get_option( 'heureka-cpc_sk' );
$heureka_kurz     = get_option( 'heureka-kurz' );

$heureka_assing_categories   = get_option( 'woo_heureka_assing_categories_sk');
$heureka_excluded_categories = get_option( 'woo_heureka_excluded_categories_sk');
$heureka_categories_cpc      = get_option( 'woo_heureka_categories_cpc_sk');
 

$catTerms = get_terms('product_cat', array('hide_empty' => 0, 'orderby' => 'ASC')); 
$cat_list = custom_taxonomy_walker('product_cat');
$use_select2       = get_option( 'woo_xml_feed_use_select2' );

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

<!-- Heureka CPC -->
  <div class="t-col-12">
    <div class="toret-box box-info">
      <div class="box-header">
        <h3 class="box-title"><?php _e('Heuréka CPC','woo-xml-feeds'); ?></h3>
      </div>
      <div class="box-body">
      <p><?php _e('Nastavení hodnoty CPC pro všechny produkty. Hodnoty pro jednotlivé produkty můžete upravit v detailu produktu.','woo-xml-feeds'); ?></p>
        <table class="table-bordered">
          <tr>
            <td><?php _e('Heuréka CPC','woo-xml-feeds'); ?><span class="help-ico fa fa-question"><span class="help-tooltip"><?php _e('Tagem nastavujete maximální cenu, kterou jste ochotni za proklik nabídnout. Desetinná místa oddělujte desetinnou čárkou. Maximální cena za klik je 100 Kč.','woo-xml-feeds'); ?></span></span></td>
            <td colspan="3"><input type="text" name="heureka-cpc" value="<?php if(!empty($heureka_cpc)){ echo $heureka_cpc; } ?>"></td>
          </tr>
          
        </table>
      </div>
    </div>
  </div> 

  

<!-- Heureka doprava -->
 <div class="t-col-12">
    <div class="toret-box box-info">
      <div class="box-header">
        <h3 class="box-title"><?php _e('Nastavení dopravy','woo-xml-feeds'); ?></h3>
      </div>
      <div class="box-body">
   <p><?php _e('Globální nastavení cen dopravy, dle seznamu dopravců, podporovaných Heurékou. Seznam je pravidelně aktualizován ze serveru Heuréky a data jsou proto vždy aktuální. Pro každého, vámi používaného dopravce zadejte cenu za dopravu a cenu za dopravu na dobírku. V XML feedu se bude zobrazovat až poté, co bude označen jako aktivní.','woo-xml-feeds'); ?></p>   
      	
   <table class="table-bordered" id="tabulka-doprava">
    <tr>
      <th><?php _e('Id dopravce','woo-xml-feeds'); ?></th>
      <th><?php _e('Cena za dopravu (vč. DPH)','woo-xml-feeds'); ?></th>
      <th><?php _e('Cena za dopravu na dobírku (vč. DPH)','woo-xml-feeds'); ?></th>
      <th><?php _e('Aktivní','woo-xml-feeds'); ?></th>
      <th><?php _e('Smazat','woo-xml-feeds'); ?></th>
    </tr>
    <?php 
    if(!empty($heureka_delivery)){
    foreach($heureka_delivery as $item){ ?>
    <tr>
      <td><?php echo $item['id']; ?>
          <input type="hidden" name="delivery_id[<?php echo $item['id']; ?>]" value="<?php echo $item['id']; ?>">
      </td>
      <td><input type="text" name="delivery_price[<?php echo $item['id']; ?>]" value="<?php if(!empty($heureka_delivery[$item['id']]['delivery_price'])){  echo $heureka_delivery[$item['id']]['delivery_price'];} ?>"></td>
      <td><input type="text" name="delivery_price_cod[<?php echo $item['id']; ?>]" value="<?php if(!empty($heureka_delivery[$item['id']]['delivery_price_cod'])){  echo $heureka_delivery[$item['id']]['delivery_price_cod'];} ?>"></td>
      <td class="td_center"><input class="icheck" type="checkbox" name="delivery_active[<?php echo $item['id']; ?>]" <?php if(!empty($heureka_delivery[$item['id']]['active']) && $heureka_delivery[$item['id']]['active'] != 'no'){  echo 'checked="checked"'; } ?> ></td>
      <td class="td_center"><span class="btn btn-danger btn-sm remove-tr" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove"><i class="fa fa-times"></i></span></td>
    </tr> 
    <?php } 
    }
    
    ?>
   </table>
   <select id="doprava">
    <?php 
    if(!empty($doprava_sk)){
    foreach($doprava_sk as $item){ ?>
      <option value="<?php echo $item; ?>"><?php echo $item; ?></option>
    <?php } 
    }
    ?>
   </select> 
   <span class="button" id="pridatdopravu"><?php _e('Přidat dopravu','woo-xml-feeds'); ?></span>
   <div class="clear"></div>
      </div>
    </div>
  </div>


<?php echo tax_pagination($cat_list); ?>

<!-- Heureka kategorie -->
<div class="t-col-12">
    <div class="toret-box box-info">
      <div class="box-header">
        <h3 class="box-title"><?php _e('Přiřazení kategorií','woo-xml-feeds'); ?></h3>
      </div>
      <div class="box-body">
      <p><?php _e('Zde můžete přiřadit všechny kategorie ve vašem obchodu, k jednotlivým kategoriím Heuréky. Pokud máte v eshopu větší množství kategorií, buďte prosím trpěliví, načtení může chvíli trvat. Po rozkliknutí výběru Heuréka kategorie, můžete do řádku zapsat počáteční písmena hledané kategorie a použít našeptávač.','woo-xml-feeds'); ?></p>
      
      <p><?php _e('Pokud nechcete zobrazovat zboží z určité kategorie v XML feedu, zaškrtněte "Vyloučit kategorii".','woo-xml-feeds'); ?></p>
        <table class="table-bordered">
    <tr>
      <th><?php _e('Vyloučit kategorii', 'woo-xml-feeds'); ?></th>
      <th><?php _e('Kategorie obchodu', 'woo-xml-feeds'); ?></th>
      <th><?php _e('Kategorie na Heuréce', 'woo-xml-feeds'); ?></th>
      <th><?php _e('CPC kategorie', 'woo-xml-feeds'); ?></th>
    </tr>
   <?php 
   $aa = 0;
   $catTerms = explode(',',$cat_list);
   foreach($catTerms as $c_item) : 
   if(!empty($c_item) && $i >= $catstart){
   if($i > $catend){ break; }
   ?>
    <tr>
        <td class="td_center"><input class="icheck_red" type="checkbox" name="excluded[<?php echo $aa; ?>]" <?php if(!empty($heureka_excluded_categories[$c_item])){ echo 'checked="checked"'; } ?> value="<?php echo $c_item; ?>" ></td>
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
            jQuery(document).ready(function() { jQuery("#heureka<?php echo $i; ?>").select2(); });
          </script>
        <?php } ?>  
        <style>#s2id_heureka<?php echo $i; ?>{min-width:800px;}</style>
          <select name="heurekaid[]" id="heureka<?php echo $i; ?>">
            <option value="default"></option>
            <?php
              foreach($heureka_categories as $key => $item){ 
              if(!empty($item['category_fullname'])){
              ?> 
            <option <?php if(!empty($heureka_assing_categories[$catTerm->term_id]) && $heureka_assing_categories[$catTerm->term_id]==$key){ echo 'selected="selected"'; }; ?>value="<?php echo $key; ?>"><?php echo $item['category_fullname']; ?></option>  
            <?php  }
            }
            ?>
          </select>
        </td>
        <td><input type="text" name="category_cpc[]" value="<?php if(!empty($heureka_categories_cpc[$c_item])){ echo $heureka_categories_cpc[$c_item]; } ?>" style="width:40px;" /></td>
    </tr>
   <?php 
   }
   $i++;
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
   $catTerms = explode(',',$cat_list);
   foreach($catTerms as $c_item) : 
   if(!empty($c_item) && $ii >= $catstart){
   if($ii > $catend){ break; }
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
   }
   $ii++;
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
