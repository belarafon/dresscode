<?php

if ( ! class_exists( 'Nexio_Shortcode_Instagramshopwrap' ) ) {
	class Nexio_Shortcode_Instagramshopwrap extends Nexio_Shortcode {
		/**
		 * Shortcode name.
		 *
		 * @var  string
		 */
		public $shortcode = 'instagramshopwrap';

		/**
		 * Default $atts .
		 *
		 * @var  array
		 */
		public $default_atts = array();


		public static function generate_css( $atts ) {
			extract( $atts );
			$css = '';

			return $css;
		}

		public function output_html( $atts, $content = null ) {
			$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'nexio_instagramshopwrap', $atts ) : $atts;

			extract( $atts );
			$css_class   = array( 'nexio-instagramshopwrap' );
			$css_class[] = $atts['el_class'];
			$css_class[] = $atts['iconimage'];
			$css_class[] = $atts['instagramshopwrap_custom_id'];
			$css_class[] = $atts['style'];
			if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
				$css_class[] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $atts['css'], ' ' ), '', $atts );
			}
			$type_icon = isset( $atts['i_type'] ) ? $atts['i_type'] : '';
			if ( $type_icon == 'fontflaticon' ) {
				$class_icon = isset( $atts['icon_nexiocustomfonts'] ) ? $atts['icon_nexiocustomfonts'] : '';
			} else {
				$class_icon = isset( $atts['icon_fontawesome'] ) ? $atts['icon_fontawesome'] : '';
			}
			ob_start();
			?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <div class="title-insshop">
                    <div class="title-inner">
						<?php if ( $atts['iconimage'] == 'imagetype' && $atts['image'] ): ?>
                            <div class="image">
								<?php echo wp_get_attachment_image( $atts['image'], 'full' ); ?>
                            </div>
						<?php else: ?>
                            <div class="icon">
                                <span class="<?php echo esc_attr( $class_icon ); ?>"></span>
                            </div>
						<?php endif; ?>
						<?php if ( $atts['style'] == 'style-01' ): ?>
                            <div class="nexio-title style-02">
								<?php if ( $atts['title'] ): ?>
                                    <h3 class="block-title"><?php echo esc_html( $atts['title'] ); ?></h3>
								<?php endif; ?>
								<?php if ( $atts['desc'] ): ?>
                                    <div class="block-desc"><?php echo esc_html( $atts['desc'] ); ?></div>
								<?php endif; ?>
                            </div>
						<?php endif; ?>
						<?php if ( $atts['style'] == 'style-02' || $atts['style'] == 'style-03' || $atts['style'] == 'style-06' || $atts['style'] == 'style-07'): ?>
							<?php if ( $atts['title'] ): ?>
                                <h3 class="block-title"><?php echo esc_html( $atts['title'] ); ?></h3>
							<?php endif; ?>
						<?php endif; ?>
			            <?php if ( $atts['style'] == 'style-05' ): ?>
				            <?php if ( $atts['title'] ): ?>
                                <h3 class="block-title"><?php echo esc_html( $atts['title'] ); ?></h3>
				            <?php endif; ?>
				            <?php if ( $atts['desc'] ): ?>
                                <div class="block-desc"><?php echo esc_html( $atts['desc'] ); ?></div>
				            <?php endif; ?>
			            <?php endif; ?>
                    </div>
                </div>
				<?php echo wpb_js_remove_wpautop( $content ); ?>
            </div>
			<?php
			$html = ob_get_clean();

			return apply_filters( 'Nexio_Shortcode_instagramshopwrap', $html, $atts, $content );
		}
	}
}