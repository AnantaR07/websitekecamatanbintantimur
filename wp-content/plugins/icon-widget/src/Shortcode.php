<?php

namespace SeoThemes\IconWidget;

use function add_shortcode;
use function apply_filters;
use function esc_attr;
use function esc_html;
use function esc_url;
use function shortcode_atts;
use function sprintf;
use function wp_kses_post;
use function wpautop;

/**
 * Class Shortcode
 *
 * @package SeoThemes\IconWidget
 */
class Shortcode extends Service {

	/**
	 * Runs class hooks.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function run() {
		add_shortcode( 'icon_widget', [ $this, 'add_shortcode' ] );
	}

	/**
	 * Add Shortcode.
	 *
	 * @since 1.2.0
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string
	 */
	public function add_shortcode( $atts ) {
		$atts = shortcode_atts(
			apply_filters( 'icon_widget_defaults', [
				'classes' => $this->plugin->handle,
				'title'   => $this->plugin->name,
				'content' => 'Add a short description.',
				'link'    => '',
				'icon'    => apply_filters( 'icon_widget_default_shortcode_icon', 'fa-star' ),
				'weight'  => 'default',
				'size'    => apply_filters( 'icon_widget_default_size', '2x' ),
				'align'   => apply_filters( 'icon_widget_default_align', 'left' ),
				'color'   => apply_filters( 'icon_widget_default_color', '#333333' ),
				'heading' => 'h4',
				'break'   => '<br>',
				'bg'      => '',
				'padding' => '',
				'radius'  => '',
			] ),
			$atts,
			'icon_widget'
		);

		$allowed_headings = [
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'p',
			'span',
			'b',
			'strong',
		];

		$heading = $atts['heading'];
		$heading = in_array( $heading, $allowed_headings, true ) ? $heading : 'h4';
		$classes = esc_attr( $atts['classes'] );
		$title   = esc_html( $atts['title'] );
		$content = wp_kses_post( $atts['content'] );
		$link    = esc_url( $atts['link'] );
		$icon    = esc_attr( $atts['icon'] );
		$size    = esc_attr( $atts['size'] );
		$align   = esc_attr( $atts['align'] );
		$break   = wp_kses_post( $atts['break'] );
		$weight  = 'default' !== $atts['weight'] ? 'font-weight:' . esc_attr( $atts['weight'] ) . ';' : '';
		$color   = $atts['color'] ? 'color:' . esc_attr( $atts['color'] ) . ';' : '';
		$bg      = $atts['bg'] ? 'background-color:' . esc_attr( $atts['bg'] ) . ';' : 'background-color:transparent;';
		$padding = $atts['padding'] ? 'padding:' . esc_attr( $atts['padding'] ) . 'px;' : '';
		$radius  = $atts['radius'] ? 'border-radius:' . esc_attr( $atts['radius'] ) . 'px;' : '';

		// Build HTML.
		$html = sprintf( '<div class="%s" style="text-align:%s">', $classes, $align );
		$html .= $link ? sprintf( '<a href="%s" %s>', $link, apply_filters( 'icon_widget_link_atts', '' ) ) : '';
		$html .= sprintf( '<i class="fa %s fa-%s" style="%s%s%s%s%s"> </i>', $icon, $size, $weight, $color, $bg, $padding, $radius );
		$html .= $link ? '</a>' : '';
		$html .= apply_filters( 'icon_widget_line_break', $break );
		$html .= sprintf( '<%s class="widget-title">%s</%s>', $heading, $title, $heading );
		$html .= apply_filters( 'icon_widget_wpautop', true ) ? wp_kses_post( wpautop( $content ) ) : wp_kses_post( $content );
		$html .= '</div>';

		return apply_filters( 'icon_widget_html_output', $html );
	}
}
