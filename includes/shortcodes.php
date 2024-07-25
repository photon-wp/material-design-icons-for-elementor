<?php
/**
 * MD_Icons_Shortcodes class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'MD_Icons_Shortcodes' ) ) {

	/**
	 * Define MD_Icons_Shortcodes class
	 */
	class MD_Icons_Shortcodes {

		/**
		 * Initialize hooks
		 *
		 * @return void
		 */
		public function __construct() {
			add_shortcode( 'md_icon', array( $this, 'do_shortcode' ) );
		}

		/**
		 * Handle shortcode
		 *
		 * @param  array  $atts
		 * @return string
		 */
		public function do_shortcode( $atts = array() ) {

			$atts = shortcode_atts( array(
				'style' => '',
				'icon'  => '',
				'size'  => '',
			), $atts, 'md_icon' );

			$result = '';

			if ( empty( $atts['style'] ) || empty( $atts['icon'] ) ) {
				return $result;
			}

			if ( empty( $atts['size'] ) ) {
				$atts['size'] = '50px';
			}

			$config = md_icons()->integration->get_icons_config( $atts['style'] );

			if ( ! $config ) {
				return $result;
			}

			$result = sprintf(
				'<i class="%1$s %2$s" data-md-icon="%3$s" style="font-size:%4$s"></i>',
				$config['displayPrefix'],
				$atts['icon'],
				str_replace( 'md-', '', $atts['icon']  ),
        $atts['size']
			);

			$result = wp_kses_post( $result );

			static $added_styles = false;

			// Dequeue dependency styles.
			$config['enqueue'] = array();

			$this->enqueue_icon_assets( $config );

			return $result;
		}

		/**
		 * Enqueue icon assets
		 *
		 * @param array $config
		 */
		public function enqueue_icon_assets( $config ) {

			if ( ! wp_script_is( $config['name'], 'registered' ) ) {
				md_icons()->integration->register_icon_style_assets( $config );
			}

			if ( ! wp_script_is( $config['name'], 'enqueued' ) ) {
				wp_enqueue_style( $config['name'] );
			}

		}
	}

}
