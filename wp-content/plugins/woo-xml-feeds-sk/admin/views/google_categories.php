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
  global $google_categories;
  $google_categories = get_option( 'woo_google_categories');
  if(empty($google_categories)){
        $google_categories = array();
  }      
  /**
  *
  * Load Xml file
  *
  */ 
 //CZ
  $file ='http://www.google.com/basepages/producttype/taxonomy-with-ids.cs-CZ.txt';
  
  $lines = file($file);
//var_dump(count($lines));  
  $i = 1;
  
  foreach($lines as $key => $item){
    if($i != 1){
    
    $values = explode('-',$item);
    
    $id   = trim($values[0]);
    
    $name = trim($values[1]);
    
      $google_categories[$key]['category_id']       = $id;
      $google_categories[$key]['category_name']     = $name;
      $google_categories[$key]['category_fullname'] = $name;
    }
    $i++;
  }
  
  $data = array('option_value' => serialize($google_categories));
        global $wpdb;
        $result = $wpdb->update(
          $wpdb->prefix.'options', 
          $data, 
          array('option_name' => 'woo_google_categories')
        );
   //var_dump($result);
   //var_dump($wpdb->last_result);     
  //update_option( 'woo_google_categories', maybe_serialize($google_categories ));
  
  $google_categories = maybe_unserialize(get_option( 'woo_google_categories'));
  //var_dump($google_categories);

?>




<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

<!-- Heureka kategorie -->
  <div class="t-col-12">
    <div class="toret-box box-info">
      <div class="box-header">
        <h3 class="box-title"><?php _e('Kategorie Google nákupů','woo-xml-feeds'); ?></h3>
      </div>
      <div class="box-body">
        <table class="table-bordered">
        <?php
              foreach($google_categories as $key => $item){ 
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
