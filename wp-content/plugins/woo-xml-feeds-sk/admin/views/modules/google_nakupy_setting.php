<?php
/**
 *
 * @package   woo_xml_feeds
 * @author    Vladislav Musilek
 * @license   GPL-2.0+
 * @link      http://toret.cz
 * @copyright 2014 Vladislav Musilek
 *
 * Version 1.0.0
 *  
 */
 
$google_jmeno_eshopu = get_option( 'google-jmeno-eshopu' );
if(empty($google_jmeno_eshopu)){ $google_jmeno_eshopu = ''; }

$google_link_eshopu = get_option( 'google-link-eshopu' );
if(empty($google_link_eshopu)){ $google_link_eshopu = ''; }

$google_popis_eshopu = get_option( 'google-popis-eshopu' );
if(empty($google_popis_eshopu)){ $google_popis_eshopu = ''; }

$google_varianty = get_option( 'google-varianty' );
if(empty($google_varianty)){ $google_varianty = ''; }

?>
<!-- Heureka CPC -->
  <div class="t-col-12">
    <div class="toret-box box-info">
      <div class="box-header">
        <h3 class="box-title"><?php _e('Google nákupy - informace o eshopu','woo-xml-feeds'); ?></h3>
      </div>
      <div class="box-body">
        <table class="table-bordered">
          <tr>
            <td><?php _e('Jméno eshopu','woo-xml-feeds'); ?><span class="help-ico fa fa-question"><span class="help-tooltip"><?php _e('Zobrazuje se na začátku feedu.','woo-xml-feeds'); ?></span></span></td>
            <td colspan="3"><input type="text" name="google-jmeno-eshopu" value="<?php echo $google_jmeno_eshopu; ?>"></td>
          </tr>
          <tr>
            <td><?php _e('Odkaz na eshop','woo-xml-feeds'); ?><span class="help-ico fa fa-question"><span class="help-tooltip"><?php _e('Zobrazuje se na začátku feedu.','woo-xml-feeds'); ?></span></span></td>
            <td colspan="3"><input type="text" name="google-link-eshopu" value="<?php echo $google_link_eshopu; ?>"></td>
          </tr>
          <tr>
            <td><?php _e('Popis eshopu','woo-xml-feeds'); ?><span class="help-ico fa fa-question"><span class="help-tooltip"><?php _e('Zobrazuje se na začátku feedu.','woo-xml-feeds'); ?></span></span></td>
            <td colspan="3"><textarea name="google-popis-eshopu"><?php echo $google_popis_eshopu; ?></textarea></td>
          </tr>
          <tr>
            <td><?php _e('Zobrazit feed bez variant produktu','woo-xml-feeds'); ?><span class="help-ico fa fa-question"><span class="help-tooltip"><?php _e('Pokud je zaškrtnuto, nebudou se ve feedu zobrazovat varianty produktu. Defaultní nastavení je s variantami.','woo-xml-feeds'); ?></span></span></td>
            <td colspan="3"><input type="checkbox" name="google-varianty" value="ok" <?php if(!empty($google_varianty) && $google_varianty == 'ok'){ echo 'checked="checked"'; } ?>"></td>
          </tr>
        </table>
      </div>
    </div>
  </div> 