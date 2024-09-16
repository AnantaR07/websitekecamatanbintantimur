<?php

namespace SeoThemes\IconWidget;

use Exception;
use function array_key_exists;
use function esc_html__;
use function method_exists;
use function strtolower;

/**
 * Container class.
 *
 * @package SeoThemes\IconWidget
 */
abstract class Container {

	/**
	 * @var array
	 */
	protected $services = [];

	/**
	 * Registers a service.
	 *
	 * @since 1.2.0
	 *
	 * @throws Exception
	 *
	 * @param string $service
	 *
	 * @return void
	 *
	 */
	public function register( $service ) {
		if ( array_key_exists( strtolower( $service ), $this->services ) ) {
			throw new Exception( $service . esc_html__( ' service is already registered.', 'icon-widget' ) );
		} else {
			$this->services[ strtolower( $service ) ] = new $service( $this );
		}
	}

	/**
	 * Runs all services.
	 *
	 * @since 1.2.0
	 *
	 * @throws Exception
	 * @return void
	 *
	 */
	public function run() {
		foreach ( $this->services as $service => $class ) {
			if ( ! method_exists( $class, 'run' ) ) {
				throw new Exception( $service . esc_html__( ' does not have a run method.', 'icon-widget' ) );
			}

			$class->run();
		}
	}
}
