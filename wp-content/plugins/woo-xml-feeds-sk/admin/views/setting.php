<?php
/**
 *
 * @package   Woo XML Feeds
 * @author    Vladislav Musilek
 * @license   GPL-2.0+
 * @link      http://toret.cz
 * @copyright 2014 Toret
 */
 
if(isset($_POST['control'])){ 
  if(!empty($_POST['licence'])){
    if(trim($_POST['licence'])!=''){  
      wooshop_xml_feeds_control_licence($_POST['licence']); 
    }
  }
  wp_redirect(admin_url().'admin.php?page=woo-xml-feeds');
   
}   
 
 
if(isset($_POST['update'])){
  //deadline_time
  if(!empty($_POST['deadline_time'])){
    $deadline_time = sanitize_text_field($_POST['deadline_time']);
    update_option( 'deadline_time', $deadline_time );
  }else{
    delete_option( 'deadline_time' );
  }
  //delivery_time
  if(!empty($_POST['delivery_time'])){
    $delivery_time = sanitize_text_field($_POST['delivery_time']);
    update_option( 'delivery_time', $delivery_time );
  }else{
    delete_option( 'delivery_time' );
  }
  
  //MANUFAKTURER
  if(!empty($_POST['manufakturer'])){
    $manufakturer = sanitize_text_field($_POST['manufakturer']);
    update_option( 'manufakturer', $manufakturer );
  }else{
    delete_option( 'manufakturer' );
  }
  //
  if(!empty($_POST['delivery_date'])){
    $delivery_date = sanitize_text_field($_POST['delivery_date']);
    update_option( 'delivery_date', $delivery_date );
  }else{
    delete_option( 'delivery_date' );
  }
  if(!empty($_POST['heureka_hide_bazar'])){
    $heureka_hide_bazar = sanitize_text_field($_POST['heureka_hide_bazar']);
    update_option( 'heureka_hide_bazar', $heureka_hide_bazar );
  }
  if(!empty($_POST['heureka_item_type'])){
    $heureka_item_type = sanitize_text_field($_POST['heureka_item_type']);
    update_option( 'heureka_item_type', $heureka_item_type );
  }
  if(!empty($_POST['use_select2'])){
    $use_select2 = sanitize_text_field($_POST['use_select2']);
    update_option( 'woo_xml_feed_use_select2', $use_select2 );
  }
  if(!empty($_POST['use_excerpt'])){
    $use_excerpt = sanitize_text_field($_POST['use_excerpt']);
    update_option( 'use_excerpt', $use_excerpt );
  }
  
  wp_redirect(home_url().'/wp-admin/admin.php?page=woo-xml-feeds');
  
}  

if(isset($_GET['generate'])){
  if($_GET['generate']=='heurekacz'){
    include(WOOXMLDIR.'feed-heureka-cz.php');
  }
  if($_GET['generate']=='heurekask'){
    include(WOOXMLDIR.'feed-heureka-sk.php');
  }
  if($_GET['generate']=='zbozi'){
    include(WOOXMLDIR.'feed-zbozi.php');
  }
  if($_GET['generate']=='srovname'){
    include(WOOXMLDIR.'feed-srovname.php');
  }
  if($_GET['generate']=='pricemania'){
    include(WOOXMLDIR.'feed-pricemania-cz.php');
  }
  if($_GET['generate']=='pricemania-sk'){
    include(WOOXMLDIR.'feed-pricemania-sk.php');
  }
  if($_GET['generate']=='najnakup'){
    include(WOOXMLDIR.'feed-najnakup.php');
  }


  wp_redirect(admin_url().'admin.php?page=woo-xml-feeds');
}
 
  

$licence_key  = get_option('wooshop-xml-feeds-licence-key');
$licence_info = get_option('wooshop-xml-feeds-info');
global $lic; 


$manufakturer      = get_option( 'manufakturer' );
$delivery_date     = get_option( 'delivery_date' );
if(empty($delivery_date)){ $delivery_date = '0'; }

$heureka_item_type = get_option( 'heureka_item_type' );
$heureka_hide_bazar = get_option( 'heureka_hide_bazar' );
$use_excerpt       = get_option( 'use_excerpt' );
$deadline_time     = get_option( 'deadline_time' );
$delivery_time     = get_option( 'delivery_time' );
$use_select2        = get_option( 'woo_xml_feed_use_select2' );
?>

<div class="wrap">

	
  <h2><?php _e('Woocommerce XML feed','woo-xml-feeds'); ?></h2>
	
  <div class="t-col-12">
    <div class="toret-box box-info">
      <div class="box-header">
        <h3 class="box-title"><?php _e('Zadejte licenční klíč','woo-xml-feeds'); ?></h3>
      </div>
      <div class="box-body">
        <?php if(!empty($licence_info)){ ?>
     <p><strong><?php echo $licence_info; ?></strong></p>
  <?php
  delete_option('wooshop-checkout-info');  
    }
  ?>
  <?php if(!empty($lic)){ ?>
     <p><strong>Vaše licence je aktivní.</strong></p>
  <?php
    }
  ?>

  <form method="post" style="margin-bottom:10px;">
    <input type="text" name="licence" id="licence" style="width:400px;" value="<?php if(!empty($licence_key)){ echo $licence_key; } ?>" />
    <input type="hidden" name="control" value="ok" />
    <input type="submit" class="button" value="Ověřit licenci" />
  </form>
      </div>
    </div>
  </div>    
  
  <form method="post" action="">
  
  <div class="t-col-12">
    <div class="toret-box box-info">
      <div class="box-header">
        <h3 class="box-title"><?php _e('','woo-xml-feeds'); ?></h3>
      </div>
      <div class="box-body">
      <p><?php _e('Nastavení hodnot feedu pro všechny produkty. Hodnoty pro jednotlivé produkty můžete upravit v detailu produktu.','woo-xml-feeds'); ?></p>
        <table class="table-bordered">
          <tr>
            <th><?php _e('Název porovnávače','woo-xml-feeds'); ?></th>
            <th><?php _e('Url feedu','woo-xml-feeds'); ?></th>
            <th><?php _e('Odkaz na feed','woo-xml-feeds'); ?></th>
            <th><?php _e('Aktualizovat feed ručně','woo-xml-feeds'); ?></th>
          </tr>
          <tr>
            <td><?php _e('<strong>Heuréka SK</strong> XML feed','woo-xml-feeds'); ?></td>
            <td><?php echo WOOXMLURL.'xml/heureka-sk.xml'; ?></td>
            <td class="td_center"><a href="<?php echo WOOXMLURL.'xml/heureka-sk.xml'; ?>" class="btn btn-info btn-sm" target="_blank">Heureka SK</a></td>
            <td class="td_center"><a href="<?php echo admin_url().'admin.php?page=woo-xml-feeds&generate=heurekask'; ?>" class="btn btn-success btn-sm">Aktualizovat</a></td>
          </tr>
          <tr>
            <td><?php _e('<strong>Pricemania.sk</strong> feed','woo-xml-feeds'); ?></td>
            <td><?php echo WOOXMLURL.'xml/pricemania-sk.xml'; ?></td>
            <td class="td_center"><a href="<?php echo WOOXMLURL.'xml/pricemania-sk.xml'; ?>" class="btn btn-info btn-sm" target="_blank">Pricemania.sk</a></td>
            <td class="td_center"><a href="<?php echo admin_url().'admin.php?page=woo-xml-feeds&generate=pricemania-sk'; ?>" class="btn btn-success btn-sm">Aktualizovat</a></td>
          </tr>
          <tr>
            <td><?php _e('<strong>Najnakup.sk</strong> feed','woo-xml-feeds'); ?></td>
            <td><?php echo WOOXMLURL.'xml/najnakup.xml'; ?></td>
            <td class="td_center"><a href="<?php echo WOOXMLURL.'xml/najnakup.xml'; ?>" class="btn btn-info btn-sm" target="_blank">Najnakup.sk</a></td>
            <td class="td_center"><a href="<?php echo admin_url().'admin.php?page=woo-xml-feeds&generate=najnakup'; ?>" class="btn btn-success btn-sm">Aktualizovat</a></td>
          </tr>
          <tr>
            <td><?php _e('<strong>Google nákupy</strong> feed','woo-xml-feeds'); ?></td>
            <td><?php echo WOOXMLURL.'xml/google.xml'; ?></td>
            <td class="td_center"><a href="<?php echo WOOXMLURL.'xml/google.xml'; ?>" class="btn btn-info btn-sm" target="_blank">Google nákupy</a></td>
            <td class="td_center"><a href="<?php echo admin_url().'admin.php?page=woo-xml-feeds&generate=google'; ?>" class="btn btn-success btn-sm">Aktualizovat</a></td>
          </tr>
          <tr>
            <td><?php _e('<strong>123 Nákup</strong> feed','woo-xml-feeds'); ?></td>
            <td><?php echo WOOXMLURL.'xml/123nakup.xml'; ?></td>
            <td class="td_center"><a href="<?php echo WOOXMLURL.'xml/123-nakup.xml'; ?>" class="btn btn-info btn-sm" target="_blank">123 Nákup</a></td>
            <td class="td_center"><a href="<?php echo admin_url().'admin.php?page=woo-xml-feeds&generate=123nakup'; ?>" class="btn btn-success btn-sm">Aktualizovat</a></td>
          </tr>
        </table>
        
      <p><?php _e('Pro aktualizaci jednotlivých xml souborů, je nutné nastavit pravidelné provádění scriptů, které je generují.<br />V tabulce jsou url adresy souborů pro jednotlivé porovnávače, jejichž provádění je třeba nastavit pomocí cronu.<br />V případě nejasností, se obraťte na poskytovatele vašeho webhostingu.','woo-xml-feeds'); ?></p>
        
        <table class="table-bordered">
          <tr>
            <th><?php _e('Název porovnávače','woo-xml-feeds'); ?></th>
            <th><?php _e('Url cron souboru','woo-xml-feeds'); ?></th>
          </tr>
          <tr>
            <td><?php _e('<strong>Heuréka SK</strong>','woo-xml-feeds'); ?></td>
            <td><?php echo WOOXMLURL.'feed-heureka-sk.php'; ?></td>
          </tr>
          <tr>
            <td><?php _e('<strong>Pricemania.sk</strong>','woo-xml-feeds'); ?></td>
            <td><?php echo WOOXMLURL.'feed-pricemania-sk.php'; ?></td>
          </tr>
          <tr>
            <td><?php _e('<strong>Najnakup.sk</strong>','woo-xml-feeds'); ?></td>
            <td><?php echo WOOXMLURL.'feed-najnakup.php'; ?></td>
          </tr>
          <tr>
            <td><?php _e('<strong>Google nákupy</strong>','woo-xml-feeds'); ?></td>
            <td><?php echo WOOXMLURL.'feed-google-nakupy.php'; ?></td>
          </tr>
          <tr>
            <td><?php _e('<strong>Google nákupy</strong>','woo-xml-feeds'); ?></td>
            <td><?php echo WOOXMLURL.'feed-123-nakup.php'; ?></td>
          </tr>
        </table>  
        
      </div>
    </div>
  </div>
  
  
  
  <div class="t-col-12">
    <div class="toret-box box-info">
      <div class="box-header">
        <h3 class="box-title"><?php _e('Globalní nastavení','woo-xml-feeds'); ?></h3>
      </div>
      <div class="box-body">
      <p><?php _e('Nastavení hodnot feedu pro všechny produkty. Hodnoty pro jednotlivé produkty můžete upravit v detailu produktu.','woo-xml-feeds'); ?></p>
        <table class="table-bordered">
          <tr>
            <td><?php _e('Název výrobce','woo-xml-feeds'); ?><span class="help-ico fa fa-question"><span class="help-tooltip"><?php _e('Zadejte název výrobce produktů. Pro jednotlivé produkty pak můžete jméno výrobce změnit v detailu produktu.','woo-xml-feeds'); ?></span></span></td>
            <td colspan="3"><input type="text" name="manufakturer" value="<?php if(!empty($manufakturer)){ echo $manufakturer; } ?>"></td>
          </tr>
          <tr>
            <td>
              <?php _e('Dodací doba','woo-xml-feeds'); ?>
              <span class="help-ico fa fa-question">
                <span class="help-tooltip">
                  <?php _e('Zadejte dobu doručení v dnech.','woo-xml-feeds'); ?><br />
                  <?php _e('Pro zboží skladem zadejte: skladom','woo-xml-feeds'); ?><br />
                  <?php _e('Dodání do 3 dnů, zadejte:  1 - 3','woo-xml-feeds'); ?><br />
                  <?php _e('Dodání do týdne, zadejte:  4 - 7','woo-xml-feeds'); ?><br />
                  <?php _e('Dodání do 2 týdnů, zadejte:  8 - 14','woo-xml-feeds'); ?><br />
                  <?php _e('Dodání do měsíce, zadejte:  15 - 30','woo-xml-feeds'); ?><br />
                  <?php _e('Více jak měsíc, zadejte:  31 a více','woo-xml-feeds'); ?><br />
                  <?php _e('Dodání neznámé, nechte pole prázdné','woo-xml-feeds'); ?><br />
                </span>
              </span>
            </td>
            <td colspan="3">
             <input type="text" name="delivery_date" value="<?php echo $delivery_date; ?>">
            </td>
          </tr>
          <tr>
            <td><?php _e('Typ zboží','woo-xml-feeds'); ?><span class="help-ico fa fa-question"><span class="help-tooltip"><?php _e('K rozlišení nových a bazarových položek.','woo-xml-feeds'); ?></span></span></td>
            <td colspan="3">
              <label>Nové zboží&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="heureka_item_type" value="nove" <?php if(!empty($heureka_item_type) && $heureka_item_type == 'nove'){ echo 'checked="checked"'; } ?>></label>
              <label>Bazarové zboží&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="heureka_item_type" value="bazar" <?php if(!empty($heureka_item_type) && $heureka_item_type == 'bazar'){ echo 'checked="checked"'; } ?>></label>
              
            </td>
          </tr>
          <tr>
            <td><?php _e('Skrýt bazarové položky','woo-xml-feeds'); ?><span class="help-ico fa fa-question"><span class="help-tooltip"><?php _e('Skrýt bazarové položky ve feedu Heuréky.','woo-xml-feeds'); ?></span></span></td>
            <td colspan="3">
              <label>Ano&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="heureka_hide_bazar" value="ano" <?php if(!empty($heureka_hide_bazar) && $heureka_hide_bazar == 'ano'){ echo 'checked="checked"'; } ?>></label>
              <label>Ne&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="heureka_hide_bazar" value="ne" <?php if(!empty($heureka_hide_bazar) && $heureka_hide_bazar == 'ne'){ echo 'checked="checked"'; } ?>></label>
              
            </td>
          </tr>
          <tr>
            <td><?php _e('Jako popis použít popis produktu','woo-xml-feeds'); ?><span class="help-ico fa fa-question"><span class="help-tooltip"><?php _e('Pro tag description','woo-xml-feeds'); ?></span></span></td>
            <td colspan="3">
              <label>Zkrácený popis&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="use_excerpt" value="excerpt" <?php if(!empty($use_excerpt) && $use_excerpt == 'excerpt'){ echo 'checked="checked"'; } ?>></label>
              <label>Celý popis&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="use_excerpt" value="content" <?php if(!empty($use_excerpt) && $use_excerpt == 'content'){ echo 'checked="checked"'; } ?>></label>
              
            </td>
          </tr>
          <tr>
            <th colspan="4"><?php _e('Heuréka dostupnostní feed','woo-xml-feeds'); ?></th>
          </tr>
          <tr>
            <td><?php _e('Čas expedice balíků','woo-xml-feeds'); ?><span class="help-ico fa fa-question"><span class="help-tooltip"><?php _e('Určuje čas, do kterého je nutné provést objednávku, aby byla doručena dle uvedené doby dodání. Zadávejte ve tvaru 12:00','woo-xml-feeds'); ?></span></span></td>
            <td colspan="3">
              <input type="text" name="deadline_time" placeholder="Příklad - 12:00" value="<?php if(!empty($deadline_time)){ echo $deadline_time; } ?>">              
            </td>
          </tr>
          <tr>
            <td><?php _e('Doba od objednání, do doručení. Uvádějte celé číslo.','woo-xml-feeds'); ?><span class="help-ico fa fa-question"><span class="help-tooltip"><?php _e('Aby bylo možné považovat produkt za "skladový", nesmí doba mezi objednáním (orderDeadline) a doručením přesáhnout 7 dnů. Produkty, které nelze dodat do 7 dnů od objednání, do dostupnostního XML souboru neuvádějte.','woo-xml-feeds'); ?></span></span></td>
            <td colspan="3">
              <input type="text" name="delivery_time" placeholder="" value="<?php if(!empty($delivery_time)){ echo $delivery_time; } ?>">              
            </td>
          </tr>
          <tr>
            <th colspan="4"><?php _e('Url dostupnostního feedu a php souboru pro jeho vytvoření','woo-xml-feeds'); ?></th>
          </tr>
          <tr>
            <td><?php _e('Feed','woo-xml-feeds'); ?><span class="help-ico fa fa-question"><span class="help-tooltip"><?php _e('Url xml souboru, který bude načítat Heuréka.','woo-xml-feeds'); ?></span></span></td>
            <td colspan="3">
             <?php echo WOOXMLURL.'xml/dostupnostni-feed.xml'; ?>              
            </td>
          </tr>
          <tr>
            <td><?php _e('Soubor pro generování feedu.','woo-xml-feeds'); ?><span class="help-ico fa fa-question"><span class="help-tooltip"><?php _e('Url souboru pro nastavení cronu. Heuréka načítá feed každých deset minut, doporučujeme nastavit aktualizaci také po deseti minutách.','woo-xml-feeds'); ?></span></span></td>
            <td colspan="3">
              <?php echo WOOXMLURL.'dostupnostni-feed.php'; ?>               
            </td>
          </tr>
        </table>
        <p><?php _e('Dostupnostní feed uvádí, do jaké doby je nutné učinit objednávku, aby byla doručena do určitého termínu. Pokud uvedete Čas expedice balíků - 14:00 a dobu od objednání - 2, bude ve feedu nastavena doba objednání do 14:00 aktuálního dne a doba doručení +2 dny.','woo-xml-feeds'); ?></p>
        <p><?php _e('Příklad: Čas expedice balíků - 22.2.2019 14:00, Maximální doba doručení - 24.2.2019 14:00','woo-xml-feeds'); ?></p>
        <p><?php _e('Nastavení můžete upravit u každého produktu zvlášť.','woo-xml-feeds'); ?></p>
        
        <p>&nbsp;</p>
        <p><?php _e('Použít našeptávací formulář pro přiřazení kategorií?','woo-xml-feeds'); ?></p>
        <p><?php _e('Jedná se o JS knihovnu, která umožňuje jednodušší vyhledávání ve stromu kategorií od Heuréky. Při větším množství katgorií její použití značně zpomaluje proces výběru, proto může být vypnuta','woo-xml-feeds'); ?></p>
        <table class="table-bordered">  
          <tr>
            <td><?php _e('Našeptávací formulář','woo-xml-feeds'); ?><span class="help-ico fa fa-question"></span></td>
            <td colspan="3">
              <label>Použít našeptávač&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="use_select2" value="yes" <?php if(!empty($use_select2) && $use_select2 == 'yes'){ echo 'checked="checked"'; } ?>></label>
              <label>Vypnout našeptávač&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="use_select2" value="no" <?php if(!empty($use_select2) && $use_select2 == 'no'){ echo 'checked="checked"'; } ?>></label>
              
            </td>
          </tr>
        </table>
        
      </div>
    </div>
  </div>    
  
 


   <input type="hidden" name="update" value="ok" />
   <input type="submit" class="btn btn-info" value="<?php _e('Uložit nastavení','woo-xml-feeds'); ?>" />
   
</form>


</div>
