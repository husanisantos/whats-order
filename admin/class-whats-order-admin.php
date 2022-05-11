<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       criativi.me
 * @since      1.0.0
 *
 * @package    Whats_Order
 * @subpackage Whats_Order/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Whats_Order
 * @subpackage Whats_Order/admin
 * @author     Criativi <plugins@criativi.me>
 */
class Whats_Order_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function enqueue_styles() {
		wp_enqueue_style( 'wp-components' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/whats-order-admin.css', array(), $this->version, 'all' );	
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/whats-order-admin.js', array( 'jquery' ), $this->version, false );
	}

	public function wo_register_settings() {
		register_setting( 'wo-settings-group', 'whatsapp'); 	
	} 


	public function whats_order_menu_page() {
		add_menu_page(
			__( 'Whats Order', 'whats-order' ),
			__( 'Vendedores', 'whats-order' ),
			'manage_options',
			'whats-order',
			'render_whats_order_menu_page',
			plugins_url( 'whats-order/admin/img/icon-light.svg' ),
			6
		);

		function render_whats_order_menu_page(){ ?>
			<div class="wrap">	
				<?php if(!$_GET['action']) : ?>
				<h2>
					<?php _e( 'Vendedores', 'whats-order' ); ?>
					<a href="<?php echo admin_url( 'admin.php?page=whats-order&action=new' ); ?>" class="add-new-h2"><?php _e( 'Adicionar', 'whats-order' ); ?></a>
					<a href="<?php echo admin_url( 'admin.php?page=whats-order-configuration' ); ?>" class="add-new-h2"><?php _e( 'Configurações', 'whats-order' ); ?></a>
				</h2>
				<?php endif; ?>
				<form method="post">
					<?php
					$list_table = new Sellers_List;
        			$list_table->prepare_items();
					$list_table->display();
					?>
				</form>
			</div>
		<?php }


		add_submenu_page(
			'whats-order',
			__( 'Whats Order', 'whats-order' ),
			__( 'Configurações', 'whats-order' ),
			'manage_options',
			'whats-order-configuration',
			'render_whats_order_configuration_page'
		);


		function render_whats_order_configuration_page(){ ?>
			
			<div class="wrap">
				<h1>Configurações</h1>

				<form method="post" action="options.php">
					<?php settings_fields( 'wo-settings-group' ); ?>
					<?php do_settings_sections( 'wo-settings-group' ); ?>
					<table class="form-table  form-sellers">
						<tr valign="top">
						<th scope="row">WhatsApp Padrão</th>
						<td><input type="text" name="whatsapp" value="<?php echo esc_attr( get_option('whatsapp') ); ?>" /></td>
						</tr>
					</table>
					
					<?php submit_button(); ?>

				</form>
			</div>
			
		<?php }

	}


	public function set_post_default_category($id, $post, $updadate) {
		global $post;
		if ( 'seller' == $post->post_type ) {			
			echo '<pre>';
			var_dump($_POST);
			die();
		}

	}


}

require PLUGIN_DIR . '/includes/update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://wordpress.criativi.me/?action=get_metadata&slug=whats-order',
	PLUGIN_DIR . '/whats-order.php', //Full path to the main plugin file or functions.php.
	'whats-order'
);
