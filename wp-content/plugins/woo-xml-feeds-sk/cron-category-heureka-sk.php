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
 
$heureka_categories = array();

$dir =  dirname(dirname(dirname(dirname(__FILE__))));
require_once($dir.'/wp-load.php');   
require_once($dir.'/wp-includes/option.php');

/*
$licence_status = get_option('wooshop-xml-feeds-licence');
  if ( empty( $licence_status ) ) {
    return false;
  } 
*/
function heureka_xml_loop($data,$category_id){
  global $heureka_categories;
  
  if(!empty($data)){
     foreach($data as $item){
  
  $item_id       = (string)$item->CATEGORY_ID;
  $item_name     = (string)$item->CATEGORY_NAME;
  $item_fullname = (string)$item->CATEGORY_FULLNAME;
  if(!empty($item->CATEGORY_FULLNAME)){
  $heureka_categories[$item_id]['category_id'] = $item_id;
  $heureka_categories[$item_id]['category_name'] = $item_name;  
  $heureka_categories[$item_id]['category_fullname'] = $item_fullname;   
  }
     heureka_xml_loop($item->CATEGORY,$category_id);
     }
  }
}

/**
 *
 * Load Xml file
 *
 */ 
//SK   
$xml ='http://www.heureka.sk/direct/xml-export/shops/heureka-sekce.xml';
//CZ
//$xml ='http://www.heureka.cz/direct/xml-export/shops/heureka-sekce.xml';
$feed = simplexml_load_file($xml);

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

foreach($feed->CATEGORY as $first){
  
  $first_id   = (string)$first->CATEGORY_ID;
  $first_name = (string)$first->CATEGORY_NAME;
  
  $heureka_categories[$first_id]['category_id'] = $first_id;
  $heureka_categories[$first_id]['category_name'] = $first_name;
  $heureka_categories[$first_id]['category_fullname'] = '';
  heureka_xml_loop($first->CATEGORY,$first_id);
 
}

if ( get_option( 'woo_heureka_categories' ) !== false ) {
    update_option( 'woo_heureka_categories', $heureka_categories );
} else {
    add_option( 'woo_heureka_categories', $heureka_categories );
} 

?>