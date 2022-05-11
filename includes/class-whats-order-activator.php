<?php

/**
 * Fired during plugin activation
 *
 * @link       criativi.me
 * @since      1.0.0
 *
 * @package    Whats_Order
 * @subpackage Whats_Order/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Whats_Order
 * @subpackage Whats_Order/includes
 * @author     Criativi <plugins@criativi.me>
 */
class Whats_Order_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		add_role( 'seller', __('Vendedor'), get_role( 'subscriber' )->capabilities );
	}

}
