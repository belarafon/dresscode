<?php

if ( ! class_exists( 'Nexio_Shortcode_demo' ) ) {
	class Nexio_Shortcode_demo extends Nexio_Shortcode {
		/**
		 * Shortcode name.
		 *
		 * @var  string
		 */
		public $shortcode = 'demo';
		
		
		/**
		 * Default $atts .
		 *
		 * @var  array
		 */
		public $default_atts = array();
		
		
		public static function generate_css( $atts ) {
			// Extract shortcode parameters.
			extract( $atts );
			$css = '';
			
			return $css;
		}
		
		
		public function output_html( $atts, $content = null ) {
			$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'nexio_demo', $atts ) : $atts;
			
			// Extract shortcode parameters.
			extract( $atts );
			$css_class   = array( 'nexio-demo' );
			$css_class[] = $atts['style'];
			$css_class[] = $atts['comming'];
			$css_class[] = $atts['el_class'];
			$css_class[] = $atts['demo_custom_id'];
			$css_class[] = $atts['animate_on_scroll'];
			
			if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
				$css_class[] = ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
			}
			$demo_link = vc_build_link( $atts['link'] );
			if ( $demo_link['url'] ) {
				$link_url = $demo_link['url'];
			} else {
				$link_url = '#';
			}
			
			ob_start();
			?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <div class="demo-inner">
                    <?php if ( $atts['image'] ) {
                        $img      = nexio_toolkit_resize_image( $atts['image'], null, 4000, 4000, true, true, false );
                        $img_html = nexio_toolkit_img_output( $img );
                        ?>
	                    <?php if ( $demo_link['title'] && $atts['style'] == 'style-01') : ?>
                            <a class="demo-link" href="<?php echo esc_url( $link_url ); ?>"><?php echo $img_html; ?></a>
				        <?php else: ?>
                            <div class="nexio-demo-wrap">
                                <a class="demo-open" href="#demo-popup"><?php echo $img_html; ?></a>
                                <div id="demo-popup" class="mfp-hide">
                                    <div class="demo-popup">
                                        <div class="nexio-demo-inner scrollbar-macosx">
                                            <?php
                                            $post_convert = $atts['post_id'];
                                            $post_id = get_post( $post_convert );
                                            $post_content = $post_id->post_content;
                                            $post_content = apply_filters( 'the_content', $post_content );
                                            $post_content = str_replace( ']]>', ']]>', $post_content );
                                            echo wp_specialchars_decode( $post_content );
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
				        <?php endif; ?>
                    <?php } ?>
                    <?php if ( $demo_link['title'] && $atts['style'] == 'style-01') : ?>
                        <div class="demo-content">
                            <a class="demo-button" href="<?php echo esc_url( $link_url ); ?>"><?php echo esc_html( $demo_link['title'] ); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
			<?php
			$html = ob_get_clean();

			return apply_filters( 'Nexio_Shortcode_demo', $html, $atts, $content );
		}
	}
}