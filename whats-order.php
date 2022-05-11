<?php

/**
 *
 * @link              criativi.me
 * @since             1.0.1
 * @package           Whats_Order
 *
 * @wordpress-plugin
 * Plugin Name:       Whats Order
 * Plugin URI:        plugins.criativi.me/whats-order
 * Description:       Apenas um plugin que altera o botão de adicionar ao carrinho do Woocommerce para um chat com vendedores no whatsapp.
 * Version:           1.0.6
 * Author:            Criativi
 * Author URI:        criativi.me
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       whats-order
 * Domain Path:       /languages
 */


if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WHATS_ORDER_VERSION', '1.0.6' );
define( 'PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_URL', plugins_url( 'whats-order' ) );
define( 'UPDATE_URL', 'https://wordpress.criativi.me');


function activate_whats_order() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-whats-order-activator.php';
	Whats_Order_Activator::activate();
}


function deactivate_whats_order() { 
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-whats-order-deactivator.php';
	Whats_Order_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_whats_order' );
register_deactivation_hook( __FILE__, 'deactivate_whats_order' );


require plugin_dir_path( __FILE__ ) . 'includes/class-whats-order.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-user-list-table.php';

function run_whats_order() { 

	$plugin = new Whats_Order();
	$plugin->run();

}

require PLUGIN_DIR . '/includes/update-checker/plugin-update-checker.php';
$UpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	UPDATE_URL . '/?action=get_metadata&slug=whats-order',
	__FILE__, 
	'whats-order'
);

run_whats_order();
