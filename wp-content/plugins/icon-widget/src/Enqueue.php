<?php

namespace SeoThemes\IconWidget;

/**
 * Enqueue class.
 *
 * @package SeoThemes\IconWidget
 */
class Enqueue extends Service {

	/**
	 * Enqueue constructor.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function run() {
		add_action( 'wp_enqueue_scripts', [ $this, 'load_icon_font' ] );
		add_action( 'admin_print_styles', [ $this, 'load_icon_font' ] );
		add_action( 'admin_print_styles', [ $this, 'load_admin_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_scripts' ] );
	}

	/**
	 * Registers and enqueues admin-specific styles.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function load_admin_styles() {
		if ( ! is_customize_preview() && get_current_screen()->id !== 'widgets' ) {
			return;
		}

		wp_enqueue_style(
			'bootstrap',
			$this->plugin->url . 'assets/css/bootstrap.min.css',
			[ 'wp-color-picker' ]
		);

		wp_enqueue_style(
			'bootstrap-select',
			$this->plugin->url . 'assets/css/bootstrap-select.min.css',
			[ 'bootstrap' ]
		);
	}

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function load_admin_scripts() {
		if ( ! is_customize_preview() && get_current_screen()->id !== 'widgets' ) {
			return;
		}

		wp_enqueue_script(
			'bootstrap',
			$this->plugin->url . 'assets/js/bootstrap.min.js',
			[ 'jquery', 'wp-color-picker' ]
		);

		wp_enqueue_script(
			'bootstrap-select',
			$this->plugin->url . 'assets/js/bootstrap-select.min.js',
			[ 'bootstrap' ]
		);
	}

	/**
	 * Registers and enqueues widget-specific styles.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function load_icon_font() {
		$settings     = get_option( 'icon_widget_settings' );
		$current_font = $settings['font'] ?? Utils::get_default_icon_font();

		foreach ( Utils::get_fonts() as $font ) {
			if ( $font === $current_font ) {
				wp_enqueue_style(
					$font,
					$this->plugin->url . 'assets/css/' . $font . '.min.css'
				);
			}
		}
	}
}
