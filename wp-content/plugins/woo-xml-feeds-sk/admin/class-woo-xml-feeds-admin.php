<?php
/**
 * Woocommerce XML Feeds 
 *
 * @package   Woocommerce XML Feeds  
 * @author    Vladislav Musilek
 * @license   GPL-2.0+
 * @link      http://musilda.cz
 * @copyright 2014 Vladislav Musilek
 */

/**
 * @package Woo_Xml_Feeds_Admin
 * @author  Vladislav Musilek
 */
class Woo_Xml_Feeds_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * Call $plugin_slug from public plugin class.
		 */
		$plugin = Woo_Xml_Feeds::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

/*
    $licence_status = get_option('wooshop-xml-feeds-licence');
    if ( empty( $licence_status ) ) {
	     return false;
    }
*/

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

    $this->includes();

		/**
     *  Output fix
     */              
    add_action('admin_init', array( $this, 'heureka_xml_output_buffer' ) );
    
		/**
		 *
		 * Product fields
		 *     
		 */              
    //add_action('woocommerce_product_write_panel_tabs', array( $this,'info_tab_options_tab_spec' ) );
    //add_action('woocommerce_product_write_panels', array( $this, 'woo_add_custom_general_fields' ) );
    //add_action('woocommerce_process_product_meta', array( $this, 'woo_add_custom_general_fields_save' ) );
    
    
    /**
     *
     * Variable fields
     *
     */                   
    //Display Fields
    //add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'variable_fields'), 10, 3 );
    //JS to add fields for new variations
    //add_action( 'woocommerce_product_after_variable_attributes_js', array( $this, 'variable_fields_js' ) );
    //Save variation fields
    //add_action( 'woocommerce_process_product_meta_variable', array( $this, 'save_variable_fields' ) , 10000, 1 );
 

	}
  
  
  /**
	 * Include required core files used in admin.
	 * 
	 * @since     1.0.0      
	 */
	private function includes() {
  
    include_once( 'includes/class-manager.php' );
  
  }
  
  /**
	 * Get stock class
	 * @return WCM_Stock
	 * 
	 * @since     1.0.0      
	 */
	public function get_manager() {
		return XML_Manager::get_instance();
    
    
	}
  

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Woo_Xml_Feeds::VERSION );
      $use_select2 = get_option( 'woo_xml_feed_use_select2' );
      if(!empty($use_select2) && $use_select2 == 'no'){}else{ 
      wp_enqueue_style( 'select2', plugins_url( 'assets/css/select2.css', __FILE__ ), array(), Woo_Xml_Feeds::VERSION );
      }
      wp_enqueue_style( 'icheck', plugins_url( 'assets/css/iCheck/flat/green.css', __FILE__ ), array(), Woo_Xml_Feeds::VERSION );
      wp_enqueue_style( 'icheckred', plugins_url( 'assets/css/iCheck/flat/red.css', __FILE__ ), array(), Woo_Xml_Feeds::VERSION );
      wp_enqueue_style( 'font-awesome', plugins_url( 'assets/fonts/font-awesome/css/font-awesome.css', __FILE__ ), array(), Woo_Xml_Feeds::VERSION );
		
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Woo_Xml_Feeds::VERSION );
      wp_enqueue_script( 'select2', plugins_url( 'assets/js/select2.js', __FILE__ ), array( 'jquery' ), Woo_Xml_Feeds::VERSION );
      wp_enqueue_script( 'icheck', plugins_url( 'assets/js/iCheck/icheck.min.js', __FILE__ ), array( 'jquery' ), Woo_Xml_Feeds::VERSION );

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 */
		add_menu_page(
			__( 'Xml Feed', $this->plugin_slug ),
			__( 'Xml Feed pro Woocommerce', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'plugin_setting' )
		);
    add_submenu_page( 
      $this->plugin_slug, 
      __( 'Heuréka SK', $this->plugin_slug ), 
      'Heuréka SK', 
      'manage_options', 
      'heureka-sk', 
      array( $this, 'heureka_sk' )
    );
    add_submenu_page( 
      $this->plugin_slug, 
      __( 'Pricemania.sk', $this->plugin_slug ), 
      'Pricemania.sk', 
      'manage_options', 
      'pricemania', 
      array( $this, 'pricemania' )
    );
    add_submenu_page( 
      $this->plugin_slug, 
      __( 'Google nákupy', $this->plugin_slug ), 
      'Google nákupy', 
      'manage_woocommerce', 
      'google-nakupy', 
      array( $this, 'g_merchant' )
    );
    add_submenu_page( 
      $this->plugin_slug, 
      __( 'Najnakup.sk', $this->plugin_slug ), 
      'Najnakup.sk', 
      'manage_options', 
      'najnakup', 
      array( $this, 'najnakup' )
    );
    add_submenu_page( 
      $this->plugin_slug, 
      __( '123 Nákup', $this->plugin_slug ), 
      __( '123 Nákup', $this->plugin_slug ), 
      'manage_options', 
      'page_123_nakup', 
      array( $this, 'page_123_nakup' )
    );
    add_submenu_page( 
      $this->plugin_slug, 
      __( 'Manager', $this->plugin_slug ), 
      'Manager', 
      'manage_options', 
      'manager', 
      array( $this, 'manager' )
    );
    add_submenu_page( 
      $this->plugin_slug, 
      __( 'Kategorie Heuréka.sk', $this->plugin_slug ), 
      'Kategorie Heuréka.sk', 
      'manage_options', 
      'kategorie-heureky-sk', 
      array( $this, 'kategorie_heureky_sk' )
    );
    add_submenu_page( 
      $this->plugin_slug, 
      __( 'Kategorie Google nákupy', $this->plugin_slug ), 
      'Kategorie Google nákupy', 
      'manage_woocommerce', 
      'kategorie-gogle-nakupy', 
      array( $this, 'kategorie_google_nakupy' )
    );
          
    
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}
  /**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function plugin_setting() {
		include_once( 'views/setting.php' );
	}  
  /**
	 * Render the settings page for Heureka CZ.
	 *
	 * @since    1.0.0
	 */
	public function heureka_cz() {
		include_once( 'views/heureka_cz.php' );
	}
  /**
	 * Render the settings page for Heureka SK.
	 *
	 * @since    1.0.0
	 */
	public function heureka_sk() {
		include_once( 'views/heureka_sk.php' );
	}
  /**
	 * Render the settings page for Zboží.
	 *
	 * @since    1.0.0
	 */
	public function zbozi() {
		include_once( 'views/zbozi.php' );
	}
  /**
	 * Render the settings page for Zboží.
	 *
	 * @since    1.0.0
	 */
	public function srovname() {
		include_once( 'views/srovname.php' );
	}
  /**
	 * Render the settings page for Pricemania.
	 *
	 * @since    1.0.0
	 */
	public function pricemania() {
		include_once( 'views/pricemania.php' );
	}
  /**
	 * Render the settings page for Najnakup.
	 *
	 * @since    1.0.0
	 */
	public function najnakup() {
		include_once( 'views/najnakup.php' );
	}
  /**
	 * Render the settings page for Google.
	 *
	 * @since    1.0.0
	 */
	public function g_merchant() {
		include_once( 'views/g_merchant.php' );
	}
  /**
	 * Render the settings page for Heureka SK.
	 *
	 * @since    1.0.0
	 */
	public function page_123_nakup() {
		include_once( 'views/123_nakup.php' );
	}
  /**
	 * Render the settings page for shop catergories and Heureka categories.
	 *
	 * @since    1.0.0
	 */
	public function feed_check() {
		include_once( 'views/feed_check.php' );
	}
  
  /**
	 * Render the settings page for Heureka Kategorie sk.
	 *
	 * @since    1.0.0
	 */
	public function kategorie_heureky_sk() {
		include_once( 'views/heureka_categories_sk.php' );
	}
  /**
	 * Render the settings page manager
	 *
	 * @since    1.0.0
	 */
	public function manager() {
		include_once( 'views/manager.php' );
	}
  /**
	 * Render the settings page for Google Nákupy kategorie.
	 *
	 * @since    1.0.0
	 */
	public function kategorie_google_nakupy() {
		include_once( 'views/google_categories.php' );
	}
  
  
	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * Headers allready sent fix
	 * 
	 * @since    1.0.0        
	 */
	public function heureka_xml_output_buffer() {
		ob_start();
	}

	/**
 * Custom Tabs for product 
 */
public function info_tab_options_tab_spec() {
?>
<li class="info_tabxml"><a href="#info_tab_dataxml"><?php _e('Specifikace pro XML feed', $this->plugin_slug ); ?></a></li>
<?php
}

  /**
	 * 
	 * 
	 * @since    1.0.0        
	 */
	public function woo_add_custom_general_fields() {
    ?><div id="info_tab_dataxml" class="panel woocommerce_options_panel"><?php 
    include('includes/custom-general-data.php');	
    ?></div><?php
  }
  
  public function woo_add_custom_general_fields_save( $post_id ){
	// Title field
	if(!empty($_POST['custom_product_title'])){$woocommerce_custom_product_title_field = $_POST['custom_product_title'];}
	if( !empty( $woocommerce_custom_product_title_field ) ){
		update_post_meta( $post_id, 'custom_product_title', esc_attr( $woocommerce_custom_product_title_field ) );
  }else{
    delete_post_meta( $post_id, 'custom_product_title' );
  }
  
  // Product name field
	if(!empty($_POST['custom_product_name'])){$woocommerce_custom_product_title_field = $_POST['custom_product_name'];}
	if( !empty( $woocommerce_custom_product_title_field ) ){
		update_post_meta( $post_id, 'custom_product_name', esc_attr( $woocommerce_custom_product_title_field ) );
  }else{
    delete_post_meta( $post_id, 'custom_product_name' );
  }
   
	// EAN field
  if(!empty($_POST['_ean'])){$woocommerce_ean_field = $_POST['_ean'];}
	if( !empty( $woocommerce_ean_field ) ){
		update_post_meta( $post_id, '_ean', esc_attr( $woocommerce_ean_field ) );
  }else{
    delete_post_meta( $post_id, '_ean' );
  }  
  // ISBN field
  if(!empty($_POST['_isbn'])){$woocommerce_isbn_field = $_POST['_isbn'];}
	if( !empty( $woocommerce_isbn_field ) ){
		update_post_meta( $post_id, '_isbn', esc_attr( $woocommerce_isbn_field ) );  
	}else{
    delete_post_meta( $post_id, '_isbn' );
  }
  // Manufacturer field
  if(!empty($_POST['manufacturer'])){$woocommerce_manufacturer_field = $_POST['manufacturer'];}
	if( !empty( $woocommerce_manufacturer_field ) ){
		update_post_meta( $post_id, 'manufacturer', esc_attr( $woocommerce_manufacturer_field ) );
  }else{
    delete_post_meta( $post_id, 'manufacturer' );
  }
  // CPC field
  if(!empty($_POST['heureka_cpc'])){$woocommerce_heureka_cpc_field = $_POST['heureka_cpc'];}
	
	if( !empty( $woocommerce_heureka_cpc_field ) ){
		update_post_meta( $post_id, 'heureka_cpc', esc_attr( $woocommerce_heureka_cpc_field ) );
  }else{
    delete_post_meta( $post_id, 'heureka_cpc' );
  }
  //Heureka sk cpc
  if(!empty($_POST['heureka_cpc_sk'])){$woocommerce_heureka_cpc_sk_field = $_POST['heureka_cpc_sk'];}
  if( !empty( $woocommerce_heureka_cpc_sk_field ) ){
		update_post_meta( $post_id, 'heureka_cpc_sk', esc_attr( $woocommerce_heureka_cpc_sk_field ) );
  }else{
    delete_post_meta( $post_id, 'heureka_cpc_sk' );
  }  
  //Zbozi cpc
  if(!empty($_POST['zbozi_cpc'])){$woocommerce_zbozi_cpc_field = $_POST['zbozi_cpc'];}
  if( !empty( $woocommerce_zbozi_cpc_field ) ){
		update_post_meta( $post_id, 'zbozi_cpc', esc_attr( $woocommerce_zbozi_cpc_field ) );
  }else{
    delete_post_meta( $post_id, 'zbozi_cpc' );
  }  
    
  // Alternative images
  if(!empty($_POST['imgurl_alternative'])){$alternative_images = $_POST['imgurl_alternative'];}
	$a_img = array(); 
  if( !empty( $alternative_images ) ){
    foreach($_POST['imgurl_alternative'] as $item){
      $a_img[] = $item;
    }
    $a = serialize($a_img);
    update_post_meta( $post_id, 'imgurl_alternative', $_POST['imgurl_alternative'] ) ;
  }else{
    delete_post_meta( $post_id, 'imgurl_alternative' );
  }  
	
  // Video url
  if(!empty($_POST['video_url'])){$video_url = $_POST['video_url'];}
	if( !empty( $video_url ) ){
		update_post_meta( $post_id, 'video_url', $video_url );
  }else{
    delete_post_meta( $post_id, 'video_url' );
  } 
    
   
  // Category field
	if(!empty($_POST['heureka_category'])){$woocommerce_custom_product_category_field = $_POST['heureka_category'];}
  if( !empty( $woocommerce_custom_product_category_field ) ){
		update_post_meta( $post_id, 'heureka_category', esc_attr( $woocommerce_custom_product_category_field ) );
  }else{
    delete_post_meta( $post_id, 'heureka_category' );
  }
  // Category field
	if(!empty($_POST['heureka_category_sk'])){$woocommerce_custom_product_category_field = $_POST['heureka_category_sk'];}
  if( !empty( $woocommerce_custom_product_category_field ) ){
		update_post_meta( $post_id, 'heureka_category_sk', esc_attr( $woocommerce_custom_product_category_field ) );
  }else{
    delete_post_meta( $post_id, 'heureka_category_sk' );
  }  
  // Category field
  if(!empty($_POST['zbozi_category'])){$woocommerce_custom_product_category_field = $_POST['zbozi_category'];}
	if( !empty( $woocommerce_custom_product_category_field ) ){
		update_post_meta( $post_id, 'zbozi_category', esc_attr( $woocommerce_custom_product_category_field ) );
  }else{
    delete_post_meta( $post_id, 'zbozi_category' );
  }
  // Category field
  if(!empty($_POST['pricemania_category'])){$woocommerce_custom_product_category_field = $_POST['pricemania_category'];}
	if( !empty( $woocommerce_custom_product_category_field ) ){
		update_post_meta( $post_id, 'pricemania_category', esc_attr( $woocommerce_custom_product_category_field ) );
  }else{
    delete_post_meta( $post_id, 'pricemania_category' );
  }
  // Category field
  if(!empty($_POST['najnakup_category'])){$woocommerce_custom_product_category_field = $_POST['najnakup_category'];}
	if( !empty( $woocommerce_custom_product_category_field ) ){
		update_post_meta( $post_id, 'najnakup_category', esc_attr( $woocommerce_custom_product_category_field ) );
  }else{
    delete_post_meta( $post_id, 'najnakup_category' );
  }
  // Heureka item type
  if(!empty($_POST['heureka_item_type'])){$heureka_item_type = $_POST['heureka_item_type'];}
	if( !empty( $heureka_item_type ) ){
		update_post_meta( $post_id, 'heureka_item_type', esc_attr( $heureka_item_type ) ); 
  }else{
    delete_post_meta( $post_id, 'heureka_item_type' );
  }  
    
  // Delivery date
  if(!empty($_POST['delivery_date'])){$delivery_date = $_POST['delivery_date'];}
	//if( !empty( $delivery_date ) && $delivery_date == 0 )
		update_post_meta( $post_id, 'delivery_date', esc_attr( $delivery_date ) ); 
  // Accessory
  if(!empty($_POST['accessory'])){$accessory = $_POST['accessory'];}
	if( !empty( $accessory ) ){
		update_post_meta( $post_id, 'accessory', esc_attr( $accessory ) );
  }else{
    delete_post_meta( $post_id, 'accessory' );
  }   
  // Dues
  if(!empty($_POST['dues'])){$dues = $_POST['dues'];}
	if( !empty( $dues ) ){
		update_post_meta( $post_id, 'dues', esc_attr( $dues ) );        
  }else{
    delete_post_meta( $post_id, 'dues' );
  } 
  
  // zbozi_extra_message
	if(!empty($_POST['zbozi_extra_message'])){$zbozi_extra_message = $_POST['zbozi_extra_message'];}
	if( !empty( $zbozi_extra_message ) ){
		update_post_meta( $post_id, 'zbozi_extra_message', esc_attr( $zbozi_extra_message ) );        
  }else{
    delete_post_meta( $post_id, 'zbozi_extra_message' );
  } 
  
  // Dues
  if(!empty($_POST['zbozi_unfeatured'])){$zbozi_unfeatured = $_POST['zbozi_unfeatured'];}
	if( !empty( $zbozi_unfeatured ) ){
		update_post_meta( $post_id, 'zbozi_unfeatured', esc_attr( $zbozi_unfeatured ) );        
  }else{
    delete_post_meta( $post_id, 'zbozi_unfeatured' );
  } 
  
  // pricemania_shipping
  if(!empty($_POST['pricemania_shipping'])){$pricemania_shipping = $_POST['pricemania_shipping'];}
	if( !empty( $pricemania_shipping ) ){
		update_post_meta( $post_id, 'pricemania_shipping', esc_attr( $pricemania_shipping ) );        
  }else{
    delete_post_meta( $post_id, 'pricemania_shipping' );
  } 
  
  // Dues
  if(!empty($_POST['najnakup_shipping'])){$najnakup_shipping = $_POST['najnakup_shipping'];}
	if( !empty( $najnakup_shipping ) ){
		update_post_meta( $post_id, 'najnakup_shipping', esc_attr( $najnakup_shipping ) );        
  }else{
    delete_post_meta( $post_id, 'najnakup_shipping' );
  } 
  
  // Dues
  if(!empty($_POST['najnakup_availability'])){$najnakup_availability = $_POST['najnakup_availability'];}
	if( !empty( $najnakup_availability ) ){
		update_post_meta( $post_id, 'najnakup_availability', esc_attr( $najnakup_availability ) );        
  }else{
    delete_post_meta( $post_id, 'najnakup_availability' );
  } 
  
  
  //Dostupnostní feed
  // product_deadline_time
  if(!empty($_POST['product_deadline_time'])){$product_deadline_time = $_POST['product_deadline_time'];}
	if( !empty( $product_deadline_time ) ){
		update_post_meta( $post_id, 'product_deadline_time', esc_attr( $product_deadline_time ) );        
  }else{
    delete_post_meta( $post_id, 'product_deadline_time' );
  } 
  // product_delivery_time
  if(!empty($_POST['product_delivery_time'])){$product_delivery_time = $_POST['product_delivery_time'];}
	if( !empty( $product_delivery_time ) ){
		update_post_meta( $post_id, 'product_delivery_time', esc_attr( $product_delivery_time ) );        
  }else{
    delete_post_meta( $post_id, 'product_delivery_time' );
  }  
  
  }
  



/**
 * Create new fields for variations
 *
*/
public function variable_fields( $loop, $variation_data, $variation ) {

?>
	<tr>
		<td colspan="2">
			<?php
			
      include('includes/custom-variation-data.php');
      
			?>
		</td>
	</tr>
<?php
}
 
/**
 * Create new fields for new variations
 *
*/
public function variable_fields_js() {
?>
	<tr>
		<td>
			<?php
			// Text Field
			woocommerce_wp_text_input( 
				array( 
					'id'          => '_variation_heureka_title[ + loop + ]', 
					'label'       => __( 'Variation Heureka title', 'woocommerce' ), 
					'placeholder' => '',
					'desc_tip'    => 'true',
					'description' => __( 'Enter the custom title for feed', 'woocommerce' ),
					'value'       => $variation_data['_variation_heureka_title'][0]
				)
			);
			?>
		</td>
	</tr>
<?php
}
 
/**
 * Save new fields for variations
 *
*/
public function save_variable_fields( $post_id ) {
	if (isset( $_POST['variable_sku'] ) ) :
 
		$variable_sku          = $_POST['variable_sku'];
		$variable_post_id      = $_POST['variable_post_id'];
		
		// Text Field
		$_variation_heureka_title         = $_POST['_variation_heureka_title'];
    $_variation_heureka_name          = $_POST['_variation_heureka_name'];
    $_variation_heureka_category      = $_POST['_variation_heureka_category'];
    $_variation_pricemania_category   = $_POST['_variation_pricemania_category'];
    $_variation_najnakup_category     = $_POST['_variation_najnakup_category'];
    $_variation_imgurl_alternative    = $_POST['_variation_imgurl_alternative'];
    $_variation_heureka_video_url     = $_POST['_variation_video_url'];
    $_variation_heureka_delivery_date = $_POST['_variation_delivery_date'];
    $_variation_heureka_accessory     = $_POST['_variation_accessory'];
    $_variation_heureka_dues          = $_POST['_variation_dues'];
    
    
		for ( $i = 0; $i < sizeof( $variable_sku ); $i++ ) :
			$variation_id = (int) $variable_post_id[$i];

      if ( isset( $_variation_heureka_name[$i] ) ) {
				update_post_meta( $variation_id, '_variation_heureka_name', stripslashes( $_variation_heureka_name[$i] ) );
			}else{
        delete_post_meta( $variation_id, '_variation_heureka_name' );
      }
      
      if ( isset( $_variation_heureka_title[$i] ) ) {
				update_post_meta( $variation_id, '_variation_heureka_title', stripslashes( $_variation_heureka_title[$i] ) );
			}else{
        delete_post_meta( $variation_id, '_variation_heureka_title' );
      }
      
      if ( isset( $_variation_heureka_category[$i] ) ) {
				update_post_meta( $variation_id, '_variation_heureka_category', stripslashes( $_variation_heureka_category[$i] ) );
			}else{
        delete_post_meta( $variation_id, '_variation_heureka_category' );
      }
      if ( isset( $_variation_pricemania_category[$i] ) ) {
				update_post_meta( $variation_id, '_variation_pricemania_category', stripslashes( $_variation_pricemania_category[$i] ) );
			}else{
        delete_post_meta( $variation_id, '_variation_pricemania_category' );
      }
      if ( isset( $_variation_najnakup_category[$i] ) ) {
				update_post_meta( $variation_id, '_variation_najnakup_category', stripslashes( $_variation_najnakup_category[$i] ) );
			}else{
        delete_post_meta( $variation_id, '_variation_najnakup_category' );
      }
      if ( isset( $_variation_imgurl_alternative[$i] ) ) {
      	update_post_meta( $variation_id, '_variation_imgurl_alternative', $_variation_imgurl_alternative[$i] );
    	}else{
        delete_post_meta( $variation_id, '_variation_imgurl_alternative' );
      }
      if ( isset( $_variation_heureka_video_url[$i] ) ) {
				update_post_meta( $variation_id, '_variation_video_url', stripslashes( $_variation_heureka_video_url[$i] ) );
			}else{
        delete_post_meta( $variation_id, '_variation_video_url' );
      }
      if ( isset( $_variation_heureka_delivery_date[$i] ) ) {
				update_post_meta( $variation_id, '_variation_delivery_date', stripslashes( $_variation_heureka_delivery_date[$i] ) );
			}else{
        delete_post_meta( $variation_id, '_variation_delivery_date' );
      }
      if ( isset( $_variation_heureka_accessory[$i] ) ) {
				update_post_meta( $variation_id, '_variation_accessory', stripslashes( $_variation_heureka_accessory[$i] ) );
			}else{
        delete_post_meta( $variation_id, '_variation_accessory' );
      }
      if ( isset( $_variation_heureka_dues[$i] ) ) {
				update_post_meta( $variation_id, '_variation_dues', stripslashes( $_variation_heureka_dues[$i] ) );
			}else{
        delete_post_meta( $variation_id, '_variation_dues' );
      }
      
		endfor;
		
	endif;
}

/**
 *
 * Heureka category walker
 *
 */   
public function heureka_xml_loop($data,$category_id){
  global $heureka_categories;
  if(!empty($data)){
     foreach($data as $item){
  
  $item_id       = (string)$item->CATEGORY_ID;
  $item_name     = (string)$item->CATEGORY_NAME;
  $item_fullname = (string)$item->CATEGORY_FULLNAME;
  
  if(!empty($item_fullname)){
  $heureka_categories[$item_id]['category_id'] = $item_id;
  $heureka_categories[$item_id]['category_name'] = $item_name;  
  $heureka_categories[$item_id]['category_fullname'] = $item_fullname;   
  }
     $this->heureka_xml_loop($item->CATEGORY,$category_id);
     }
  }
}

  /**
   * Save single option
   *
   * @since 2.0.2  
   */        
  public function save_single_option($name,$option_name){
  
    if(!empty($_POST[$name])){
        $value = sanitize_text_field($_POST[$name]);
        update_option( $option_name, $value );
    }else{
        delete_option( $option_name );
    }  
  
  }
  
  /**
   *
   * Save Heureka delivery
   *
   */  
  public function save_heureka_delivery($name = 'woo_heureka_delivery'){
  
    $heureka_delivery = array();
      foreach($_POST['delivery_id'] as $key => $item){
  
            $heureka_delivery[$_POST['delivery_id'][$item]]['id']                 = $_POST['delivery_id'][$item];
            $heureka_delivery[$_POST['delivery_id'][$item]]['delivery_price']     = $_POST['delivery_price'][$item];
            $heureka_delivery[$_POST['delivery_id'][$item]]['delivery_price_cod'] = $_POST['delivery_price_cod'][$item];
    
    
          if(!empty($_POST['delivery_active'][$item])){  
            $heureka_delivery[$_POST['delivery_id'][$item]]['active'] = 'on';
          }else{
            $heureka_delivery[$_POST['delivery_id'][$item]]['active'] = 'no';
          }
  
      }
  //update_option( 'woo_heureka_delivery', $heureka_delivery );
  
    update_option( $name, $heureka_delivery );
  }  
  
  /**
   * Get Heureka categorie
   *
   * @since 2.0.2
   */           
  public function get_heureka_categories($country = 'cz'){
  
      if($country == 'cz'){
        $option_name = 'woo_heureka_categories';
        $xml = 'http://www.heureka.cz/direct/xml-export/shops/heureka-sekce.xml';
      }elseif($country == 'sk'){
        $option_name = 'woo_heureka_categories_sk';
        $xml ='http://www.heureka.sk/direct/xml-export/shops/heureka-sekce.xml';
      }
  
      $heureka_categories = get_option( $option_name);
      if(empty($heureka_categories)){
        $heureka_categories = array();

        $feed = simplexml_load_file($xml);

          foreach($feed->CATEGORY as $first){
            $first_id   = (string)$first->CATEGORY_ID;
            $first_name = (string)$first->CATEGORY_NAME;
            $heureka_categories[$first_id]['category_id'] = $first_id;
            $heureka_categories[$first_id]['category_name'] = $first_name;
            $heureka_categories[$first_id]['category_fullname'] = '';
            $this->heureka_xml_loop($first->CATEGORY,$first_id);
          }

          update_option( $option_name, $heureka_categories );
 
          $heureka_categories = get_option( $option_name);
      }
    return $heureka_categories;
  }  
  
  


    /**
   * Get Heureka categorie
   *
   * @since 2.1.1
   */           
  public function get_google_categories(){
  
      //CZ
  $file ='http://www.google.com/basepages/producttype/taxonomy-with-ids.cs-CZ.txt';
  
  $lines = file($file);
  
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
  
    update_option( 'woo_google_categories', $google_categories );
  
    $google_categories = get_option( 'woo_google_categories');
    return $google_categories;
  }  


}
