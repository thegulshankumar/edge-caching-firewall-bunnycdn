<?php
	// Find request scheme
	function edge_caching_and_firewall_with_bunnycdn_get_request_scheme() {

		if ( isset( $_SERVER['REQUEST_SCHEME'] ) && ( 'https' === $_SERVER['REQUEST_SCHEME'] ) ) {
			return 'https';
		}

		if ( isset( $_SERVER['SERVER_PORT'] ) && ( '443' === $_SERVER['SERVER_PORT'] ) ) {
			return 'https';
		}

		if ( isset( $_SERVER['HTTPS'] ) && ( 'on' === strtolower( $_SERVER['HTTPS'] ) || '1' === $_SERVER['HTTPS'] ) ) {
			return 'https';
		}

		if ( ! empty( $_SERVER['HTTP_X_FORWARDED_SSL'] ) && ( 'on' === $_SERVER['HTTP_X_FORWARDED_SSL'] ) ) {
			return 'https';
		}

		if ( ! empty( $_SERVER['HTTP_X_FORWARDED_SSL'] ) && ( '1' === $_SERVER['HTTP_X_FORWARDED_SSL'] ) ) {
			return 'https';
		}

		if ( ! empty( $_SERVER['HTTP_X_PROTO'] ) && ( 'SSL' === $_SERVER['HTTP_X_PROTO'] ) ) {
			return 'https';
		}

		if ( ! empty( $_SERVER['HTTP_CF_VISITOR'] ) && ( false !== strpos( $_SERVER['HTTP_CF_VISITOR'], 'https' ) ) ) {
			return 'https';
		}

		if ( ! empty( $_SERVER['HTTP_CLOUDFRONT_FORWARDED_PROTO'] ) && ( 'https' === $_SERVER['HTTP_CLOUDFRONT_FORWARDED_PROTO'] ) ) {
			return 'https';
		}

		if ( isset( $_ENV['HTTPS'] ) && ( 'on' === $_ENV['HTTPS'] ) ) {
			return 'https';
		}

		return 'http';
	}

	// Reverse Scheme for Canonical redirection setup
	function edge_caching_and_firewall_with_bunnycdn_reverse_scheme() {

		$scheme = edge_caching_and_firewall_with_bunnycdn_get_request_scheme();

		if ( 'https' === $scheme ) {
			return 'http://';
		}
			return 'https://';
	}

	// Host
	function edge_caching_and_firewall_with_bunnycdn_get_http_host() {

		$http_host = parse_url( get_site_url() );

		return $http_host['host'];
	}

	// Get Zone name
	function edge_caching_and_firewall_with_bunnycdn_get_zone_name() {

			$hostname = edge_caching_and_firewall_with_bunnycdn_get_http_host();
			$hostname_length = strlen( $hostname );

			if ( $hostname_length <= 23  && ! defined( 'EDGE_CACHING_AND_FIREWALL_WITH_BUNNYCDN_PULLZONE' ) ) {

				$zone_name = str_replace( '_', '-', $hostname );
				$zone_name = str_replace( '.', '-', $zone_name );

				return $zone_name;

			} elseif ( $hostname_length > 23   &&  ! defined( 'EDGE_CACHING_AND_FIREWALL_WITH_BUNNYCDN_PULLZONE' ) ) {

				$hashed_hostname = md5( $hostname );
				$zone_name = substr( $hashed_hostname, 0, 23 );

				return $zone_name;

			} elseif ( defined( 'EDGE_CACHING_AND_FIREWALL_WITH_BUNNYCDN_PULLZONE' ) ) {

				$alphanumeric = preg_replace('/[^A-Za-z0-9]/', '', EDGE_CACHING_AND_FIREWALL_WITH_BUNNYCDN_PULLZONE );
				$zone_name = substr( $alphanumeric, 0, 23 );

				return $zone_name;
			}
	}


	// Get Public IPV4 of WordPress server
	function edge_caching_and_firewall_with_bunnycdn_get_server_address() {

    // Primary
    if ( isset( $_SERVER['SERVER_ADDR'] ) 
		&& filter_var( $_SERVER['SERVER_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === $_SERVER['SERVER_ADDR'] ) {
		return $_SERVER['SERVER_ADDR'];
    } 

	// Some args for privacy
	$args = array(
        'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.72 Safari/537.36',
        'redirection' => 0,
        'timeout' => 3,
	);

    // Secondary medium for server behind NAT ( Courtsey: Cloudflare 1.1.1.1 )
    $cf_get = wp_safe_remote_get( 'https://1.1.1.1/cdn-cgi/trace', $args );
    $cf_acceptable_response = ( 200 === wp_remote_retrieve_response_code( $cf_get ) ) ? true : false;
    $cf_body = wp_remote_retrieve_body( $cf_get, $args );
    $regex = '/(?!1.1.1.1)(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/';

    if ( $cf_acceptable_response && preg_match( $regex, $cf_body, $data ) ) {
        $cf_connecting_ip = $data[0];
		$validated_cf_ip = filter_var( $cf_connecting_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ? $cf_connecting_ip : false;
		return $validated_cf_ip;
    }
    // Tertiary as fallback ( Courtsey: ipinfo.io )
	$ipinfo_get = wp_safe_remote_get( 'https://ipinfo.io/ip', $args );
	$ipinfo_acceptable_response = ( 200 === wp_remote_retrieve_response_code( $ipinfo_get ) ) ? true : false;
	$infoip_body = trim( wp_remote_retrieve_body( $ipinfo_get, $args ) );  // returns plain text IP
	$validated_infoip_ip = $ipinfo_acceptable_response && filter_var( $infoip_body, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ? $infoip_body : error_log( 'Something went wrong. Please try later.' );
    	return $validated_infoip_ip;
	}


	// Site Version
	function edge_caching_and_firewall_with_bunnycdn_get_host( $type ) {

		$site_url = edge_caching_and_firewall_with_bunnycdn_get_http_host();
		$non_www = $site_url;
		$www = 'www.' . $site_url;

		if ( strpos( $site_url, 'www.' ) !== false ) {
			$www = $site_url;
			$non_www = str_replace( 'www.', '', $site_url );
		}

		if ( $type === 'www' ) {
			return $www;
		}

		return $non_www;
	}


	// Get Plugin settings
	function edge_caching_and_firewall_with_bunnycdn_get_settings() {

		$settings = get_option( 'edge_caching_and_firewall_with_bunnycdn_settings' );
		if ( is_serialized( $settings ) ) {
			$settings = unserialize( $settings );
		}

		return $settings;
	}


	// Check if Zone exist
	function edge_caching_and_firewall_with_bunnycdn_does_host_exist( $zones ) {

		$www_host_exist = false;
		$non_www_host_exist = false;

		$www_host = edge_caching_and_firewall_with_bunnycdn_get_host( 'www' );
		$non_www_host = edge_caching_and_firewall_with_bunnycdn_get_host( 'non-www' );
		$delete_pullzone_id = '';

		foreach ( $zones as $zone ) {

			foreach ( $zone['host_names'] as $host ) {

				if ( $zone['zone_name'] === edge_caching_and_firewall_with_bunnycdn_get_zone_name() && ( $host === $www_host || $host === $non_www_host ) ) {
					continue;
				}

				if ( $host === $non_www_host ) {
					$delete_pullzone_id = $zone['zone_id'];
					$non_www_host_exist = true;
				}

				if ( $host === $www_host ) {
					$delete_pullzone_id = $zone['zone_id'];
					$www_host_exist = true;
				}

			}

			if ( $non_www_host_exist || $www_host_exist ) {

				if ( is_admin() ) {

					$settings = edge_caching_and_firewall_with_bunnycdn_get_settings();

					if ( $settings && isset($settings['bunnycdn_api_key'] ) ) {

						$bunnycdn_api_key = $settings['bunnycdn_api_key'];
						delete_option( 'edge_caching_and_firewall_with_bunnycdn_settings' );
						$edge_caching_and_firewall_with_bunnycdn_settings = array( 'bunnycdn_api_key'  => $bunnycdn_api_key );
						add_action( 'edge_caching_and_firewall_with_bunnycdn_settings', $edge_caching_and_firewall_with_bunnycdn_settings );

					}
					// In case hostnames are linked in other zone.
					$message = "Hostname " . $non_www_host . " or " . $www_host . " already exist in " . $zone['zone_name'] . ".b-cdn.net Pullzone. Please <a ref='noopener noreferrer' href='https://panel.bunny.net/pullzones/edit/" . $delete_pullzone_id . "#config-delete-zone'>delete hostname</a> from it and try again. ";
					add_settings_error( 'edge_caching_and_firewall_with_bunnycdn_settings', 200, $message, 'error' );
				}

				return true;
			}

		}

		return false;
	}


	// Validate the API key
	function edge_caching_and_firewall_with_bunnycdn_is_valid_api_key( $api_key ) {

		if ( get_option( 'edge_caching_and_firewall_with_bunnycdn_settings' ) ) {

		$args = array(
			'httpversion' => '1.1',
			'headers' => array( 'AccessKey' => $api_key )
		);

		$http_response = wp_remote_retrieve_response_code( wp_safe_remote_head( 'https://bunnycdn.com/api/pullzone/', $args ) );

		if ( 302 === $http_response ) {
			return false;
		}
			return true;
		}
	}

	// Remind updating scheme
	function edge_caching_and_firewall_with_bunnycdn_use_https_instead_of_http_notice() {

		$site_url = get_option( 'siteurl' );
		$home_url = get_option( 'homeurl' );

		if ( edge_caching_and_firewall_with_bunnycdn_is_plugin_setting_page()
		&& ( isset( $_SERVER['SERVER_PORT'] ) && ( '443' === $_SERVER['SERVER_PORT'] ) )
		&& ( false !== strpos( $site_url, 'http://' ) || false !== strpos( $home_url, 'http://' ) )
		&& ( ! isset( $_SERVER['HTTP_VIA'] ) || $_SERVER['HTTP_VIA'] !== 'BunnyCDN' )
		) {

		$message = __( 'Kindly use <strong>HTTPS</strong> scheme instead HTTP in WordPress Address (URL) and Site Address (URL) fields in your <a href="/wp-admin/options-general.php">General Settings</a>', 'edge_caching_and_firewall_with_bunnycdn' );
			if ( is_admin() ) {
				add_settings_error( 'edge_caching_and_firewall_with_bunnycdn_settings', 200, $message, 'error' );
			}
		}
	}

	// settings
	function edge_caching_and_firewall_with_bunnycdn_update_setting( $setting_value, $setting_name ) {
		$settings = edge_caching_and_firewall_with_bunnycdn_get_settings();
		$settings[$setting_name] = $setting_value;
		update_option( 'edge_caching_and_firewall_with_bunnycdn_settings', serialize( $settings ) );
		return $settings;
	}

	add_action( 'admin_notices', 'edge_caching_and_firewall_with_bunnycdn_use_https_instead_of_http_notice' );


	// Get site version.
	function edge_caching_and_firewall_with_bunnycdn_get_site_version() {
		$siteurl = edge_caching_and_firewall_with_bunnycdn_get_http_host();
		if ( substr( $siteurl, 0, 4 ) === 'www.' ) {
			return 'www';
		}
		return 'non-www';
	}


	// Main Settings Page
		function edge_caching_and_firewall_with_bunnycdn_is_plugin_setting_page() {
			if ( isset( $_GET['page'] ) &&  $_GET['page'] === 'edge_caching_and_firewall_with_bunnycdn' && ! isset( $_GET['tab'] ) ) {
				return true;
			} elseif ( isset( $_GET['page'] ) &&  $_GET['page'] === 'edge_caching_and_firewall_with_bunnycdn'  &&  isset( $_GET['tab'] ) &&  $_GET['tab'] === 'edge_caching'  ) {
				return true;
			// skip HTTP API call at following pages
			} elseif ( isset( $_GET['page'] ) &&  $_GET['page'] === 'edge_caching_and_firewall_with_bunnycdn'  &&  isset( $_GET['tab'] ) &&  $_GET['tab'] === 'cloud_firewall'  ) {
				return false;
			} elseif  ( isset( $_GET['page'] ) &&  $_GET['page'] === 'edge_caching_and_firewall_with_bunnycdn'  &&  isset( $_GET['tab'] ) &&  $_GET['tab'] === 'help'  ) {
				return false;
			}
	}

	// Get Pull Zone ID
	function edge_caching_and_firewall_with_bunnycdn_get_bunnycdn_pullzone_id() {

		$settings = get_option( 'edge_caching_and_firewall_with_bunnycdn_settings' );
		if ( is_serialized( $settings ) ) {
			$settings = unserialize($settings);
		}

		$bunnycdn_pullzone_id = isset( $settings['bunnycdn_pullzone_id'] ) ? $settings[ 'bunnycdn_pullzone_id' ] : false;
		if ( $bunnycdn_pullzone_id ) {
			return $bunnycdn_pullzone_id;
		}
		return 0;
	}


	// Check presence of TLS certs
	function edge_caching_and_firewall_with_bunnycdn_get_bunnycdn_ssl_certificate() {

		if ( get_option( 'edge_caching_and_firewall_with_bunnycdn_settings' ) ) {

			$bunnycdn_has_ssl_certificate = false;
			$settings = get_option( 'edge_caching_and_firewall_with_bunnycdn_settings' );

			if ( is_serialized( $settings ) ) {
				$settings = unserialize( $settings );
			}

			$bunnycdn_api_key = isset( $settings['bunnycdn_api_key'] ) ? $settings['bunnycdn_api_key'] : false;
			$bunnycdn_pullzone_id = isset( $settings['bunnycdn_pullzone_id'] ) ? $settings['bunnycdn_pullzone_id'] : false;


			$response = wp_remote_get( 'https://bunnycdn.com/api/pullzone/' . $bunnycdn_pullzone_id, array(
			'timeout' => 20,
			'redirection' => 0,
			'headers' => array( "AccessKey" => $bunnycdn_api_key, "Content-type" => "application/json", "Accept" => "application/json" ),
			));

			$response = wp_remote_retrieve_body( $response );
			$response = json_decode( $response, true );
			$certificates = 0;

			if ( isset( $response['Hostnames'] ) ) {
				foreach ( $response['Hostnames'] as $host ) {
					if ( $host['HasCertificate'] ) {
						$certificates++;
					}
				}

				if ( (int) $certificates === 3 ) {
				$bunnycdn_has_ssl_certificate = true;
				}
			}
			return $bunnycdn_has_ssl_certificate;
		}
	}

	// Encourage switching www if non-www is detected
	function edge_caching_and_firewall_with_bunnycdn_non_www_to_www_notice() {

		$settings = edge_caching_and_firewall_with_bunnycdn_get_settings();
		$bunnycdn_pullzone_id = isset( $settings['bunnycdn_pullzone_id'] ) ? $settings['bunnycdn_pullzone_id'] : false;

		if ( is_admin() && edge_caching_and_firewall_with_bunnycdn_is_plugin_setting_page( 'edge_caching_and_firewall_with_bunnycdn' ) ) {
			if ( ! $bunnycdn_pullzone_id
			&& false === strpos( edge_caching_and_firewall_with_bunnycdn_get_http_host(), 'www' )
			&& 1 === substr_count( edge_caching_and_firewall_with_bunnycdn_get_http_host(), '.' ) ) {
				$message = __( 'For better performance, consider switching from non-www to www <a id="site_version_switching" href="#">Switch now</a>', 'edge_caching_and_firewall_with_bunnycdn' );
				if ( is_admin() ) {
					add_settings_error( 'edge_caching_and_firewall_with_bunnycdn_settings', 200, $message, 'info' );
				}
			}
		}
	}

	add_action( 'admin_notices', 'edge_caching_and_firewall_with_bunnycdn_non_www_to_www_notice' );


	// Notice for invalid API key
	function edge_caching_and_firewall_with_bunnycdn_invalid_api_key_notice() {
	    if ( edge_caching_and_firewall_with_bunnycdn_is_plugin_setting_page() ) {
			$settings = get_option( 'edge_caching_and_firewall_with_bunnycdn_settings' );
			if ( is_serialized( $settings) ) {
				$settings = unserialize( $settings );
			}
		
			if ( isset( $settings['bunnycdn_api_key']) && ! empty( $settings['bunnycdn_api_key']) && ! edge_caching_and_firewall_with_bunnycdn_is_valid_api_key( $settings['bunnycdn_api_key'] ) ) {
			    add_settings_error( 'edge_caching_and_firewall_with_bunnycdn_settings', 200, __( 'Invalid or Expired BunnyCDN Key!', 'edge_caching_and_firewall_with_bunnycdn' ), 'error' );
			}

		}
	}

	add_action( 'admin_notices', 'edge_caching_and_firewall_with_bunnycdn_invalid_api_key_notice' );

	// Avoid Flexible SSL
	function edge_caching_and_firewall_with_bunnycdn_flexible_ssl() {
		if ( isset( $_SERVER['HTTP_CF_VISITOR'] ) && isset( $_SERVER['SERVER_PORT'] ) ) {
			$visitor = json_decode( str_replace( '\\', '', $_SERVER['HTTP_CF_VISITOR'] ) );

			if ( $_SERVER['SERVER_PORT'] === '80' && $visitor->scheme === 'https' ) {
				$message = __( 'Flexible SSL Found! It is strongly recommended to have valid TLS cert at hosting signed by a trusted CA.', 'edge_caching_and_firewall_with_bunnycdn' );
				if ( is_admin() ) {
					add_settings_error( 'edge_caching_and_firewall_with_bunnycdn_settings', 200, $message, 'error' );
				}
			}
		}
	}

	add_action( 'admin_notices', 'edge_caching_and_firewall_with_bunnycdn_flexible_ssl' );
	 	

	add_action( 'updated_option', function( $option_name, $old_value, $value ) {

		if ( $option_name == 'edge_caching_and_firewall_with_bunnycdn_settings' ) {
		    $settings = get_option( 'edge_caching_and_firewall_with_bunnycdn_settings' );
		    $bunnycdn_api_key = isset( $settings['bunnycdn_api_key'] ) ? $settings['bunnycdn_api_key'] : false;
		    $site_version = isset( $settings['site_version'] ) ? $settings['site_version'] : "";
		    if(is_serialized($value)) {
			$value=unserialize($value);
			}
			$str = $value['bunnycdn_api_key']; 
			$pattern = '/[a-f0-9-+()]{36,72}$/';
			if(preg_match($pattern, $str)==0) {
				if ( is_admin() ) {
					$message = __( 'Invalid API Key format', 'edge_caching_and_firewall_with_bunnycdn' );
					add_settings_error( 'edge_caching_and_firewall_with_bunnycdn_settings', 200, $message, 'error' );
					update_option('edge_caching_and_firewall_with_bunnycdn_settings',$old_value);
				}
			}
		    if ( 'www' === $site_version ) {
				if ( is_admin() ) {

					$site_url = get_option( 'siteurl' );
					$parse_url = parse_url( $site_url );
					$www_site_version = $site_url;

					if ( false === strpos( edge_caching_and_firewall_with_bunnycdn_get_http_host(), 'www.' ) ) {
						$path = isset( $parse_url['path'] ) ? $parse_url['path'] : '';
						$www_site_version = edge_caching_and_firewall_with_bunnycdn_get_request_scheme() . '://www.' . edge_caching_and_firewall_with_bunnycdn_get_http_host() . $path;
						update_option( 'siteurl', $www_site_version );
						update_option( 'home', $www_site_version );

						delete_option( 'edge_caching_and_firewall_with_bunnycdn_settings' );
						$edge_caching_and_firewall_with_bunnycdn_settings = array( 'bunnycdn_api_key'  => $bunnycdn_api_key );
						add_action( 'edge_caching_and_firewall_with_bunnycdn_settings', $edge_caching_and_firewall_with_bunnycdn_settings );

						header( 'Location: ' . $www_site_version . '/wp-admin/options-general.php?page=edge_caching_and_firewall_with_bunnycdn' );
						exit();
					}
				}
		    }


	// Display success message if Pull Zone configured successfully.
		    if ( edge_caching_and_firewall_with_bunnycdn_is_valid_api_key( $bunnycdn_api_key ) ) {
				if ( edge_caching_and_firewall_with_bunnycdn_run_bunnycdn_setup() ) {
					$message = __( 'BunnyCDN Pullzone is successfully configured.', 'edge_caching_and_firewall_with_bunnycdn' );
					if ( is_admin() ) {
						add_settings_error( 'edge_caching_and_firewall_with_bunnycdn_settings', 200, $message, 'success' );
					}
					return true;
				}
				return false;
		    }
		}

	}, 10, 3);


	add_action( 'added_option', function( $option_name, $option_value ) {

		if ( $option_name === 'edge_caching_and_firewall_with_bunnycdn_settings' ) {
		    $settings = edge_caching_and_firewall_with_bunnycdn_get_settings();
		    $bunnycdn_api_key = $settings['bunnycdn_api_key'];
		    $site_version = $settings['site_version'];
			
			//API Format check
			if( is_serialized( $option_value ) ) {
			$option_value = unserialize( $option_value );
			}
			$str = $option_value['bunnycdn_api_key']; 
			$pattern = '/[a-f0-9-+()]{36,72}$/';
			if( 0 === preg_match( $pattern, $str ) ) {
				if ( is_admin() ) {
					$message = __( 'Invalid API Key format', 'edge_caching_and_firewall_with_bunnycdn' );
					add_settings_error( 'edge_caching_and_firewall_with_bunnycdn_settings', 200, $message, 'error' );
					delete_option( 'edge_caching_and_firewall_with_bunnycdn_settings' );
				}
			}
			
		    if ( 'www' === $site_version ) {
				if ( is_admin() ) {
					$site_url = get_option( 'siteurl' );
					$parse_url = parse_url( $site_url );
					$www_site_version = $site_url;

					if ( false === strpos( edge_caching_and_firewall_with_bunnycdn_get_http_host(), 'www.' ) ) {

						$path = isset( $parse_url['path'] ) ? $parse_url['path'] : '';
						$www_site_version = edge_caching_and_firewall_with_bunnycdn_get_request_scheme() . '://www.' . edge_caching_and_firewall_with_bunnycdn_get_http_host() . $path;
						update_option( 'siteurl', $www_site_version );
						update_option( 'home', $www_site_version );

						delete_option( 'edge_caching_and_firewall_with_bunnycdn_settings' );
						$edge_caching_and_firewall_with_bunnycdn_settings = array( 'bunnycdn_api_key'  => $bunnycdn_api_key );
						add_action( 'edge_caching_and_firewall_with_bunnycdn_settings', $edge_caching_and_firewall_with_bunnycdn_settings );

						header( 'Location: ' . $www_site_version . '/wp-admin/options-general.php?page=edge_caching_and_firewall_with_bunnycdn' );
						exit();
					}
				}
		    }

		    if ( edge_caching_and_firewall_with_bunnycdn_is_valid_api_key( $bunnycdn_api_key ) ) {
				if ( edge_caching_and_firewall_with_bunnycdn_run_bunnycdn_setup() ) {
					$message = __( 'BunnyCDN Pullzone is successfully configured.', 'edge_caching_and_firewall_with_bunnycdn' );
					if ( is_admin() ) {
						add_settings_error( 'edge_caching_and_firewall_with_bunnycdn_settings', 200, $message, 'success' );
					}
				}
		    }
		}
	}, 5, 3);


	function get_wp_post_permalink( $post = 0, $leavename = false ) {
		$rewritecode = array(
			'%year%',
			'%monthnum%',
			'%day%',
			'%hour%',
			'%minute%',
			'%second%',
			$leavename ? '' : '%postname%',
			'%post_id%',
			'%category%',
			'%author%',
			$leavename ? '' : '%pagename%',
		);

		if ( is_object( $post ) && isset( $post->filter ) && 'sample' === $post->filter ) {
			$sample = true;
		} else {
			$post   = get_post( $post );
			$sample = false;
		}

		if ( empty( $post->ID ) ) {
			return false;
		}

		if ( 'page' === $post->post_type ) {
			return get_page_link( $post, $leavename, $sample );
		} elseif ( 'attachment' === $post->post_type ) {
			return get_attachment_link( $post, $leavename );
		} elseif ( in_array( $post->post_type, get_post_types( array( '_builtin' => false ) ), true ) ) {
			return get_post_permalink( $post, $leavename, $sample );
		}

		$permalink = get_option( 'permalink_structure' );
		$permalink = apply_filters( 'pre_post_link', $permalink, $post, $leavename );

		if ( $permalink ) {

			$category = '';
			if ( strpos( $permalink, '%category%' ) !== false ) {
				$cats = get_the_category( $post->ID );
				if ( $cats ) {
					$cats = wp_list_sort(
						$cats,
						array(
							'term_id' => 'ASC',
						)
					);

					$category_object = apply_filters( 'post_link_category', $cats[0], $cats, $post );

					$category_object = get_term( $category_object, 'category' );
					$category        = $category_object->slug;
					if ( $category_object->parent ) {
						$category = get_category_parents( $category_object->parent, false, '/', true ) . $category;
					}
				}

				if ( empty( $category ) ) {
					$default_category = get_term( get_option( 'default_category' ), 'category' );
					if ( $default_category && ! is_wp_error( $default_category ) ) {
						$category = $default_category->slug;
					}
				}
			}

			$author = '';
			if ( strpos( $permalink, '%author%' ) !== false ) {
				$authordata = get_userdata( $post->post_author );
				$author     = $authordata->user_nicename;
			}

			$date = explode( ' ', str_replace( array( '-', ':' ), ' ', $post->post_date ) );

			$rewritereplace = array(
				$date[0],
				$date[1],
				$date[2],
				$date[3],
				$date[4],
				$date[5],
				$post->post_name,
				$post->ID,
				$category,
				$author,
				$post->post_name,
			);

			$permalink = home_url( str_replace( $rewritecode, $rewritereplace, $permalink ) );
			$permalink = user_trailingslashit( $permalink, 'single' );

		}
		return apply_filters( 'post_link', $permalink, $post, $leavename );
	}

	add_action( 'save_post', 'edge_caching_and_firewall_with_bunnycdn_purge_on_post', 10, 3 );
	function edge_caching_and_firewall_with_bunnycdn_purge_on_post( $post_id, $post, $update ) {
		if (  0 !== edge_caching_and_firewall_with_bunnycdn_get_bunnycdn_pullzone_id() && $update && $post->post_status != 'auto-draft' ) {

			$settings = get_option( 'edge_caching_and_firewall_with_bunnycdn_settings' );
			$siteurl = get_option( 'siteurl' );

			$feedurl = $siteurl . '/feed/';
			$commentfeedurl = $siteurl . '/comments/feed/';
			$blog_id = get_option( 'page_for_posts' );
			$author_url = get_author_posts_url( $post->post_author );

			if ( $blog_id > 0) {
				$siteurl = get_the_permalink( $blog_id );
			}

			if ( is_serialized( $settings ) ) {
				$settings = unserialize( $settings );
			}

			$amp_url = false;
			if ( function_exists( 'is_amp_endpoint' )) {
				$amp_url = amp_get_permalink( $post_id );
			}

			$account_key = $settings['bunnycdn_api_key'];
			$cdn = new bunnycdn_api();

			$cdn->Account( $account_key )->PurgeCache( $siteurl );
			$cdn->Account( $account_key )->PurgeCache( str_replace( '__trashed', '', get_wp_post_permalink( $post_id )) );

			if ( $amp_url ) {
				$cdn->Account( $account_key )->PurgeCache( $amp_url );
			}

			$category_base = '/category/';
			if ( get_option( 'category_base' ) ) {
				$category_base = '/' . get_option( 'category_base' ) . '/';
			}

			$tag_base = '/tag/';
			if ( get_option( 'tag_base' ) ) {
				$tag_base = '/' . get_option( 'tag_base' ) . '/';
			}

			$categories = get_the_category( $post_id );

			if ( $categories ) {
				foreach ( $categories as $category ) {
					$category_url = $siteurl . $category_base . $category->slug . "/";
					$cdn->Account( $account_key )->PurgeCache( $category_url );
				}
			}

			$tags = get_the_tags($post_id);

			if ( $tags ) {
				foreach ( $tags as $tag ) {
					$tag_url = $siteurl . $tag_base . $tag->slug . "/";
					$cdn->Account( $account_key )->PurgeCache( $tag_url );
				}
			}


			$cdn->Account( $account_key )->PurgeCache( $feedurl );
			$cdn->Account( $account_key )->PurgeCache( $commentfeedurl );

			$cdn->Account( $account_key )->PurgeCache( $author_url );
			$cdn->Account( $account_key )->PurgeCache( get_permalink( $post_id . "feed" ) );
			$cdn->Account( $account_key )->PurgeCache( get_permalink( $post_id . "comments/feed/" ) );


		}
	}

	add_action( 'comment_post', 'edge_caching_and_firewall_with_bunnycdn_purge_on_comment' );
	function edge_caching_and_firewall_with_bunnycdn_purge_on_comment( $comment_id ) {

		$comment = get_comments()[0];
		$post_url = get_permalink( $comment->comment_post_ID );

		$settings = edge_caching_and_firewall_with_bunnycdn_get_settings();
		$account_key = $settings['bunnycdn_api_key'];
		$cdn = new bunnycdn_api();

		if ( $account_key && 0 !== edge_caching_and_firewall_with_bunnycdn_get_bunnycdn_pullzone_id() ) {
			$cdn->Account( $account_key )->PurgeCache( $post_url );
		}
	}

	add_action( 'transition_comment_status', 'edge_caching_and_firewall_with_bunnycdn_purge_on_comment_status_update', 10, 3 );
	function edge_caching_and_firewall_with_bunnycdn_purge_on_comment_status_update( $new_status, $old_status, $comment ) {
		if( $old_status != $new_status ) {

			if( $new_status != 'spam' ) {

				$post_id = $comment->comment_post_ID;
				$post_url = get_permalink( $post_id );

				$feed =  $post_url . 'feed';
				$commentfeedurl = $post_url . 'comments/feed/';

				$settings = edge_caching_and_firewall_with_bunnycdn_get_settings();
				$account_key = $settings['bunnycdn_api_key'];
				$pullzone_id = $settings['bunnycdn_pullzone_id'];
				$cdn = new bunnycdn_api();

				if ( $account_key &&  0 !== edge_caching_and_firewall_with_bunnycdn_get_bunnycdn_pullzone_id() ) {
					$cdn->Account( $account_key )->PurgeCache( '', $pullzone_id );
					$cdn->Account( $account_key )->PurgeCache( $feed );
					$cdn->Account( $account_key )->PurgeCache( $commentfeedurl );
				}

			}
		}
	}
	
	if (  0 !== edge_caching_and_firewall_with_bunnycdn_get_bunnycdn_pullzone_id() ) {
  	// Similarly how WP Rocket does https://github.com/wp-media/wp-rocket/blob/trunk/inc/common/purge.php#L5#L16
	if ( class_exists( 'autoptimizeCache' ) ) {
		add_action( 'autoptimize_action_cachepurged', 'edge_caching_and_firewall_with_bunnycdn_purge_everthing' );
	}
	add_action( 'switch_theme', 'edge_caching_and_firewall_with_bunnycdn_purge_everthing' );  // When user change theme.
	add_action( 'wp_update_nav_menu', 'edge_caching_and_firewall_with_bunnycdn_purge_everthing' );  // When a custom menu is update.
	add_action( 'update_option_sidebars_widgets', 'edge_caching_and_firewall_with_bunnycdn_purge_everthing' );  // When you change the order of widgets.
	add_action( 'update_option_category_base', 'edge_caching_and_firewall_with_bunnycdn_purge_everthing' );  // When category permalink is updated.
	add_action( 'update_option_tag_base', 'edge_caching_and_firewall_with_bunnycdn_purge_everthing' );  // When tag permalink is updated.
	add_action( 'permalink_structure_changed', 'edge_caching_and_firewall_with_bunnycdn_purge_everthing' );  // When permalink structure is update.
	add_action( 'add_link', 'edge_caching_and_firewall_with_bunnycdn_purge_everthing' );  // When a link is added.
	add_action( 'edit_link', 'edge_caching_and_firewall_with_bunnycdn_purge_everthing' );  // When a link is updated.
	add_action( 'delete_link', 'edge_caching_and_firewall_with_bunnycdn_purge_everthing' );  // When a link is deleted.
	add_action( 'customize_save', 'edge_caching_and_firewall_with_bunnycdn_purge_everthing' );  // When customizer is saved.
	add_action( 'update_option_theme_mods_' . get_option( 'stylesheet' ), 'edge_caching_and_firewall_with_bunnycdn_purge_everthing' ); // When any theme modifications are updated
	}

	function edge_caching_and_firewall_with_bunnycdn_purge_everthing() {

		$settings = edge_caching_and_firewall_with_bunnycdn_get_settings();
		$account_key = $settings['bunnycdn_api_key'];
		$pullzone_id = $settings['bunnycdn_pullzone_id'];
		$cdn = new bunnycdn_api();

		if ( $account_key && 0 !== $pullzone_id ) {
			$cdn->Account( $account_key )->PurgeCache( '', $pullzone_id );
		}

		return false;
	}

	/**
		* Prevent bypassing Cloud Firewall
		* Inspired by https://community.cloudflare.com/t/stop-cloudflare-bypassing-on-shared-hosting/91203
		*/

	function edge_caching_and_firewall_with_bunnycdn_require_origin_access_token_from_bunny() {

			$is_active = isset( get_option( 'edge_caching_and_firewall_with_bunnycdn_cloudfirewall_settings' )['bunnycdn_enable_origin_access_token'] ) ? true : false;

			$bypass = defined( 'HTTP_ORIGIN_ACCESS_TOKEN' ) ? HTTP_ORIGIN_ACCESS_TOKEN : false;

			if ( false === $is_active || 0 === edge_caching_and_firewall_with_bunnycdn_get_bunnycdn_pullzone_id() || true === $bypass || current_user_can( 'manage_options' ) ) {
				return 0;
			}

			$key = get_option( 'edge_caching_and_firewall_with_bunnycdn_origin_access_token' );

			if ( ! isset( $_SERVER['HTTP_ORIGIN_ACCESS_TOKEN'] ) || $key !== $_SERVER['HTTP_ORIGIN_ACCESS_TOKEN'] ) {
					http_response_code('417');
					die( '<h1>Expectation Failed</h1>' );
			}
	}
	add_action( 'plugins_loaded', 'edge_caching_and_firewall_with_bunnycdn_require_origin_access_token_from_bunny' );

	// Modify IP
	function edge_caching_and_firewall_with_bunnycdn_modify_remote_ip() {
		if ( isset( $_SERVER['HTTP_X_REAL_IP'] ) ) {
			$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];
		}
	}

	add_action( 'plugins_loaded', 'edge_caching_and_firewall_with_bunnycdn_modify_remote_ip' );

	// Run Setup
	function edge_caching_and_firewall_with_bunnycdn_run_bunnycdn_setup() {

		$continue = true;

		if ( is_admin() ) {

			$new_zone = true;
			$www_host_exist = false;
			$non_www_host_exist = false;

			$site_version = edge_caching_and_firewall_with_bunnycdn_get_site_version();

			$non_www_host = edge_caching_and_firewall_with_bunnycdn_get_host( 'non-www' );
			$www_host = edge_caching_and_firewall_with_bunnycdn_get_host( 'www' );

			$settings = edge_caching_and_firewall_with_bunnycdn_get_settings();
			$ip = edge_caching_and_firewall_with_bunnycdn_get_server_address();

			$zone_data = '';
			$zone_name = edge_caching_and_firewall_with_bunnycdn_get_zone_name();

			if ( edge_caching_and_firewall_with_bunnycdn_is_valid_api_key( $settings['bunnycdn_api_key'] ) ) {

				// Zone Getting/Creating

				$cdn = new bunnycdn_api();
				$response = $cdn->Account( $settings['bunnycdn_api_key'] )->GetZoneList();

				if ( 'success' === $response['status'] ) {

					$zones = $response['zone_smry'];
					if ( edge_caching_and_firewall_with_bunnycdn_does_host_exist( $zones ) ) {

						$continue = false;

					}

					if ( $continue ) {

						foreach( $zones as $zone ) {

							if ( $zone['zone_name'] === $zone_name ) {

								$zone_data = $zone;
								$new_zone = false;

								break;

							}

						}

					}


					if ( $continue && $new_zone ) {

						$response = $cdn->Account( $settings['bunnycdn_api_key'] )->CreateNewZone( $zone_name, edge_caching_and_firewall_with_bunnycdn_get_request_scheme()  . '://' .  $ip );

						if ( 'success' === $response['status'] ) {

							$zone_data = $response;

						} else {

							$message = 'Oops! something went wrong, we are unable to create new pullzone!';
							if ( is_admin() ) {
								add_settings_error( 'edge_caching_and_firewall_with_bunnycdn_settings', 200, $message, 'error' );
							}

							$continue = false;

						}

					}

				}



				if ( isset( $zone_data['host_names'] ) ) {

					foreach ( $zone_data['host_names'] as $host ) {

						if ( $non_www_host === $host ) {
							$non_www_host_exist = true;
						}

						if ( $host == $www_host ) {
							$www_host_exist = true;
						}

					}

				}

				// Hosts Settings
				if ( $continue ) {

					if ( ! $www_host_exist ) {

						$host_name_url = $www_host;
						$add_host = $cdn->Account( $settings['bunnycdn_api_key'] )->AddHostName( $zone_data['zone_id'], $host_name_url );

						if ( 'success' === $add_host['status'] ) {

							$www_host_setup = true;

						} else {

							$message = 'Oops! something went wrong, we are unable add ' . $host_name_url . ' as a host!';
							if ( is_admin() ) {
								add_settings_error( 'edge_caching_and_firewall_with_bunnycdn_settings', 200, $message, 'error' );
							}

							$continue = false;
						}
					}


					if ( ! $non_www_host_exist ) {
						$host_name_url = $non_www_host;
						$add_host = $cdn->Account( $settings['bunnycdn_api_key'] )->AddHostName( $zone_data['zone_id'], $host_name_url );

						if ( 'success' === $add_host['status'] ) {

							$non_www_host_setup = true;

						} else {

							$message = 'Oops! something went wrong, we are unable add ' . $host_name_url . ' as a host!';
							if ( is_admin() ) {
								add_settings_error( 'edge_caching_and_firewall_with_bunnycdn_settings', 200, $message, 'error' );
							}

							$continue = false;

						}
					}
			}

				if ( $continue && $zone_data['zone_id'] ) {
					$settings = edge_caching_and_firewall_with_bunnycdn_update_setting( $zone_data['zone_id'], 'bunnycdn_pullzone_id' );
				}

				if ( $continue ) {

					// Zone Settings
					$bunnycdn_webp_image_delivery = isset( $settings['bunnycdn_webp_image_delivery'] ) ? $settings['bunnycdn_webp_image_delivery'] : 0;

					$request_parameters  = [
						"DisableCookies" => false,
						"CacheErrorResponses" => true,
						"CacheControlMaxAgeOverride" => 0,
						"CacheControlBrowserMaxAgeOverride" => 0,
						"EnableQueryStringOrdering" => true,
						"EnableWebpVary" => ( $bunnycdn_webp_image_delivery === '2' ) ? true : false
					];

					$response = $cdn->UpdateZone($zone_data['zone_id'], $request_parameters);

					if ( 'success' !== $response['status'] ) {

						$message = "Oops! something went wrong, we are unable to properly configure zone '" . $zone_data['zone_name'] . "'!";
						if ( is_admin() ) {
							add_settings_error( 'edge_caching_and_firewall_with_bunnycdn_settings', 200, $message, "error" );
						}

						$continue = false;

					}

					if ( $continue ) {

						$vary_cache_request_parameters = [
							'PullZoneId' 					=> $zone_data['zone_id'],
							'QueryStringVaryEnabled'  		=> true,
							'RequestHostnameVaryEnabled'  	=> false,
							'UserCountryCodeVaryEnabled' 	=> false,
							'WebpVaryEnabled'				=> ( $bunnycdn_webp_image_delivery === '2' ) ? true : false
						];

						$response = $cdn->SetVaryCache( $vary_cache_request_parameters );

						if ( 'success' !== $response['status'] ) {

							$message = "Oops! something went wrong, we are unable to properly configure Vary Cache for zone '" . $zone_data['zone_name'] . "'!";
							if ( is_admin() ) {
								add_settings_error( 'edge_caching_and_firewall_with_bunnycdn_settings', 200, $message, "error" );
							}

							$continue = false;

						}
					}


					if ( $continue & $new_zone ) {

						$request_parameters = [];
						$site_url = get_option( 'siteurl' );
						$parse_url = parse_url( $site_url );
						$path = isset( $parse_url['path'] ) ? $parse_url['path'] : "";

						// Zone EdgeRule
						$request_parameters[]  = [
							"ActionParameter1"		=>	"host",
							"ActionParameter2"		=>	edge_caching_and_firewall_with_bunnycdn_get_http_host(),
							"Description"			=>	"Set Host",
							"Enabled"				=> 	true,
							"ActionType"			=>	6,
							"TriggerMatchingType"	=> 	0,
							"Triggers" 				=> [
								[
									"Type"					=>	0,
									"PatternMatchingType"	=>	0,
									"Parameter1"			=>	"",
									"PatternMatches"		=>	["*"]
								]
							]
						];

						// Zone EdgeRule - Canonical

						$subdomain = substr_count( edge_caching_and_firewall_with_bunnycdn_get_http_host(), '.' ) > 1 ? true : false;
						$domain = $site_version == 'www' ? $non_www_host : $www_host;

						$request_parameters[]  = [
							"ActionParameter1"		=>	edge_caching_and_firewall_with_bunnycdn_get_request_scheme() . "://" . edge_caching_and_firewall_with_bunnycdn_get_http_host() . "{{path}}",
							"ActionParameter2"		=>	"",
							"Description"			=>	"Canonical",
							"Enabled"				=> 	true,
							"ActionType"			=>	1,
							"TriggerMatchingType"	=> 	0,
							"Triggers" 				=> [
								[
									"Type"					=>	0,
									"PatternMatchingType"	=>	0,
									"Parameter1"			=>	"",
									"PatternMatches"		=>	[
										"*://" . $zone_name . ".b-cdn.net/*",
										"*://" . $domain . "/*",
										edge_caching_and_firewall_with_bunnycdn_reverse_scheme() . edge_caching_and_firewall_with_bunnycdn_get_http_host() . "/*",
									]
								]
							]
						];

						// Zone EdgeRule - Always Cache Static Files even if user is logged in
						$request_parameters[]  = [
							"ActionParameter1"		=>	"2592000",
							"Description"			=>	"Always Cache Static Files",
							"Enabled"				=> 	true,
							"ActionType"			=>	3,
							"TriggerMatchingType"	=> 	0,
							"Triggers" 				=> [
								[
									"Type"					=>	3,
									"PatternMatchingType"	=>	0,
									"PatternMatches"		=>	[ "css", "js", "svg", "png", "jp*g" ]
								],
								[
									"Type"					=>	0,
									"PatternMatchingType"	=>	0,
									"PatternMatches"		=>	[ "woff*","ico", "webp", "gif", "mp4" ]
								],
								[
									"Type"					=>	0,
									"PatternMatchingType"	=>	0,
									"PatternMatches"		=>	[ "*/wp-admin/load-styles.php*", "*/wp-admin/load-scripts.php*" ]
								]
							]
						];

						// Zone EdgeRule - Bypass Cache
						$request_parameters[]  = [
							"ActionParameter1"		=>	"2592000",
							"ActionParameter2"		=>	"",
							"Description"			=>	"Bypass Cache",
							"Enabled"				=> 	true,
							"ActionType"			=>	3,
							"TriggerMatchingType"	=> 	2,
							"Triggers" 				=> [
								[
									"Type"					=>	0,
									"PatternMatchingType"	=>	0,
									"Parameter1"			=>	"",
									"PatternMatches"		=>	[
										"*/wp-admin/*",
										"*/wp-json*",
										"*.php*",
										"*.xml",
										"*/page/*"
									]
								],


								 /*
								  * List of cookies purposefully ignored for Cache Bypass.
								  *
								  * wordpress_test_cookie = To serve cached response to 'logged out' user in same browser window.
								  * comment_author cookie =	No need as 'show_comments_cookies_opt_in' is off due to perf and privacy reasons.
								  *
								 */


								[
									"Type"					=>	1,
									"PatternMatchingType"	=>	0,
									"Parameter1"			=>	"cookie",
									"PatternMatches"		=>	[
										"*wp-postpass*",
										"*wordpress_logged_in*",
										"*woocommerce_cart_hash*",
										"*edd_items_in_cart*"
									]
								],
								[
									"Type"					=>	6,
									"PatternMatchingType"	=>	0,
									"Parameter1"			=>	"",
									"PatternMatches"		=>	[
										"s=*",
										"unapproved*"
									]
								]
							]
						];

						// Zone EdgeRule - Ignore Query Strings
						$request_parameters[]  = [
							"ActionParameter1"		=>	"",
							"ActionParameter2"		=>	"",
							"Description"			=>	"Ignore Query Strings",
							"Enabled"				=> 	true,
							"ActionType"			=>	11,
							"TriggerMatchingType"	=> 	0,
							"Triggers" 				=> [
								[
									"Type"					=>	6,
									"Parameter1"			=> "",
									"PatternMatchingType"	=>	0,
									"PatternMatches"		=>	["*fbclid=*", "*utm_*", "*cn-reloaded*", "*ao_noptimize*", "*ref=*"]
								]
							]
						];

						// Zone EdgeRule - Set Origin Access Token
						$request_parameters[]  = [
							"ActionParameter1"		=>	"origin-access-token",
							"ActionParameter2"		=>	get_option( 'edge_caching_and_firewall_with_bunnycdn_origin_access_token' ),
							"Description"			=>	"Set Origin Access Token",
							"Enabled"				=> 	true,
							"ActionType"			=>	6,
							"TriggerMatchingType"	=> 	0,
							"Triggers" 				=> [
								[
									"Type"					=>	0,
									"Parameter1"			=> "",
									"PatternMatchingType"	=>	0,
									"PatternMatches"		=>	["*"]
								]
							]
						];

						// Zone EdgeRule - Browser Cache
						$request_parameters[]  = [
							"ActionParameter1"		=>	"Cache-Control",
							"ActionParameter2"		=>	"public, max-age=31536000, immutable",
							"Description"			=>	"Browser Cache",
							"Enabled"				=> 	true,
							"ActionType"			=>	5,
							"TriggerMatchingType"	=> 	0,
							"Triggers" 				=> [
								[
									"Type"					=>	3,
									"PatternMatchingType"	=>	0,
									"Parameter1"			=>	"",
									"PatternMatches"		=>	[
										"png",
										"css",
										"js",
										"jp*g",
										"woff*"
									]
								],
								[
									"Type"					=>	3,
									"PatternMatchingType"	=>	0,
									"Parameter1"			=>	"cookie",
									"PatternMatches"		=>	[
										"webp",
										"svg",
										"ttf"
									]
								],
								[
									"Type"					=>	0,
									"PatternMatchingType"	=>	0,
									"PatternMatches"		=>	[ "*/wp-admin/load-styles.php*", "*/wp-admin/load-scripts.php*" ]
								]
							]
						];


						foreach ( $request_parameters as $key => $request_parameter ) {

							$response = $cdn->AddEdgeRule( $zone_data['zone_id'], $request_parameter );

						}
					}

					$request_parameters = [
						"OptimizerAutomaticOptimizationEnabled"		=>	false,
						"OptimizerDesktopMaxWidth"					=>	"1600",
						"OptimizerEnableManipulationEngine"			=>	false,
						"OptimizerEnableWebP"						=>	false,
						"OptimizerEnabled"							=>	false,
						"OptimizerImageQuality"						=>	"85",
						"OptimizerMinifyCSS"						=>	false,
						"OptimizerMinifyJavaScript"					=>	false,
						"OptimizerMobileImageQuality"				=>	"70",
						"OptimizerMobileMaxWidth"					=>	"800",
						"OptimizerWatermarkEnabled"					=>	false,
						"OptimizerWatermarkMinImageSize"			=>	"300",
						"OptimizerWatermarkOffset"					=>	"3",
						"OptimizerWatermarkPosition"				=>	"0",
						"OptimizerWatermarkUrl"						=>	"",
						"PullZoneId"								=>	$zone_data['zone_id']
					];

					// BunnyCDN Optimizer
					if ($continue && $bunnycdn_webp_image_delivery == 3) {
						$request_parameters = [
							"OptimizerAutomaticOptimizationEnabled"		=>	true,
							"OptimizerDesktopMaxWidth"					=>	"1600",
							"OptimizerEnableManipulationEngine"			=>	true,
							"OptimizerEnableWebP"						=>	true,
							"OptimizerEnabled"							=>	true,
							"OptimizerImageQuality"						=>	"85",
							"OptimizerMinifyCSS"						=>	true,
							"OptimizerMinifyJavaScript"					=>	true,
							"OptimizerMobileImageQuality"				=>	"70",
							"OptimizerMobileMaxWidth"					=>	"800",
							"OptimizerWatermarkEnabled"					=>	true,
							"OptimizerWatermarkMinImageSize"			=>	"300",
							"OptimizerWatermarkOffset"					=>	"3",
							"OptimizerWatermarkPosition"				=>	"0",
							"OptimizerWatermarkUrl"						=>	"",
							"PullZoneId"								=>	$zone_data['zone_id']
						];
					}

					$response = $cdn->SetOptimizerConfiguration( $request_parameters );

					if ( "success" !== $response['status'] ) {

						$message = "Oops! something went wrong, we are unable to properly configure optimizer for the '" . $zone_data['zone_name'] . "' zone!";
						if ( is_admin() ) {
							add_settings_error( 'edge_caching_and_firewall_with_bunnycdn_settings', 200, $message, "error" );
						}

						$continue = false;

					}

				}

			} else {
				$continue = false;
			}

		}

		return $continue;
	}

	// DNS updater - Recommend CF NS
	function edge_caching_and_firewall_with_bunnycdn_dns_update_info() {
		$host = edge_caching_and_firewall_with_bunnycdn_get_http_host();

		$even = '<ul><li> <span class="dashicons dashicons-info" style="color:#312fc6;"></span> '
			.  __( 'First off, please update records as below at', 'edge_caching_and_firewall_with_bunnycdn')
			.  ' <a title="Visit Cloudflare DNS" href="https://dash.cloudflare.com/?to=/:account/:zone/dns" target="_blank" rel="noreferrer noopener">Cloudflare DNS</a>.
			</li></ul>';

		$odd = '<ul><li><span style="color:#cc3300;" class="dashicons dashicons-warning"></span> '
							. $host .  __( ' is not using Cloudflare nameservers. ', 'edge_caching_and_firewall_with_bunnycdn' )
									. '<a href="https://www.gulshankumar.net/using-cloudflare-dns-without-cdn-or-waf/" rel="noopener noreferrer" target="_blank">'
										. __( 'Follow this article.', 'edge_caching_and_firewall_with_bunnycdn' )
											. '</a></li>
											<li>
												<span class="dashicons dashicons-clock" style="color: #ff9800;">
														</span> ' . __( 'Wait 72 hours for the nameservers propagation.', 'edge_caching_and_firewall_with_bunnycdn' ) . '</li></ul>';


			$get = wp_safe_remote_get( "https://cloudflare-dns.com/dns-query?name=$host&type=NS", array(
	    			'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36',
	    			'redirection' => 0,
	    			'headers' => array( 'Accept' => 'application/dns-json' ),
	    			));

		// @todo: expand this
		echo 200 === wp_remote_retrieve_response_code( $get ) && ( false !== strpos( wp_remote_retrieve_body( $get ), 'cloudflare.com' ) || false !== strpos( wp_remote_retrieve_body( $get ), 'b-cdn.net' ) ) ? $even : $odd;
	}

	// DNS & SSL
	function edge_caching_and_firewall_with_bunnycdn_dns_ssl_html() {

		$settings = edge_caching_and_firewall_with_bunnycdn_get_settings();
		$www_host = edge_caching_and_firewall_with_bunnycdn_get_host( 'www' );
		$non_www_host = edge_caching_and_firewall_with_bunnycdn_get_host( 'non-www' );

		$bunnycdn_pullzone_id = isset( $settings['bunnycdn_pullzone_id'] ) ? $settings['bunnycdn_pullzone_id'] : false;

		if ( $bunnycdn_pullzone_id && ! edge_caching_and_firewall_with_bunnycdn_get_bunnycdn_ssl_certificate() ) {

			echo '<h2>' . __( 'Install Free SSL', 'edge_caching_and_firewall_with_bunnycdn' ) . '</h2>';

			edge_caching_and_firewall_with_bunnycdn_dns_update_info();

			echo'<table id="cloudflare_dns_table" role="presentation">
				     <tbody>
				        <tr>
				           <th scope="row" width="10%">Record Type</th>
				           <th scope="row" width="15%">Name</th>
				           <th scope="row" width="20%">Value</th>
				           <th scope="row" width="10%">TTL</th>
				           <th scope="row" width="10%">Status</th>
				        </tr>
				        <tr>
				           <td>CNAME</td>
				           <td>' . $non_www_host . '</td>
				           <td>' . edge_caching_and_firewall_with_bunnycdn_get_zone_name() . '.b-cdn.net</td>
				           <td>Automatic</td>
				           <td>DNS only</td>
				        </tr>
				        <tr>
				           <td>CNAME</td>
				           <td>' . $www_host . '</td>
				           <td>' . edge_caching_and_firewall_with_bunnycdn_get_zone_name() . '.b-cdn.net</td>
				           <td>Automatic</td>
				           <td>DNS only</td>
				        </tr>
				     </tbody>
				  </table>
				  <p class="submit"><a href="?page=edge_caching_and_firewall_with_bunnycdn&_action=install_free_ssl" id="install_free_ssl" class="button button-primary">' . __( 'Install SSL at Pull Zone', 'edge_caching_and_firewall_with_bunnycdn' ) . '</a></p>
				</form>
			';

		// Display the Final Status
		} elseif ( edge_caching_and_firewall_with_bunnycdn_get_bunnycdn_ssl_certificate() &&  'https' === edge_caching_and_firewall_with_bunnycdn_get_request_scheme() ) {

			$origin = __( '🎉 Setup completed successfully. You may restart your browser to get new DNS changes reflected.', 'edge_caching_and_firewall_with_bunnycdn' );
			$bunny = __( '😍 Bunnyfied successfully.', 'edge_caching_and_firewall_with_bunnycdn' );

			$dns_status = isset( $_SERVER[ 'HTTP_VIA' ] ) ?  $bunny :  $origin;

		echo '<h2>' . __( 'Status', 'edge_caching_and_firewall_with_bunnycdn' ) . '</h2>' . '<p>' . $dns_status . '</p>';
		}
	}

	add_action( 'admin_init', 'edge_caching_and_firewall_with_bunnycdn_install_free_ssl_request' );

	// Request Certs
	function edge_caching_and_firewall_with_bunnycdn_install_free_ssl_request() {

		if ( empty( $_GET['page'] ) || empty( $_GET['_action'] ) || $_GET['page'] !== 'edge_caching_and_firewall_with_bunnycdn' || ( $_GET['_action'] !== 'install_free_ssl' ) ) {
			return false;
		}

		$settings = edge_caching_and_firewall_with_bunnycdn_get_settings();
		$account_key = isset( $settings['bunnycdn_api_key'] ) ? $settings['bunnycdn_api_key'] : false;
		$pullzone_id = isset( $settings['bunnycdn_pullzone_id'] ) ?  $settings['bunnycdn_pullzone_id'] : false;
		$continue = true;

		$cdn = new bunnycdn_api();
		$response = $cdn->Account( $account_key )->LoadFreeCertificate( $pullzone_id, edge_caching_and_firewall_with_bunnycdn_get_host( 'non-www' ) );

		if ( 'success' !== $response['status'] ) {
			$continue = false;
		}

		if ( $continue ) {
			$response = $cdn->Account( $account_key )->LoadFreeCertificate( $pullzone_id, edge_caching_and_firewall_with_bunnycdn_get_host( 'www' ) );
			if ( 'success' !== $response['status'] ) {
				$continue = false;
			}
		}

		if ( ! $continue ) {

			$message = "Oops! something went wrong, we are unable to properly install SSL for the '" . edge_caching_and_firewall_with_bunnycdn_get_zone_name() . "' pullzone!";
			Edge_Caching_and_Firewall_with_BunnyCDN_Flash::set( $message, 'error' );

		} else {

			$message = "SSL is successfully installed for the '" . edge_caching_and_firewall_with_bunnycdn_get_zone_name() . "' pullzone!";
			Edge_Caching_and_Firewall_with_BunnyCDN_Flash::set( $message, 'success' );

		}
	}
