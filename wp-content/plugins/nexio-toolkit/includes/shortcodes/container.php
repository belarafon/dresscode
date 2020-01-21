<?php

if ( !class_exists( 'Nexio_Shortcode_Container' ) ) {

	class Nexio_Shortcode_Container extends Nexio_Shortcode
	{
		/**
		 * Shortcode name.
		 *
		 * @var  string
		 */
		public $shortcode = 'container';


		/**
		 * Default $atts .
		 *
		 * @var  array
		 */
		public $default_atts = array();


		public static function generate_css( $atts )
		{
			// Extract shortcode parameters.
			extract( $atts );
			$css = '';
			return $css;
		}


		public function output_html( $atts, $content = null )
		{
			$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'nexio_container', $atts ) : $atts;
			// Extract shortcode parameters.
			extract( $atts );

			$css_class   = array( 'nexio-container-sc' );
			$css_class[] = $atts[ 'el_class' ];
			$css_class[] = $atts['animate_on_scroll'];
			$css_class[] = $atts[ 'container_custom_id' ];
            if (function_exists('vc_shortcode_custom_css_class')) {
                $css_class[] = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), '', $atts);
            }
			$classes = '';
            if($atts['container_type'] == 'normal' ) {
	            $classes .= 'container';
            } else {
	            $classes .= 'nexio-container';
            }
			ob_start();
			?>
			<div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
				<div class="<?php echo esc_attr($classes); ?>">
					<?php echo wpb_js_remove_wpautop( $content ); ?>
				</div>
			</div>
			<?php
			$html = ob_get_clean();

			return apply_filters( 'Nexio_Shortcode_container', force_balance_tags( $html ), $atts, $content );
		}
	}
}