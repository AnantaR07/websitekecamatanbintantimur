<?php

namespace SeoThemes\IconWidget;

use function basename;
use function dirname;
use function plugin_dir_url;
use function str_replace;
use function trailingslashit;
use function ucwords;

/**
 * Class Container
 *
 * @package SeoThemes\IconWidget
 */
class Plugin extends Container {

	/**
	 * @var string
	 */
	public $file;

	/**
	 * @var string
	 */
	public $dir;

	/**
	 * @var string
	 */
	public $url;

	/**
	 * @var string
	 */
	public $handle;

	/**
	 * @var string string
	 */
	public $name;

	/**
	 * Container constructor.
	 *
	 * @since 1.2.0
	 *
	 * @param string $file Path to main plugin file.
	 *
	 * @return void
	 */
	public function __construct( $file ) {
		$this->file   = $file;
		$this->dir    = trailingslashit( dirname( $this->file ) );
		$this->url    = trailingslashit( plugin_dir_url( $this->file ) );
		$this->handle = basename( $this->file, '.php' );
		$this->name   = ucwords( str_replace( '-', ' ', $this->handle ) );
	}
}
