<?php

namespace SeoThemes\IconWidget;

use WP_Widget;
use function esc_html__;
use function file_exists;

/**
 * Class Widget
 *
 * @package SeoThemes\IconWidget
 */
class Widget extends WP_Widget {

	/**
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Widget constructor.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function __construct() {

		// Get plugin instance.
		$this->plugin = Factory::get_instance();

		parent::__construct(
			$this->plugin->handle,
			esc_html__( 'Icon', 'icon-widget' ),
			[
				'classname'   => 'icon_widget',
				'description' => esc_html__( 'Displays an icon with a title and description.', 'icon-widget' ),
			]
		);
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args     The array of form elements.
	 * @param array $instance The current instance of the widget.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$html      = $args['before_widget'];
		$shortcode = '[icon_widget ';
		$params    = [
			'title',
			'content',
			'link',
			'icon',
			'weight',
			'size',
			'align',
			'color',
			'bg',
			'padding',
			'radius',
		];

		foreach ( $params as $param ) {
			$value     = isset( $instance[ $param ] ) ? $instance[ $param ] : '';
			$shortcode .= $value ? $param . '="' . str_replace( '"', '\'', addslashes( $value ) ) . '" ' : '';
		}

		$shortcode .= ']';
		$html      .= do_shortcode( $shortcode );
		$html      .= $args['after_widget'];

		echo $html;
	}

	/**
	 * Process the widget's options to be saved.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance The new instance of values to be generated via the
	 *                            update.
	 * @param array $old_instance The previous instance of values before the update.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// Update widget's old values with new incoming values.
		$instance['title']   = sanitize_text_field( $new_instance['title'] );
		$instance['content'] = wp_kses_post( $new_instance['content'] );
		$instance['link']    = esc_url_raw( $new_instance['link'] );
		$instance['icon']    = sanitize_html_class( $new_instance['icon'] );
		$instance['weight']  = sanitize_html_class( $new_instance['weight'] );
		$instance['size']    = sanitize_html_class( $new_instance['size'] );
		$instance['align']   = sanitize_html_class( $new_instance['align'] );
		$instance['color']   = sanitize_hex_color( $new_instance['color'] );
		$instance['bg']      = sanitize_hex_color( $new_instance['bg'] );
		$instance['padding'] = intval( $new_instance['padding'] );
		$instance['radius']  = intval( $new_instance['radius'] );

		return $instance;
	}

	/**
	 * Generates the administration form for the widget.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance The array of keys and values for the widget.
	 *
	 * @return void
	 */
	public function form( $instance ) {
		$defaults = apply_filters( 'icon_widget_defaults', [
			'title'   => '',
			'content' => '',
			'link'    => '',
			'icon'    => apply_filters( 'icon_widget_default_icon', '\f000' ),
			'weight'  => 'default',
			'size'    => apply_filters( 'icon_widget_default_size', '2x' ),
			'align'   => apply_filters( 'icon_widget_default_align', 'left' ),
			'color'   => apply_filters( 'icon_widget_default_color', '#333333' ),
			'bg'      => '',
			'padding' => '',
			'radius'  => '',
		] );

		$instance = wp_parse_args( (array) $instance, $defaults );

		$this->render_admin( $instance );
	}

	/**
	 * Render admin fields.
	 *
	 * @since 1.3.0
	 *
	 * @param array $instance
	 *
	 * @return void
	 */
	public function render_admin( array $instance ) {
		$title    = $instance['title'];
		$content  = $instance['content'];
		$link     = $instance['link'];
		$icon     = $instance['icon'];
		$weight   = $instance['weight'];
		$size     = $instance['size'];
		$align    = $instance['align'];
		$color    = $instance['color'];
		$bg       = $instance['bg'];
		$padding  = $instance['padding'];
		$radius   = $instance['radius'];
		$default  = Utils::get_default_icon_font();
		$settings = get_option( 'icon_widget_settings', [] );
		$config   = $this->plugin->dir . 'config/' . ( $settings['font'] ?? $default ) . '.php';
		$scales   = [ 'xs', 'sm', 'lg', '2x', '3x', '5x', '7x', '10x' ];

		if ( file_exists( $config ) ) {
			$icons = require $config;
		} else {
			return;
		}

		?>

		<div class="wrapper">

			<p>
				<label
					for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
					<?php esc_html_e( 'Title:', 'icon-widget' ); ?>
				</label>
				<br/>
				<input
					type="text" class='widefat'
					id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
					value="<?php echo esc_attr( $title ); ?>"
				>
			</p>

			<p>
				<label
					for="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>">
					<?php esc_html_e( 'Content:', 'icon-widget' ); ?>
				</label>
				<br/>
				<textarea
					class='widefat'
					rows="4"
					id="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'content' ) ); ?>"
					value="<?php echo esc_attr( $this->get_field_name( 'content' ) ); ?>"><?php echo esc_textarea( $content ); ?>
				</textarea>
			</p>

			<p>
				<label
					for="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>">
					<?php esc_html_e( 'Link:', 'icon-widget' ); ?>
				</label>
				<br/>
				<input
					type="url"
					class='widefat'
					id="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'link' ) ); ?>"
					value="<?php echo esc_attr( $link ); ?>"
				>
			</p>

			<script type="text/javascript">
				jQuery( document ).ready( function ( $ ) {
					$( '#widgets-right .select-picker' ).selectpicker( {
						iconBase: 'fa',
						dropupAuto: false
					} );
				} );
			</script>
			<p>
				<label
					for="<?php echo esc_attr( $this->get_field_id( 'icon' ) ); ?>">
					<?php esc_html_e( 'Icon:', 'icon-widget' ); ?>
				</label>
				<br/>
				<select
					class='select-picker widefat'
					id="<?php echo esc_attr( $this->get_field_id( 'icon' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'icon' ) ); ?>"
					data-live-search="true"
				>

					<?php foreach ( $icons as $icon ) : ?>

						<option
							data-icon='<?php echo esc_attr( $icon ); ?>'
							value="<?php echo esc_attr( $icon ); ?>" <?php echo ( $instance['icon'] === $icon ) ? 'selected' : ''; ?>><?php echo esc_html( str_replace( [
								'-',
								'far ',
								'ion ',
							], [ ' ', '', '' ], $icon ) ); ?>
						</option>

					<?php endforeach; ?>

				</select>
			</p>

			<p>
				<label
					for="<?php echo esc_attr( $this->get_field_id( 'weight' ) ); ?>">
					<?php esc_html_e( 'Weight:', 'icon-widget' ); ?>
				</label>
				<br/>
				<select
					class='widefat'
					id="<?php echo esc_attr( $this->get_field_id( 'weight' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'weight' ) ); ?>"
					type="text"
				>

					<?php for ( $i = 1; $i < 10; $i++ ) : ?>

						<option
							value='<?php echo "{$i}00"; ?>' <?php echo ( "{$i}00" === $weight ) ? 'selected' : ''; ?>
						>
							<?php echo "{$i}00"; ?>
						</option>

					<?php endfor; ?>

					<option
						value='default' <?php echo ( 'default' === $weight ) ? 'selected' : ''; ?>
					>
						<?php esc_attr_e( 'Default', 'icon-widget' ); ?>
					</option>

				</select>
			</p>

			<p>
				<label
					for="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>"
				>
					<?php esc_html_e( 'Size:', 'icon-widget' ); ?>
				</label>
				<br/>
				<select
					class='widefat'
					id="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'size' ) ); ?>"
					type="text"
				>

					<?php foreach ( $scales as $scale ) : ?>

						<option
							value='<?php echo $scale; ?>' <?php echo ( $scale === $size ) ? 'selected' : ''; ?>>
							<?php echo $scale; ?>
						</option>

					<?php endforeach; ?>

				</select>
			</p>

			<?php $alignments = [ 'left', 'center', 'right' ]; ?>

			<p>
				<label
					for="<?php echo esc_attr( $this->get_field_id( 'align' ) ); ?>"
				>
					<?php esc_html_e( 'Align:', 'icon-widget' ); ?>
				</label>
				<br/>
				<select
					class='widefat'
					id="<?php echo esc_attr( $this->get_field_id( 'align' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'align' ) ); ?>"
					type="text"
				>

					<?php foreach ( $alignments as $alignment ) : ?>

						<option
							value='<?php echo $alignment; ?>' <?php echo ( $alignment === $align ) ? 'selected' : ''; ?>
						>
							<?php echo ucwords( $alignment ); ?>
						</option>

					<?php endforeach; ?>

				</select>
			</p>

			<p>
				<label
					for="<?php echo esc_attr( $this->get_field_id( 'padding' ) ); ?>"
				>
					<?php esc_html_e( 'Padding (px):', 'icon-widget' ); ?>
				</label>
				<br/>
				<input
					type="number"
					class='widefat'
					id="<?php echo esc_attr( $this->get_field_id( 'padding' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'padding' ) ); ?>"
					value="<?php echo esc_attr( $padding ); ?>"
				>
			</p>
			<p>
				<label
					for="<?php echo esc_attr( $this->get_field_id( 'radius' ) ); ?>"
				>
					<?php esc_html_e( 'Border Radius (px):', 'icon-widget' ); ?>
				</label>
				<br/>
				<input
					type="number"
					class='widefat'
					id="<?php echo esc_attr( $this->get_field_id( 'radius' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'radius' ) ); ?>"
					value="<?php echo esc_attr( $radius ); ?>"
				>
			</p>

			<script type="text/javascript">
				( function ( $ ) {
					function initColorPicker( widget ) {
						widget.find( '.color-picker' ).wpColorPicker( {
							change: _.throttle( function () { // For Customizer
								$( this ).trigger( 'change' );
							}, 3000 )
						} );
					}

					function onFormUpdate( event, widget ) {
						initColorPicker( widget );
					}

					$( document ).on( 'widget-added widget-updated', onFormUpdate );

					$( document ).ready( function () {
						$( '#widgets-right .widget:has(.color-picker)' ).each( function () {
							initColorPicker( $( this ) );
						} );
					} );
				}( jQuery ) );
			</script>
			<p>
				<label
					for="<?php echo esc_attr( $this->get_field_id( 'color' ) ); ?>"
				>
					<?php esc_html_e( 'Icon Color:', 'icon-widget' ); ?>
				</label>
				<br/>
				<input
					class="color-picker"
					type="text"
					id="<?php echo esc_attr( $this->get_field_id( 'color' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'color' ) ); ?>"
					value="<?php echo esc_attr( $color ); ?>"
				/>
			</p>

			<p>
				<label
					for="<?php echo esc_attr( $this->get_field_id( 'bg' ) ); ?>"
				>
					<?php esc_html_e( 'Background Color:', 'icon-widget' ); ?>
				</label>
				<br/>
				<input
					class="color-picker"
					type="text"
					id="<?php echo esc_attr( $this->get_field_id( 'bg' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'bg' ) ); ?>"
					value="<?php echo esc_attr( $bg ); ?>"
				/>
			</p>

		</div>


		<?php
	}
}
