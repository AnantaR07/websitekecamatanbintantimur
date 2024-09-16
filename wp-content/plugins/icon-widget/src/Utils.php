<?php

namespace SeoThemes\IconWidget;

use function basename;
use function dirname;
use function glob;
use function is_null;

/**
 * Class Utils
 *
 * @package SeoThemes\IconWidget
 */
class Utils {

	/**
	 * Returns the main plugin file.
	 *
	 * @since 1.2.0
	 *
	 * @return string
	 */
	public static function get_plugin_file() {
		static $file = null;

		if ( is_null( $file ) ) {
			$file = dirname( __DIR__ ) . '/icon-widget.php';
		}

		return $file;
	}

	/**
	 * Returns an array of font names.
	 *
	 * @since 1.2.0
	 *
	 * @return array
	 */
	public static function get_fonts() {
		static $fonts = null;

		if ( is_null( $fonts ) ) {
			$files = glob( dirname( __DIR__ ) . '/config/*.php' );

			foreach ( $files as $file ) {
				$filename = basename( $file, '.php' );
				$fonts[]  = $filename;
			}
		}

		return $fonts;
	}

	/**
	 * Get default icon set.
	 *
	 * @since 1.4.0
	 *
	 * @return string
	 */
	public static function get_default_icon_font() {
		return 'font-awesome-5';
	}
}
