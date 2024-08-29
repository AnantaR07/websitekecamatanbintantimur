<?php
/**
 * Plugin Name:  Icon Widget
 * Plugin URI:   https://wordpress.org/plugins/icon-widget/
 * Description:  Displays an icon widget with a title and description.
 * Author:       SEO Themes
 * Author URI:   https://seothemes.com/
 * Version:      1.4.0
 * Tested up to: 6.5
 * Text Domain:  icon-widget
 * License:      GPL-2.0-or-later
 * License URI:  http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:  /languages
 */

namespace SeoThemes\IconWidget;

use function call_user_func;
use function defined;
use function is_readable;
use function spl_autoload_register;
use function strrchr;
use function substr;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Autoload classes.
spl_autoload_register( function ( $class ) {
	$file = __DIR__ . '/src/' . substr( strrchr( $class, '\\' ), 1 ) . '.php';

	if ( is_readable( $file ) ) {
		require_once $file;
	}
} );

call_user_func( function () {
	$container = Factory::get_instance();

	$container->register( Textdomain::class );
	$container->register( Shortcode::class );
	$container->register( Settings::class );
	$container->register( Enqueue::class );
	$container->register( Hooks::class );

	$container->run();
} );

