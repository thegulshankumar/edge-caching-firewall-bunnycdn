<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.gulshankumar.net
 * @since      1.0.0
 *
 * @package    Edge_Caching_and_Firewall_with_BunnyCDN
 * @subpackage Edge_Caching_and_Firewall_with_BunnyCDN/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Edge_Caching_and_Firewall_with_BunnyCDN
 * @subpackage Edge_Caching_and_Firewall_with_BunnyCDN/includes
 * @author     Gulshan Kumar <admin@gulshankumar.net>
 */
class Edge_Caching_and_Firewall_with_BunnyCDN_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'edge_caching_and_firewall_with_bunnycdn',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
