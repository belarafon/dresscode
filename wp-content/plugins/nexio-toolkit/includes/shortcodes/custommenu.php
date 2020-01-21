<?php
if ( ! class_exists( 'Nexio_Shortcode_Custommenu' ) ) {
	class Nexio_Shortcode_Custommenu extends Nexio_Shortcode {
		/**
		 * Shortcode name.
		 *
		 * @var  string
		 */
		public $shortcode = 'custommenu';
		
		
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
			$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'nexio_custommenu', $atts ) : $atts;
			
			// Extract shortcode parameters.
			extract( $atts );
			
			$css_class   = array( 'nexio-custommenu' );
			$css_class[] = $atts['style'];
			$css_class[] = $atts['el_class'];
			$css_class[] = $atts['custommenu_custom_id'];
			$css_class[] = $atts['animate_on_scroll'];
            if (function_exists('vc_shortcode_custom_css_class')) {
                $css_class[] = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), '', $atts);
            }
			$nav_menu = get_term_by( 'slug', $atts['menu'], 'nav_menu' );
			
			ob_start();
			?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <div class="custommenu-inner">
                    <?php if ( is_object( $nav_menu ) ): ?>
                        <?php if ( $atts['title'] && ($atts['style'] == 'style-01' || $atts['style'] == 'style-02' || $atts['style'] == 'style-04' || $atts['style'] == 'style-06' || $atts['style'] == 'style-07')): ?>
                            <h4 class="title"><span><?php echo esc_html($atts['title'] ); ?></span></h4>
                        <?php endif ?>
                        <?php
                        wp_nav_menu( array(
                                 'menu'            => $nav_menu->slug,
                                 'theme_location'  => $nav_menu->slug,
                                 'container'       => '',
                                 'container_class' => '',
                                 'container_id'    => '',
                                 'menu_class'      => 'menu',
                                 'fallback_cb'     => 'nexio_navwalker::fallback',
                                 'walker'          => new nexio_navwalker(),
                             )
                        );
                        ?>
                    <?php endif; ?>
                </div>
            </div>
			<?php
			$html = ob_get_clean();
			
			return apply_filters( 'Nexio_Shortcode_custommenu', force_balance_tags( $html ), $atts, $content );
		}
	}
}