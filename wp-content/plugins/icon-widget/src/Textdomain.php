<?php

namespace SeoThemes\IconWidget;

use function add_action;
use function load_plugin_textdomain;

/**
 * Class Textdomain
 *
 * @package SeoThemes\IconWidget
 */
class Textdomain extends Service {

	/**
	 * Run service hooks.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function run() {
		add_action( 'init', [ $this, 'load_textdomain' ] );
	}

	/**
	 * Loads plugin text domain.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			$this->plugin->handle,
			false,
			$this->plugin->dir . 'languages'
		);
	}
}
