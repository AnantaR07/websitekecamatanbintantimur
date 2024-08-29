<?php

namespace SeoThemes\IconWidget;

use function add_action;
use function register_widget;

/**
 * Class Hooks
 *
 * @package SeoThemes\IconWidget
 */
class Hooks extends Service {

	/**
	 * Register miscellaneous hooks.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function run() {
		add_action( 'widgets_init', [ $this, 'register_widget' ] );
	}

	/**
	 * Registers icon widget.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function register_widget() {
		register_widget( __NAMESPACE__ . '\Widget' );
	}
}
