<?php

if ( ! class_exists( 'Nexio_Shortcode_categories' ) ) {

	class Nexio_Shortcode_categories extends Nexio_Shortcode {
		/**
		 * Shortcode name.
		 *
		 * @var  string
		 */
		public $shortcode = 'categories';

		/**
		 * Default $atts .
		 *
		 * @var  array
		 */
		public $default_atts = array();


		public static function generate_css( $atts ) {
			$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'nexio_categories', $atts ) : $atts;
			// Extract shortcode parameters.
			extract( $atts );
			$css = '';

			return $css;
		}


		public function output_html( $atts, $content = null ) {
			$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'nexio_categories', $atts ) : $atts;
			// Extract shortcode parameters.
			extract( $atts );

			$css_class   = array( 'nexio-categories' );
			$css_class[] = $atts['el_class'];
			$css_class[] = $atts['style'];
			$css_class[] = $atts['categories_custom_id'];
			$css_class[] = $atts['animate_on_scroll'];
			if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
				$css_class[] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $atts['css'], ' ' ), '', $atts );
			}
			ob_start();
			?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <div class="nexio-category-inner">
					<?php if ( ! empty( $atts['taxonomy'] ) ):
						$product_term = get_term_by( 'slug', $atts['taxonomy'], 'product_cat' );
						$cat_link = get_term_link( $product_term->term_id, 'product_cat' );
						$cat_thumb_id = get_term_meta( $product_term->term_id, 'thumbnail_id', true );
						$width = 380;
						$height = 380;
						if($atts['style'] == 'style-02') {
							$width = 138;
							$height = 138;
                        }
						$cat_thumb_url = nexio_toolkit_resize_image( $cat_thumb_id, null, $width, $height, true, false, false );
						?>
                        <div class="info">
                            <figure class="category-thumb">
                                <a href="<?php echo esc_url( $cat_link ); ?>">
                                    <img src="<?php echo esc_url( $cat_thumb_url['url'] ) ?>"
                                         alt="<?php echo esc_attr( $atts['taxonomy'] ); ?>"/>
                                </a>
                            </figure>
                            <div class="category-info">
                                <h3 class="category-name">
                                    <a href="<?php echo esc_url( $cat_link ); ?>"><?php echo $product_term->name; ?></a>
                                </h3>
                            </div>
                        </div>
					<?php endif; ?>
                </div>
            </div>
			<?php
			$html = ob_get_clean();

			return apply_filters( 'Nexio_Shortcode_categories', force_balance_tags( $html ), $atts, $content );
		}
	}
}