<?php

namespace SeoThemes\IconWidget;

use function is_null;

/**
 * Class Factory
 *
 * @package SeoThemes\IconWidget
 */
final class Factory {

	/**
	 * Factory method to return single instance of plugin.
	 *
	 * @since 1.2.0
	 *
	 * @return object
	 */
	public static function get_instance() {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new Plugin( Utils::get_plugin_file() );
		}

		return $instance;
	}
}
