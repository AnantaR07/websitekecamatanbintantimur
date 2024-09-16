<?php

namespace SeoThemes\IconWidget;

/**
 * Class Service
 *
 * @package SeoThemes\IconWidget
 */
abstract class Service {

	/**
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * Service constructor.
	 *
	 * @param Plugin $plugin Plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Run hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	abstract public function run();
}
