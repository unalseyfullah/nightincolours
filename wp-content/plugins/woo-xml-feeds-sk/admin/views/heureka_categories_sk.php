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
 

  /**
  *
  * Load Xml file
  *
  */ 
 //CZ
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
  
  $heureka_categories = get_option( 'woo_heureka_categories_sk');

?>




<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

<!-- Heureka kategorie -->
  <div class="t-col-12">
    <div class="toret-box box-info">
      <div class="box-header">
        <h3 class="box-title"><?php _e('Kategorie Heuréky','woo-xml-feeds'); ?></h3>
      </div>
      <div class="box-body">
        <table class="table-bordered">
        <?php
              foreach($heureka_categories as $key => $item){ 
              if(!empty($item['category_fullname'])){
              ?> 
            <tr><td><?php echo $item['category_fullname']; ?></td></tr>  
            <?php  }
            }
            ?>
        </table>    
      </div>
    </div>
  </div> 


  
</div>
