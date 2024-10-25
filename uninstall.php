<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://www.gulshankumar.net
 * @since      1.0.0
 *
 * @package    Edge_Caching_and_Firewall_with_BunnyCDN
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Drop main settings
delete_option( 'edge_caching_and_firewall_with_bunnycdn_settings' );

// Drop Origin Access Token settings
delete_option( 'edge_caching_and_firewall_with_bunnycdn_cloudfirewall_settings' );

// Drop Origin Access Token (Ignored to allow graceful re-installation)
// delete_option( 'edge_caching_and_firewall_with_bunnycdn_origin_access_token' );


