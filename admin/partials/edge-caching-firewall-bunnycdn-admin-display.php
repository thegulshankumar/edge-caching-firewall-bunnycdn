<?php
defined( 'ABSPATH' ) OR exit;
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.gulshankumar.net
 * @since      1.0.0
 *
 * @package    Edge_Caching_and_Firewall_with_BunnyCDN
 * @subpackage Edge_Caching_and_Firewall_with_BunnyCDN/admin/partials
 */


/* This file should primarily consist of HTML with a little bit of PHP. */
?>

<div class="wrap">

	<h1>
		<?php esc_html_e( get_admin_page_title() ); ?>
	</h1>

	<?php

		$active_tab = 'edge_caching';
		if ( isset( $_GET[ 'tab' ] ) ) {
			$active_tab = sanitize_key( $_GET[ 'tab' ] );
		}

	?>

	<h2 class="nav-tab-wrapper">
		<a href="?page=edge_caching_and_firewall_with_bunnycdn&tab=edge_caching" class="nav-tab <?php echo $active_tab == 'edge_caching' ? 'nav-tab-active' : ''; ?>">Edge Caching</a>
		<a href="?page=edge_caching_and_firewall_with_bunnycdn&tab=cloud_firewall" class="nav-tab <?php echo $active_tab == 'cloud_firewall' ? 'nav-tab-active' : ''; ?>">Cloud Firewall</a>
		<a href="?page=edge_caching_and_firewall_with_bunnycdn&tab=help" class="nav-tab <?php echo $active_tab == 'help' ? 'nav-tab-active' : ''; ?>">Help</a>
	</h2>

	<form method="post" action="options.php">

		<?php

			if ( $active_tab == 'edge_caching' ) {

				if (Edge_Caching_and_Firewall_with_BunnyCDN_Flash::check()) {
					$flash = Edge_Caching_and_Firewall_with_BunnyCDN_Flash::get();
					echo '
						<div class="notice notice-' . esc_attr($flash['type']). ' is-dismissible">
							<p><strong>' . esc_html($flash['message']). '</strong></p>
						</div>
					';
				}

				settings_fields( 'edge_caching_and_firewall_with_bunnycdn_settings' );
				do_settings_sections( 'edge_caching_and_firewall_with_bunnycdn' );
				submit_button("Setup Pull Zone");
				edge_caching_and_firewall_with_bunnycdn_dns_ssl_html();
			}

			if ( $active_tab == 'cloud_firewall' ) {
				settings_fields('edge_caching_and_firewall_with_bunnycdn_cloudfirewall_settings');
				do_settings_sections( 'edge_caching_and_firewall_with_bunnycdn_cloudfirewall' );
				submit_button("Update Settings");
			}

			if ( $active_tab == 'help' ) {

				//@todo: Expand this section with video.
		?>
			<h2><?php _e( 'Advanced Recommendations' ) ?></h2>
			<div style="font-size:16px; line-height:1.55689em;">
				<ol>
					<li><?php echo sprintf( __( 'Consider using <a target="_blank" rel="noopener noreferrer"  href="%s">Cloudflare in DNS only mode</a>.' ), 'https://www.gulshankumar.net/using-cloudflare-dns-without-cdn-or-waf/' ) ?></li>

					<li><?php _e( 'Deactivate existing <strong>Caching</strong> system if any. If you have added any custom NGINX snippet, or have .htaccess code that should be removed as well.' ); ?></li>
				</ol>
				<h3><?php _e( 'For HTTPS enabled site' ) ?></h3>
				<ol>
					<li><?php _e( 'Be it active, expired or even self-signed cert at origin can work.' ); ?>  </li>
					<li><?php echo sprintf( __( 'However, a valid cert is strongly recommended for a mission-critical site and an optional security feature: <a target="_blank" rel="noopener noreferrer"  href="%s">verify origin SSL</a>.' ), 'https://bunny.net/blog/origin-ssl-certificate-verification/' ) ?></li>
				</ol>
				<h2><?php _e( 'Which WebP delivery mode to choose' ) ?>?</h2>
				<p><strong><?php _e( 'Note' ) ?></strong>: <?php _e( 'This plugin will not generate WebP files. You are welcome to use an external plugin or services for it.' ) ?></p>
				<ol>
					<li><strong><?php _e( 'Off (Default)' ); ?></strong>: <?php _e( 'When you do not use WebP at all or maybe you have Picture mode enabled' ) ?>.</li>
					<li><strong><?php _e( 'Vary Cache' ) ?></strong>: <?php echo sprintf( __( 'Great companion for WebP Express or <a title="Referral link" target="_blank" rel="noopener noreferrer"  href="%s">ShortPixel</a> or similar plugin with varied response mode using .htaccess or NGINX rewrite.' ), 'https://shortpixel.com/otp/af/HLH6IPY33697' ) ?></li>
					<li><strong><?php _e( 'Optimizer (Paid)' ) ?></strong>: Perfect solution for big site who wish to save disk space and image bandwidth cost up to 63% with on-the-fly WebP serving to supported browsers.</li>
				</ol>
				<h2><?php _e( 'How to prevent direct access to origin for DDoS protection' ) ?>?</h2>
				<ol>
					<li><?php _e( 'Enable Origin Access Token in Cloud Firewall page' ) ?></li>
					<li><?php _e( 'If you get locked by chance, temporarily set a constant' ) ?> <code>define( 'HTTP_ORIGIN_ACCESS_TOKEN', true );</code> in the <code>wp-config.php</code></li>
				</ol>
				<h2><?php _e( 'Which hosting shall I choose for the faster Dashboard experience' ) ?>?</h2>
				<ol>
					<li><?php _e( 'The best choice depends on your current location' ) ?>.</li>
					<li><?php _e( 'If you are from India' ) ?>, <strong><?php echo sprintf( __( '<a target="_blank" rel="noopener noreferrer"  href="%s">DigitalOcean</a>' ), 'https://affiliate.gulshankumar.net/DigitalOcean' ) ?></strong> 
					<?php _e( 'is a perfect choice' ) ?>.<strong> <?php _e( 'Disclosure' ) ?></strong>: <?php _e( 'Referral link. We both may get some free credits' ) ?> 🤗</li>
				</li></ol>
			<h2><?php _e( 'How to get own choice Pull Zone name' ) ?>?</h2>
				<ol>
					<li><?php _e( 'This is an <em>optional</em> step for adventurous users and subject to availability. To get vanity zone name, always keep a constant ') ?> <code>define( 'EDGE_CACHING_AND_FIREWALL_WITH_BUNNYCDN_PULLZONE', 'my_awesome_site' );</code></li>
		</ol></div>
		<?php
					;

			}

		?>

	</form>
</div>