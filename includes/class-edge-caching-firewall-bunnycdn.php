<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.gulshankumar.net
 * @since      1.0.0
 *
 * @package    Edge_Caching_and_Firewall_with_BunnyCDN
 * @subpackage Edge_Caching_and_Firewall_with_BunnyCDN/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Edge_Caching_and_Firewall_with_BunnyCDN
 * @subpackage Edge_Caching_and_Firewall_with_BunnyCDN/includes
 * @author     Gulshan Kumar <admin@gulshankumar.net>
 */
class Edge_Caching_and_Firewall_with_BunnyCDN {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Edge_Caching_and_Firewall_with_BunnyCDN_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'EDGE_CACHING_AND_FIREWALL_WITH_BUNNYCDN_VERSION' ) ) {
			$this->version = EDGE_CACHING_AND_FIREWALL_WITH_BUNNYCDN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'edge_caching_and_firewall_with_bunnycdn';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->plugin_settings();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Edge_Caching_and_Firewall_with_BunnyCDN_Loader. Orchestrates the hooks of the plugin.
	 * - Edge_Caching_and_Firewall_with_BunnyCDN_i18n. Defines internationalization functionality.
	 * - Edge_Caching_and_Firewall_with_BunnyCDN_Admin. Defines all hooks for the admin area.
	 * - Edge_Caching_and_Firewall_with_BunnyCDN_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-edge-caching-firewall-bunnycdn-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-edge-caching-firewall-bunnycdn-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-edge-caching-firewall-bunnycdn-admin.php';

		$this->loader = new Edge_Caching_and_Firewall_with_BunnyCDN_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Edge_Caching_and_Firewall_with_BunnyCDN_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Edge_Caching_and_Firewall_with_BunnyCDN_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Edge_Caching_and_Firewall_with_BunnyCDN_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Edge_Caching_and_Firewall_with_BunnyCDN_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * The code used to add core functionality of plugin.
	 */
	public function plugin_settings() {


		/**
		* The code used to add setting submenu in wordpress setting menu.
		*/
		add_action("admin_menu", "edge_caching_and_firewall_with_bunnycdn_setting_submenu");
		function edge_caching_and_firewall_with_bunnycdn_setting_submenu() {
		  add_submenu_page(
		        'options-general.php',
		        'Edge Caching and Firewall with BunnyCDN',
		        'BunnyCDN Caching',
		        'administrator',
		        'edge_caching_and_firewall_with_bunnycdn',
		        'edge_caching_and_firewall_with_bunnycdn_setting_submenu_markup'
		    );
		}

		/**
		* The code used to add setting submenu markup.
		*/
		function edge_caching_and_firewall_with_bunnycdn_setting_submenu_markup() {
			if ( !current_user_can("manage_options") ) {
				return;
			}

			include (plugin_dir_path( __DIR__ ) . "admin/partials/edge-caching-firewall-bunnycdn-admin-display.php");
		}

		/**
		* The code used to add setting submenu section/fields.
		*/
		function edge_caching_and_firewall_with_bunnycdn_setting_section() {
			
			add_settings_section(
				"edge_caching_and_firewall_with_bunnycdn_cloud_firewall_settings_section",
				"Cloud Firewall Settings",
				"edge_caching_and_firewall_with_bunnycdn_cloud_firewall_settings_section_callback",
				"edge_caching_and_firewall_with_bunnycdn_cloudfirewall"
			);

			function edge_caching_and_firewall_with_bunnycdn_cloud_firewall_settings_section_callback() {
			  echo __('Prevent direct request to origin without Bunny Proxy');
			}

			add_settings_field(
				'edge_caching_and_firewall_with_bunnycdn_settings_bunnycdn_enable_origin_access_token',
				__( 'Origin Access Token', 'edge_caching_and_firewall_with_bunnycdn_cloudfirewall'),
				'edge_caching_and_firewall_with_bunnycdn_settings_bunnycdn_enable_origin_access_token_callback',
				'edge_caching_and_firewall_with_bunnycdn_cloudfirewall',
				'edge_caching_and_firewall_with_bunnycdn_cloud_firewall_settings_section'
			);

			add_settings_field(
				'edge_caching_and_firewall_with_bunnycdn_settings_bunnycdn_origin_access_token',
				__( 'Token', 'edge_caching_and_firewall_with_bunnycdn_cloudfirewall'),
				'edge_caching_and_firewall_with_bunnycdn_settings_bunnycdn_origin_access_token_callback',
				'edge_caching_and_firewall_with_bunnycdn_cloudfirewall',
				'edge_caching_and_firewall_with_bunnycdn_settings_section',
			    [
			        'class' => 'hidden'
			    ]
			);

			function edge_caching_and_firewall_with_bunnycdn_settings_bunnycdn_enable_origin_access_token_callback() {

				$options = get_option( 'edge_caching_and_firewall_with_bunnycdn_cloudfirewall_settings' );
				if (is_serialized($options)) {
					$options = unserialize($options);
				}

				$bunnycdn_enable_origin_access_token = '';

				if( isset( $options[ 'bunnycdn_enable_origin_access_token' ] ) ) {
					$bunnycdn_enable_origin_access_token = esc_html( $options['bunnycdn_enable_origin_access_token'] );
				}
				
				if ( !is_array($options) ) {
					$options = array();
					$options['bunnycdn_enable_origin_access_token'] = false;
				} else {
					if ( !isset($options['bunnycdn_enable_origin_access_token']) ) {
						$options['bunnycdn_enable_origin_access_token'] = false;
					}
				}

			    $html = '<input type="checkbox" id="bunnycdn_enable_origin_access_token" name="edge_caching_and_firewall_with_bunnycdn_cloudfirewall_settings[bunnycdn_enable_origin_access_token]"  value="1"' . checked( 1, $options['bunnycdn_enable_origin_access_token'], false ) . '/>';
			    $html .= '<label for="bunnycdn_enable_origin_access_token">Enable</label>';

			    echo $html;

			}

			function edge_caching_and_firewall_with_bunnycdn_settings_bunnycdn_origin_access_token_callback() {

				$options = get_option( 'edge_caching_and_firewall_with_bunnycdn_cloudfirewall_settings' );
				if (is_serialized($options)) {
					$options = unserialize($options);
				}

				$bunnycdn_origin_access_token = '';

				if( isset( $options[ 'bunnycdn_origin_access_token' ] ) ) {
					$bunnycdn_origin_access_token = esc_html( $options['bunnycdn_origin_access_token'] );
				}

			    $html = '<input type="hidden" id="bunnycdn_origin_access_token" name="edge_caching_and_firewall_with_bunnycdn_cloudfirewall_settings[bunnycdn_origin_access_token]" value="' . $bunnycdn_origin_access_token . '" />';
			    echo $html;

			}

			add_settings_section(
				"edge_caching_and_firewall_with_bunnycdn_settings_section",
				"Setup BunnyCDN",
				"edge_caching_and_firewall_with_bunnycdn_settings_section_callback",
				"edge_caching_and_firewall_with_bunnycdn"
			);

			add_settings_field(
				'edge_caching_and_firewall_with_bunnycdn_settings_bunnycdn_api_key',
				__( 'API Key', 'edge_caching_and_firewall_with_bunnycdn'),
				'edge_caching_and_firewall_with_bunnycdn_settings_bunnycdn_api_key_callback',
				'edge_caching_and_firewall_with_bunnycdn',
				'edge_caching_and_firewall_with_bunnycdn_settings_section'
			);

			add_settings_field(
				'edge_caching_and_firewall_with_bunnycdn_settings_bunnycdn_webp_image_delivery',
				__( 'WebP Image Delivery', 'edge_caching_and_firewall_with_bunnycdn'),
				'edge_caching_and_firewall_with_bunnycdn_settings_bunnycdn_webp_image_delivery_callback',
				'edge_caching_and_firewall_with_bunnycdn',
				'edge_caching_and_firewall_with_bunnycdn_settings_section',
				[
					'option_one' => 'Off (Default)',
					'option_two' => 'Vary Cache',
					'option_three' => 'Optimizer ($9.5/mo)'
				]
			);

			add_settings_field(
				'edge_caching_and_firewall_with_bunnycdn_settings_site_version',
				__( 'Site Version', 'edge_caching_and_firewall_with_bunnycdn'),
				'edge_caching_and_firewall_with_bunnycdn_settings_site_version_callback',
				'edge_caching_and_firewall_with_bunnycdn',
				'edge_caching_and_firewall_with_bunnycdn_settings_section',
			    [
			        'class' => 'hidden'
			    ]
			);

			add_settings_field(
				'edge_caching_and_firewall_with_bunnycdn_settings_bunnycdn_ssl_certificate',
				__( 'BunnyCDN SSL Certificate', 'edge_caching_and_firewall_with_bunnycdn'),
				'edge_caching_and_firewall_with_bunnycdn_settings_bunnycdn_ssl_certificate_callback',
				'edge_caching_and_firewall_with_bunnycdn',
				'edge_caching_and_firewall_with_bunnycdn_settings_section',
			    [
			        'class' => 'hidden'
			    ]
			);

			add_settings_field(
				'edge_caching_and_firewall_with_bunnycdn_settings_bunnycdn_pullzone_id',
				__( 'BunnyCDN Pullzone ID', 'edge_caching_and_firewall_with_bunnycdn'),
				'edge_caching_and_firewall_with_bunnycdn_settings_bunnycdn_pullzone_id_callback',
				'edge_caching_and_firewall_with_bunnycdn',
				'edge_caching_and_firewall_with_bunnycdn_settings_section',
			    [
			        'class' => 'hidden'
			    ]
			);

			register_setting(
				'edge_caching_and_firewall_with_bunnycdn_settings',
				'edge_caching_and_firewall_with_bunnycdn_settings'
			);

			register_setting(
				'edge_caching_and_firewall_with_bunnycdn_cloudfirewall_settings',
				'edge_caching_and_firewall_with_bunnycdn_cloudfirewall_settings'
			);

			function edge_caching_and_firewall_with_bunnycdn_settings_bunnycdn_api_key_callback() {

				$options = get_option( 'edge_caching_and_firewall_with_bunnycdn_settings' );
				if (is_serialized($options)) {
					$options = unserialize($options);
				}

				$bunnycdn_api_key = '';

				if( isset( $options[ 'bunnycdn_api_key' ] ) ) {
					$bunnycdn_api_key = esc_html( $options['bunnycdn_api_key'] );
				}

				echo '
					<input autocomplete="new-password" type="password" pattern="[a-f0-9-+()]{36,72}$"  id="edge_caching_and_firewall_with_bunnycdn_bunnycdn_api_key" name="edge_caching_and_firewall_with_bunnycdn_settings[bunnycdn_api_key]" value="' . $bunnycdn_api_key . '" />
					<button type="button" name="show_bunnycdn_api_key" id="show_bunnycdn_api_key" class="button button-primary">Show</button>
				';

			}

			function edge_caching_and_firewall_with_bunnycdn_settings_bunnycdn_webp_image_delivery_callback($args) {

				$options = get_option( 'edge_caching_and_firewall_with_bunnycdn_settings' );
				if (is_serialized($options)) {
					$options = unserialize($options);
				}

				$bunnycdn_webp_image_delivery = '';

				if ( isset( $options[ 'bunnycdn_webp_image_delivery' ] ) ) {
					$bunnycdn_webp_image_delivery = esc_html( $options['bunnycdn_webp_image_delivery'] );
				}

				$html = '<select id="edge_caching_and_firewall_with_bunnycdn_bunnycdn_webp_image_delivery" name="edge_caching_and_firewall_with_bunnycdn_settings[bunnycdn_webp_image_delivery]">';
				$html .= '<option value="1"' . selected( $bunnycdn_webp_image_delivery, '1', false) . '>' . $args['option_one'] . '</option>';
				$html .= '<option value="2"' . selected( $bunnycdn_webp_image_delivery, '2', false) . '>' . $args['option_two'] . '</option>';
				$html .= '<option value="3"' . selected( $bunnycdn_webp_image_delivery, '3', false) . '>' . $args['option_three'] . '</option>';
				$html .= '</select>';

				echo $html;

			}

			function edge_caching_and_firewall_with_bunnycdn_settings_site_version_callback() {

				$options = get_option( 'edge_caching_and_firewall_with_bunnycdn_settings' );
				if (is_serialized($options)) {
					$options = unserialize($options);
				}

				$site_version = edge_caching_and_firewall_with_bunnycdn_get_site_version();

				if( isset( $options[ 'site_version' ] ) ) {
					$site_version = esc_html( $options['site_version'] );
				}

				echo '<input type="text" id="edge_caching_and_firewall_with_bunnycdn_site_version" name="edge_caching_and_firewall_with_bunnycdn_settings[site_version]" value="' . $site_version . '" />';

			}

			function edge_caching_and_firewall_with_bunnycdn_settings_bunnycdn_pullzone_id_callback() {

				$options = get_option( 'edge_caching_and_firewall_with_bunnycdn_settings' );
				if (is_serialized($options)) {
					$options = unserialize($options);
				}

				$bunnycdn_pullzone_id = edge_caching_and_firewall_with_bunnycdn_get_bunnycdn_pullzone_id();

				if( isset( $options[ 'bunnycdn_pullzone_id' ] ) ) {
					$bunnycdn_pullzone_id = esc_html( $options['bunnycdn_pullzone_id'] );
				}

				echo '<input type="text" id="edge_caching_and_firewall_with_bunnycdn_bunnycdn_pullzone_id" name="edge_caching_and_firewall_with_bunnycdn_settings[bunnycdn_pullzone_id]" value="' . $bunnycdn_pullzone_id . '" />';
			}

			function edge_caching_and_firewall_with_bunnycdn_settings_bunnycdn_ssl_certificate_callback() {

				$options = get_option( 'edge_caching_and_firewall_with_bunnycdn_settings' );
				if (is_serialized($options)) {
					$options = unserialize($options);
				}

				$bunnycdn_ssl_certificate = edge_caching_and_firewall_with_bunnycdn_get_bunnycdn_ssl_certificate();

				if( isset( $options[ 'bunnycdn_ssl_certificate' ] ) ) {
					$bunnycdn_ssl_certificate = esc_html( $options['bunnycdn_ssl_certificate'] );
				}

				echo '<input type="text" id="edge_caching_and_firewall_with_bunnycdn_bunnycdn_ssl_certificate" name="edge_caching_and_firewall_with_bunnycdn_settings[bunnycdn_ssl_certificate]" value="' . $bunnycdn_ssl_certificate . '" />';
			}

			function edge_caching_and_firewall_with_bunnycdn_settings_section_callback() {
			?><p style="font-size:16px;">
	<?php _e( 'The bunny.net (formerly, known as BunnyCDN) API key is required to manage the zone. Adding this will enable features such as automatic cache purging' ); ?>.</p>
				<ol>
					<li><strong><?php _e( 'New to bunny.net?' ) ?></strong>
				<?php echo sprintf( __( '<a title="Disclosure: Referral URL for bunny.net" target="_blank" rel="noopener noreferrer"  href="%s">Sign up</a>' ), 'https://affiliate.gulshankumar.net/bunny' ); ?>
				</li>
					<li>
			<?php _e( 'Get key in the Account menu' ); ?>
					</li>
				</ol>
			<?php
			}
		}

		add_action( "admin_init", "edge_caching_and_firewall_with_bunnycdn_setting_section" );


		/**
		* The code used to add clear site and page cache menu in wordpress admin bar.
		*/
		$settings = get_option("edge_caching_and_firewall_with_bunnycdn_settings");
		if (is_serialized($settings)) {
			$settings = unserialize($settings);
		}

		$account_key = isset($settings['bunnycdn_api_key']) ? $settings['bunnycdn_api_key'] : false;
		$pullzone_id = isset($settings['bunnycdn_pullzone_id']) ?  $settings['bunnycdn_pullzone_id'] : false;

		if ($account_key && $pullzone_id) {
			add_action( 'admin_bar_menu', 'edge_caching_and_firewall_with_bunnycdn_admin_bar_item', 500 );
		}


		function edge_caching_and_firewall_with_bunnycdn_admin_bar_item( WP_Admin_Bar $admin_bar ) {

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$admin_bar->add_menu( array(
				'id'    => 'bunnycdn_clear_site_cache',
				'parent' => 'top-secondary',
				'title' => 'Clear Site Cache',
				'href'   => wp_nonce_url( add_query_arg( array(
					'_cache'  => 'edge_caching_and_firewall_with_bunnycdn',
					'_action' => 'clear_site_cache',
				) ), 'edge_caching_and_firewall_with_bunnycdn_clear_cache_nonce' ),
				'meta' => ['title' => __( 'Clear Site Cache', 'edge_caching_and_firewall_with_bunnycdn' )]
			) );

			if ( ! is_admin() ) {
				$admin_bar->add_menu( array(
					'id'    => 'bunnycdn_clear_page_cache',
					'parent' => 'top-secondary',
					'title' => 'Clear Page Cache',
					'href'   => wp_nonce_url( add_query_arg( array(
						'_cache'  => 'edge_caching_and_firewall_with_bunnycdn',
						'_action' => 'clear_page_cache',
					) ), 'edge_caching_and_firewall_with_bunnycdn_clear_cache_nonce' ),
					'meta' => ['title' => __( 'Clear Page Cache', 'edge_caching_and_firewall_with_bunnycdn' )]
				) );
			}

		}

		/**
		* The code used to process request of clear site and page cache.
		*/
		add_action( 'init', 'edge_caching_and_firewall_with_bunnycdn_process_clear_cache_request');
		function edge_caching_and_firewall_with_bunnycdn_process_clear_cache_request() {

			if ( empty( $_GET['_cache'] ) || empty( $_GET['_action'] ) || $_GET['_cache'] !== 'edge_caching_and_firewall_with_bunnycdn' || ( $_GET['_action'] !== 'clear_site_cache' && $_GET['_action'] !== 'clear_page_cache' ) ) {
				return;
			}

			if ( empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'edge_caching_and_firewall_with_bunnycdn_clear_cache_nonce' ) ) {
				return;
			}

			$settings = get_option("edge_caching_and_firewall_with_bunnycdn_settings");
			if (is_serialized($settings)) {
				$settings = unserialize($settings);
			}

			$account_key = isset($settings['bunnycdn_api_key']) ? $settings['bunnycdn_api_key'] : false;
			$pullzone_id = isset($settings['bunnycdn_pullzone_id']) ?  $settings['bunnycdn_pullzone_id'] : false;
			$cdn = new bunnycdn_api();

			if ( $account_key && $pullzone_id && $_GET['_action'] === 'clear_page_cache' ) {
				$url = parse_url( home_url(), PHP_URL_SCHEME ) . '://' . parse_url( home_url(), PHP_URL_HOST ) . preg_replace( '/\?.*/', '', $_SERVER['REQUEST_URI'] );
				$cdn->Account( $account_key )->PurgeCache( $url );
			} elseif ( $_GET['_action'] === 'clear_site_cache' ) {
				$cdn->Account( $account_key )->PurgeCache( "", $pullzone_id);

			}

			wp_safe_redirect( wp_get_referer() );
			exit();
		}
	}

}
