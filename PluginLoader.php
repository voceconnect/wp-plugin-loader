<?php

namespace Voce\PluginLoader;

class PluginLoader {

	const LOAD_ACTION = 'wp_load_plugin';

	private $vendor_directories;

	public function __construct() {
		$this->vendor_directories = [ ];
	}

	public function registerHooks() {
		if ( defined( 'WP_PLUGIN_DIR' ) && !has_action( self::LOAD_ACTION ) ) {
			$this->vendor_directories[] = trailingslashit( WP_PLUGIN_DIR );
		}

		add_action( self::LOAD_ACTION, [$this, 'loadPlugin' ], 10, 2 );
		add_filter( 'plugins_url', [$this, 'filterPluginsURL' ], 10, 3 );
	}

	public function registerVendorDirectory( $directory ) {
		$this->vendor_directories[] = trailingslashit( $directory );
		return $this;
	}

	public function loadPlugin( $name, $file = false ) {
		if ( !$file ) {
			$file = $name . '.php';
		}
		$main_file = $name . DIRECTORY_SEPARATOR . $file;

		foreach ( $this->vendor_directories as $dir ) {
			if ( file_exists( $dir . $main_file ) ) {
				return include_once( $dir . $main_file );
			}
		}
	}

	public function filterPluginsURL( $url, $path, $plugin ) {
		//core plugin directories are handled by default.
		if ( empty( $plugin ) || strpos( $url, WPMU_PLUGIN_URL ) === 0 || strpos( WP_PLUGIN_DIR, $plugin ) === 0 ) {
			return $url;
		}

		//shortcut the stylesheet and template urls since they may have been rewritten to a CDN
		$stylesheet_dir = get_stylesheet_directory();
		$template_dir = get_template_directory();
		if ( !empty( $plugin ) && is_string( $plugin ) ) {
			if ( strpos( $plugin, $stylesheet_dir ) === 0 ) {
				$folder = str_replace( $stylesheet_dir, '', dirname( $plugin ) );
				$url = get_stylesheet_directory_uri() . '/' . ltrim( $folder, '/' );
				if ( !empty( $path ) && is_string( $path ) && strpos( $path, '..' ) === false ) {
					$url .= '/' . ltrim( $path, '/' );
				}
			} elseif ( strpos( $plugin, $template_dir ) === 0 ) {
				$folder = str_replace( $template_dir, '', dirname( $plugin ) );
				$url = get_template_directory_uri() . '/' . ltrim( $folder, '/' );
				if ( !empty( $path ) && is_string( $path ) && strpos( $path, '..' ) === false ) {
					$url .= '/' . ltrim( $path, '/' );
				}
				return $url;
			} elseif ( strpos( WP_CONTENT_DIR, $plugin ) === 0 ) {
				foreach ( $this->vendor_directories as $vendor_dir ) {
					if ( strpos( $plugin, $vendor_dir ) === 0 ) {
						$folder = str_replace( WP_CONTENT_DIR, '', dirname( $plugin ) );
						$url = WP_CONTENT_URL . '/' . ltrim( $folder, '/' );
						if ( !empty( $path ) && is_string( $path ) && strpos( $path, '..' ) === false ) {
							$url .= '/' . ltrim( $path, '/' );
						}
						break;
					}
				}
			}
		}
		return $url;
	}

}
