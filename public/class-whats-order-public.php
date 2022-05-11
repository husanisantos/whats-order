<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       criativi.me
 * @since      1.0.1
 *
 * @package    Whats_Order
 * @subpackage Whats_Order/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Whats_Order
 * @subpackage Whats_Order/public
 * @author     Criativi <plugins@criativi.me>
 */
class Whats_Order_Public {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function store_seller_id() {
		if(!is_admin()) {
			$data = filter_var($_GET['vendedor'], FILTER_VALIDATE_INT);

			if($data) {
				$user = get_userdata($data);
			
				if($user and in_array('seller', $user->roles) ) {
				    
					setcookie("vendedor", "$user->ID");
					$link = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				
					header("Location: " .  Whats_Order_Public::strip_param_from_url($link, 'vendedor'));
					exit; 
				} 
			}
		}
					
	}
	
	static function strip_param_from_url( $url, $param ) {
        $base_url = strtok($url, '?');              // Get the base url
        $parsed_url = parse_url($url);              // Parse it 
        $query = $parsed_url['query'];              // Get the query string
        parse_str( $query, $parameters );           // Convert Parameters into array
        unset( $parameters[$param] );               // Delete the one you want
        $new_query = http_build_query($parameters); // Rebuilt query string
        return $base_url . (empty($new_query) ? '' : '?' . $new_query);            // Finally url is ready
    }

	/**
	 * @since    1.0.5
	 * @package  Whats_Order
	*/
	public function woocommerce_add_whatsapp_button(){ 
		global $product; 
		
		$api    = wp_is_mobile() ? 'https://api.whatsapp.com/send?phone=' : 'https://web.whatsapp.com/send?phone=';
		$seller = !empty($_COOKIE["vendedor"] ) ? $_COOKIE["vendedor"] : false;
		
		if($seller) { 
			$data  = get_userdata($seller);
			$phone = get_user_meta( $data->ID, 'whatsapp', true);
		} else {
			$phone  = get_option( 'whatsapp' );
		}

		$product = wc_get_product($product->id);
		$symbol  = get_woocommerce_currency_symbol();
		$hello	 = $seller ? sprintf(__('Olá *%s*, tenho interesse no produto', 'whats-order'), $data->display_name) : __('Olá, tenho interesse no produto', 'whats-order');
		$price   = number_format($product->get_price(),2,",",".");

		$link  = $api;
		$link .= $phone; 
		$link .= '&text=';
		$link .= "$hello %0D%0A%0D%0A";
		$link .= "*$product->name* %0D%0A";
		$link .= __('Preço:', 'whats-order') . " *$symbol$price* %0D%0A%0D%0A";
		$link .= __('Poderia me ajudar?', 'whats-order');

		?>

		<button class="whatsapp-button" onclick="OpenWhatsapp('<?php echo html_entity_decode($link) ?>')">
			<div class="whatsapp-icon">
				<svg enable-background="new 0 0 24 24" height="512" viewBox="0 0 24 24" width="512" xmlns="http://www.w3.org/2000/svg"><path d="m20.52 3.449c-2.28-2.204-5.28-3.449-8.475-3.449-9.17 0-14.928 9.935-10.349 17.838l-1.696 6.162 6.335-1.652c2.76 1.491 5.021 1.359 5.716 1.447 10.633 0 15.926-12.864 8.454-20.307z" fill="#eceff1"/><path d="m12.067 21.751-.006-.001h-.016c-3.182 0-5.215-1.507-5.415-1.594l-3.75.975 1.005-3.645-.239-.375c-.99-1.576-1.516-3.391-1.516-5.26 0-8.793 10.745-13.19 16.963-6.975 6.203 6.15 1.848 16.875-7.026 16.875z" fill="#4caf50"/><path d="m17.507 14.307-.009.075c-.301-.15-1.767-.867-2.04-.966-.613-.227-.44-.036-1.617 1.312-.175.195-.349.21-.646.075-.3-.15-1.263-.465-2.403-1.485-.888-.795-1.484-1.77-1.66-2.07-.293-.506.32-.578.878-1.634.1-.21.049-.375-.025-.524-.075-.15-.672-1.62-.922-2.206-.24-.584-.487-.51-.672-.51-.576-.05-.997-.042-1.368.344-1.614 1.774-1.207 3.604.174 5.55 2.714 3.552 4.16 4.206 6.804 5.114.714.227 1.365.195 1.88.121.574-.091 1.767-.721 2.016-1.426.255-.705.255-1.29.18-1.425-.074-.135-.27-.21-.57-.345z" fill="#fafafa"/></svg>
			</div>
			<span><?php _e('Comprar','whats-order') ?></span>
		</button>

		<script>  
			function OpenWhatsapp(info) { 
				window.open(info, '_blank'); 
			} 
		</script>
 
	<?php 	
	}

	public function add_stock_qty(){
		global $product;
		$stock = $product->get_stock_quantity();
		if($stock == 0)
			echo '<p style="color: red;">Esgotado</p>';
		else
			if($stock == 1)
			echo '<p style="color: red;">Última unidade disponível</p>';
		else
		echo "<p style='color: green;'>$stock disponíveis</p>";
	}

	public function woocommerce_remove_add_to_cart_button() {
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	}


	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/whats-order-public.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/whats-order-public.js', array( 'jquery' ), $this->version, false );

	}

}
