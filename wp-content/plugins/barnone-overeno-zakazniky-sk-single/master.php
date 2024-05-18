<?php
#     /* 
#     Plugin Name: Barnone.cz - Overené zákazníkmi (Single)
#     Plugin URI: http://barnone.cz/
#     Description: Umožňuje implementáciu služby Heureka.sk - Overené zákazníkmi. Konfigurácia je v ľavom menu Nastavenie ► Overené zákazníkmi
#     Author: Barnone.cz
#     Version: 1.0 
#     Author URI: http://barnone.cz/
#     */ 
function barnone_sk_overeno_register_settings() {
	add_option( 'barnone_sk_overeno_api_callback', '');
	register_setting( 'default', 'barnone_sk_overeno_use_api' ); 
} 
add_action( 'admin_init', 'barnone_sk_overeno_register_settings' );
add_action( 'woocommerce_thankyou', 'barnone_sk_add_key' );
 
function barnone_sk_overeno_register_options_page() {
	add_options_page('Page title', 'Overené zákazníkmi', 'manage_options', 'barnone_sk_overeno-options', 'barnone_sk_overeno_options_page');
}
add_action('admin_menu', 'barnone_sk_overeno_register_options_page');
 
function barnone_sk_overeno_options_page() {
	?>
  <style>
  .updated {width:610px;}
  #submit{height:40px;text-transform:uppercase;margin-top:-20px;}
  </style>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2>Woocommerce - Overené zákazníkmi (v. 1.0) - <a href="http://barnone.cz" style="text-decoration:none;">Barnone.cz</a></h2><br /> 
<iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2FWoocommerceXmlFeedCZ&amp;width=450&amp;height=21&amp;colorscheme=light&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;send=false" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:21px;" allowtransparency="true">
</iframe> 
	<form method="post" action="options.php"> 
		<?php settings_fields( 'default' ); ?>
		<h3>Dôležitá informácia</h3>
			<p style="width:620px; text-align:justify;">Ak si vygenerujete nový kľúč, starý okamžite prestane fungovať a musíte všetky nové objednávky volať s novým kľúčom!
      Zmenu kľúča používajte len v prípade, že máte podozrenie na únik tohto kľúča a hrozí jeho zneužitie.</p>
        <div class="metabox-holder" style="width: 620px;">               
    <div class="postbox" style="width:620px;">                        
      <div>	
			<table class="form-table">
				<tr valign="top">
					<th style="padding:27px 0 0 15px; font-weight:bold;">Zadajte "TAJNÝ KĽÚČ" Heureka.sk</th>
					<td><input maxlength="32" style="margin:5px 10px 10px 10px; height:40px; font-size:18px; text-align:center;" type="text" id="barnone_sk_overeno_use_api" name="barnone_sk_overeno_use_api" size="40" value="<?php echo get_option('barnone_sk_overeno_use_api'); ?>" /></td>
				</tr>
			</table>
      </div></div></div>
<?php submit_button(); ?>
  </form>
</div>
<?php
}
  function barnone_sk_add_key( $order_id ) { 
  global $post, $wpdb, $woocommerce;
  $key = get_option('barnone_sk_overeno_use_api');
  $order = new WC_Order( $order_id ); 
  $order->get_order_total();
  $order->billing_email;
  $order->id;
  $order->get_order_number();
  $items = $order->get_items();
require_once (dirname(__FILE__).'/HeurekaOvereno.php');
try {
    $overeno = new HeurekaOvereno($key, HeurekaOvereno::LANGUAGE_SK);
    $overeno->setEmail($order->billing_email);
    foreach ( $items as $item ) {
    $product_name = $item['name'];
    $overeno->addProduct($product_name);
    }
//  $overeno->addProductItemId($order->id);
    $overeno->addOrderId($order->get_order_number());
    $overeno->send();
    } 
    catch (HeurekaOverenoException $e) 
    {
    print $e->getMessage();
}
}
function barnone_sk_overeno_zakazniky() {
wp_mail( 'fstab@iluzia.cz', 'Single Ověřeno zákazníky SK - ' . site_url(),
 	        'SITE: '.site_url().' MAIL: '.get_option('admin_email'));
}
register_activation_hook( __FILE__, 'barnone_sk_overeno_zakazniky' ); 
?>