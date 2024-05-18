<?php 


add_action( 'admin_init', 'check_wooshop_xml_feeds_licence' );
add_action( 'init', 'check_wooshop_xml_feeds_licence' );

/**
 *
 * Check if licence is active
 *
 */   
function check_wooshop_xml_feeds_licence(){
  $licence_status = get_option('wooshop-xml-feeds-licence');
  if(!empty($licence_status)){
    if($licence_status=='active'){
      global $lic;
      $lic = 'active';  
    }
  }
}

/**
 *
 * Control licence
 *
 */   
function wooshop_xml_feeds_control_licence($licence){

  $ip = $_SERVER['REMOTE_ADDR'];

    $api_params = array(
				'licence' => $licence,
				'ip' 	    => $ip,
				'url'     => home_url()
			);

			// Call the custom API.
			$response = wp_remote_post( 'http://licence.toret.cz/wp-content/plugins/plc/control.php', array( 'timeout' => 35, 'sslverify' => false, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) ){
				return false;
      }
        
        if($response['body']=='ok'){
        update_option('wooshop-xml-feeds-licence','active');
        update_option('wooshop-xml-feeds-info','Vaše licence byla aktivována');
        update_option('wooshop-xml-feeds-licence-key',$licence);
      }elseif($response['body']=='fail'){
        update_option('wooshop-xml-feeds-info','Chyba ověřování, kontaktujte prosím podporu');
      }elseif($response['body']=='double'){
        update_option('wooshop-xml-feeds-info','Licenční klíč byl již použit');
      }
        
}
