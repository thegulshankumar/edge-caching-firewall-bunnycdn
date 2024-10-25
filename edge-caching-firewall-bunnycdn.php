<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.gulshankumar.net
 * @since             1.0.0
 * @package           Edge_Caching_and_Firewall_with_BunnyCDN
 *
 * @wordpress-plugin
 * Plugin Name:       Edge Caching and Firewall with BunnyCDN
 * Plugin URI:        https://www.gulshankumar.net/
 * Description:       Edge Caching and DDoS protection made simple.
 * Version:           1.0.2
 * Author:            Gulshan Kumar
 * Author URI:        https://www.gulshankumar.net
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       edge_caching_firewall_bunnycdn
 * Domain Path:       /languages
 */

/**
 * Edge Caching and Firewall with BunnyCDN
 * Copyright (C) 2021 Gulshan Kumar
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 3 as published
 * by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
 
 // If this file is called directly, abort.
 if ( ! defined( 'WPINC' ) ) {
 	die;
 }

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'EDGE_CACHING_AND_FIREWALL_WITH_BUNNYCDN_SLUG', 'edge-caching-firewall-bunnycdn' );
define( 'EDGE_CACHING_AND_FIREWALL_WITH_BUNNYCDN_VERSION', '1.0.2' );

/**
 * The code that display plugin setting's page link.
 */
function edge_caching_and_firewall_with_bunnycdn_settings_link( $links ) {
	$settings_link = '<a href="options-general.php?page=edge_caching_and_firewall_with_bunnycdn">' . __( 'Settings' ) . '</a>';
	array_push( $links, $settings_link );
	return $links;
}

$filter_name = "plugin_action_links_" . plugin_basename( __FILE__ );
add_filter( $filter_name, "edge_caching_and_firewall_with_bunnycdn_settings_link" );

/**
 * The code that redirect to plugin setting page after activation of plugin.
 * Register 'Origin Access Token' to restrict origin request to the authorized Pull Zone.
 * Disable cookies opt-in checkbox for performance and privacy reasons.
 */
register_activation_hook( __FILE__, 'edge_caching_and_firewall_with_bunnycdn_plugin_activate' );
add_action( 'admin_init', 'edge_caching_and_firewall_with_bunnycdn_plugin_redirect' );

function edge_caching_and_firewall_with_bunnycdn_plugin_activate() {

	$key = isset( $_SERVER['HTTP_COOKIE'] ) ? hash( 'sha256', $_SERVER['HTTP_COOKIE'] ) : hash( 'sha256', time() );
	add_option( 'edge_caching_and_firewall_with_bunnycdn_origin_access_token', $key );

	add_option('edge_caching_and_firewall_with_bunnycdn_plugin_do_activation_redirect', true);

	// Disable opt-in once.
	update_option( 'show_comments_cookies_opt_in', '' );
}

function edge_caching_and_firewall_with_bunnycdn_plugin_redirect() {
	if ( get_option( 'edge_caching_and_firewall_with_bunnycdn_plugin_do_activation_redirect', false ) ) {
		delete_option( 'edge_caching_and_firewall_with_bunnycdn_plugin_do_activation_redirect' );
		if( ! isset( $_GET['activate-multi'] ) ) {
			wp_redirect( admin_url( 'options-general.php?page=edge_caching_and_firewall_with_bunnycdn&tab=help' ) );
			exit();
		}
	}
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-edge_caching_and_firewall_with_bunnycdn-activator.php
 */
function activate_edge_caching_and_firewall_with_bunnycdn() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-edge-caching-firewall-bunnycdn-activator.php';
	Edge_Caching_and_Firewall_with_BunnyCDN_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-edge_caching_and_firewall_with_bunnycdn-deactivator.php
 */
function deactivate_edge_caching_and_firewall_with_bunnycdn() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-edge-caching-firewall-bunnycdn-deactivator.php';
	Edge_Caching_and_Firewall_with_BunnyCDN_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_edge_caching_and_firewall_with_bunnycdn' );
register_deactivation_hook( __FILE__, 'deactivate_edge_caching_and_firewall_with_bunnycdn' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */                                             
require plugin_dir_path( __FILE__ ) . 'includes/class-edge-caching-firewall-bunnycdn.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-edge-caching-firewall-bunnycdn-flash.php';
require plugin_dir_path( __FILE__ ) . 'bunnycdn-api.php';
require plugin_dir_path( __FILE__ ) . 'functions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_edge_caching_and_firewall_with_bunnycdn() {

	$plugin = new Edge_Caching_and_Firewall_with_BunnyCDN();
	$plugin->run();

}
run_edge_caching_and_firewall_with_bunnycdn();
