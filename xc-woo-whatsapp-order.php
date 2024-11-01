<?php
/*
  Plugin Name:  Woocommerce Orders on Whatsapp
  Plugin URI:   https://xperts.club/
  Description:  Woocommerce Orders on Whatsapp allows your customers to contact you and chat via Whatsapp directly from your wordpress/woocommerce products pages to the mobile..
  Version:      1.0
  Author:       XpertsClub
  Author URI:   https://xperts.club/
  License:      GPL2
  License URI:  https://www.gnu.org/licenses/gpl-2.0.html
  Text Domain:  xc-woo-order-on-whatapp
  Domain Path:  /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'woocommerce/woocommerce.php')){
	new XC_Woo_Order_On_Whatsapp();
}else{
	add_action( 'admin_notices', 'xc_woo_order_on_whatsapp_installed_notice' );
}

function xc_woo_order_on_whatsapp_installed_notice()
{
	?>
    <div class="error">
      <p><?php _e( 'Woocommerce Orders on Whatsapp requires the WooCommerce plugin. Please install or activate WooCommerce!', 'xc-woo-order-on-whatapp'); ?></p>
    </div>
    <?php
}


/**
 * XC_Woo_Order_On_Whatsapp class.
 */
class XC_Woo_Order_On_Whatsapp{
	 /**
     * Constructor.
     */
    public function __construct() {
        //this action callback is triggered when wordpress is ready to add new items to menu.
		add_action("admin_menu", array($this,"add_new_xc_woo_order_on_whatsapp_settings_menu"));
		
		add_action("admin_init", array($this, "xc_woo_order_on_whatsapp_display_options"));
		
		add_action('init', array($this, "xc_woocommerce_plugin_woo_order_on_whatsapp"));
		
		add_action('wp_enqueue_scripts', array($this, "xc_frontend_styles"));
    }
	
	/* WordPress Menus API. */
	public function add_new_xc_woo_order_on_whatsapp_settings_menu() {
	//add a new menu item. This is a top level menu item i.e., this menu item can have sub menus
		add_menu_page(
				"Woo Order on Whatsapp", //Required. Text in browser title bar when the page associated with this menu item is displayed.
				"Woo Order on Whatsapp", //Required. Text to be displayed in the menu.
				"manage_options", //Required. The required capability of users to access this menu item.
				"xc-woo_order-on-whatsapp", //Required. A unique identifier to identify this menu item.
				array($this,"xc_woo_order_on_whatsapp_theme_options_page"), //Optional. This callback outputs the content of the page associated with this menu item.
				"", //Optional. The URL to the menu item icon.
				100 //Optional. Position of the menu item in the menu.
		);
	}
	
	//Optional. This callback outputs the content of the page associated with this menu item.
	public function xc_woo_order_on_whatsapp_theme_options_page() {
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h1>Whatsapp Settings</h1>
			<form method="post" action="options.php">
				<?php
				//add_settings_section callback is displayed here. For every new section we need to call settings_fields.
				settings_fields("xc_woo_order_on_whatsapp_section");
				// all the add_settings_field callbacks is displayed here
				do_settings_sections("xc-woo_order-on-whatsapp");
	
				// Add the submit button to serialize the options
				submit_button();
				?>          
			</form>
		</div>
		<?php
	}
	
	//    Add the Settings Page
	public function xc_woo_order_on_whatsapp_display_options() {
	
	// Add Section For Setting Page
		add_settings_section("xc_woo_order_on_whatsapp_section", "Whatsapp Settings Options", array($this, "display_xc_whatsapp_section_content"), "xc-woo_order-on-whatsapp");
	
	// Add the Fields
		add_settings_field("xc_whatsapp_enable", __("Enable Woo Order on Whatsapp", "xc-woo-order-on-whatapp"), array($this,"display_xc_whatsapp_element"), "xc-woo_order-on-whatsapp", "xc_woo_order_on_whatsapp_section");
		add_settings_field("xc_whatsapp_no", __("Whatsapp Number", "xc-woo-order-on-whatapp"), array($this,"display_xc_whatsapp_no_element"), "xc-woo_order-on-whatsapp", "xc_woo_order_on_whatsapp_section");
		add_settings_field("xc_whatsapp_message", __("Whatsapp Message", "xc-woo-order-on-whatapp"), array($this, "display_xc_whatsapp_message_element"), "xc-woo_order-on-whatsapp", "xc_woo_order_on_whatsapp_section");
		add_settings_field("xc_whatsapp_btn_text", __("Whatsapp Button Text", "xc-woo-order-on-whatapp"), array($this, "display_xc_whatsapp_btn_text_element"), "xc-woo_order-on-whatsapp", "xc_woo_order_on_whatsapp_section");
	
	
	
		add_option('xc_woo_order_on_whatsapp_message', 'Hi I would like to buy {product_name}');
		add_option('xc_woo_order_on_whatsapp_btn_text', 'Buy on Whatsapp');
	// Register The Fields By the input names
		register_setting("xc_woo_order_on_whatsapp_section", "xc_woo_order_on_whatsapp_enable");
		register_setting("xc_woo_order_on_whatsapp_section", "xc_woo_order_on_whatsapp_no");
		register_setting("xc_woo_order_on_whatsapp_section", "xc_woo_order_on_whatsapp_message");
		register_setting("xc_woo_order_on_whatsapp_section", "xc_woo_order_on_whatsapp_btn_text");
	}
	
	/**
	 *  Display The Whatsapp Settings By The Function Call
	 */
	public function display_xc_whatsapp_section_content() {
	//echo "The header of the theme";
	}
	
	//  Enable Whats App In Plugin
	public function display_xc_whatsapp_element() {
	
		if (get_option('xc_woo_order_on_whatsapp_enable') === 1 || get_option('xc_woo_order_on_whatsapp_enable') === '1') {
			?>
			<input type="checkbox" name="xc_woo_order_on_whatsapp_enable" id="xc_woo_order_on_whatsapp_enable" checked="checked" value="1" />
		<?php } else {
			?>
			<input type="checkbox" name="xc_woo_order_on_whatsapp_enable" id="xc_woo_order_on_whatsapp_enable" value="1" />
			<?php
		}
	}
	
	//  Whatsapp Number
	public function display_xc_whatsapp_no_element() {
		?>
		<input type="tel" name="xc_woo_order_on_whatsapp_no" id="xc_woo_order_on_whatsapp_no" value="<?php echo get_option('xc_woo_order_on_whatsapp_no'); ?>" />
		<?php
	}
	
	//  Whats app Message in Orders
	public function display_xc_whatsapp_message_element() {
		?>
		<textarea rows="4" cols="50" name="xc_woo_order_on_whatsapp_message" id="xc_woo_order_on_whatsapp_message"><?php echo get_option('xc_woo_order_on_whatsapp_message'); ?></textarea>
		<p><i><?php _e('Available placeholder {product_name}', 'xc-woo-order-on-whatapp'); ?></i></p>
		<?php
	}
	//  Whatsapp button text
	public function display_xc_whatsapp_btn_text_element() {
		?>
		<input type="text" name="xc_woo_order_on_whatsapp_btn_text" id="xc_woo_order_on_whatsapp_btn_text" value="<?php echo get_option('xc_woo_order_on_whatsapp_btn_text'); ?>" />
		<p><i><?php _e('Buy on whatsapp', 'xc-woo-order-on-whatapp'); ?></i></p>
		<?php
	}
	
	public function xc_woocommerce_plugin_woo_order_on_whatsapp() {
		if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) && get_option('xc_woo_order_on_whatsapp_enable') === '1') {
			add_action('woocommerce_single_product_summary', array($this, 'xc_woo_order_on_whatsapp_shortcode'), 31);
		}
	}
	
	public function xc_woo_order_on_whatsapp_shortcode() {
		global $product;
		$xc_whatsapp_message = get_option('xc_woo_order_on_whatsapp_message');
		$xc_whatsapp_message = str_replace('{product_name}', $product->get_title(), $xc_whatsapp_message);
		echo '<a class="xc-woo-order-whatsapp-btn" href="https://api.whatsapp.com/send?phone=' . get_option('xc_woo_order_on_whatsapp_no') . '&text=' . $xc_whatsapp_message . '" target="_blank"> <img src="' . plugins_url('/assets/images/whatsapp.png', __FILE__) . '" alt=""/> ' . get_option('xc_woo_order_on_whatsapp_btn_text') . ' </a>';
	}
	
	public function xc_frontend_styles() {
		wp_register_style('xcwhatsapp_button_styles', '');
		wp_enqueue_style('xcwhatsapp_button_styles');
		$frontend_style = ""
				. ".xc-woo-order-whatsapp-btn{"
				. "padding: 5px 10px;"
				. "background-color: #2ab200;"
				. "text-align: center;"
				. "vertical-align: middle;"
				. "color: white;"
				. "font-weight: 600;"
				. "border-radius: 5px;"
				. "display:inline-block;"
				. "margin: 10px 0px;"
				. "}"
				. ".xc-woo-order-whatsapp-btn img{"
				. "display: inline-block;"
				. "max-width: 30px;"
				. "vertical-align: middle;"
				. "margin-right: 5px;"
				. "}";
	
		wp_add_inline_style('xcwhatsapp_button_styles', $frontend_style);
	}
	
}
