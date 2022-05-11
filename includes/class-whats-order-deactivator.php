<?php

/**
 * Fired during plugin deactivation
 *
 * @link       criativi.me
 * @since      1.0.0
 *
 * @package    Whats_Order
 * @subpackage Whats_Order/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Whats_Order
 * @subpackage Whats_Order/includes
 * @author     Criativi <plugins@criativi.me>
 */
class Whats_Order_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		remove_role( 'seller' );
	}

}
