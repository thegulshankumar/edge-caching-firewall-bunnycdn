<?php
	class Edge_Caching_and_Firewall_with_BunnyCDN_Flash {
		public static function set( $message, $type ) {
			return $_SESSION['flash'] = [
				"message"	=>	$message,
				"type"		=> $type
			];
		}

		public static function get() {
		$flash = array_map('wp_kses_post',$_SESSION['flash']); /* Using _SESSION to print static HTML message. There is no DB query involved. wp_kses_post ensures no script tag is allowed. */
			unset( $_SESSION['flash'] );
			return $flash;
		}


		public static function check() {
			return isset( $_SESSION['flash'] ) ? true : false;
		}
	}
?>