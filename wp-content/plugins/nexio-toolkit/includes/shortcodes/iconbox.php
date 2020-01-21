<?php

if ( ! class_exists( 'Nexio_Shortcode_Iconbox' ) ) {
	class Nexio_Shortcode_Iconbox extends Nexio_Shortcode {
		/**
		 * Shortcode name.
		 *
		 * @var  string
		 */
		public $shortcode = 'iconbox';


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
			$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'nexio_iconbox', $atts ) : $atts;

			// Extract shortcode parameters.
			extract( $atts );

			$css_class   = array( 'nexio-iconbox' );
			$css_class[] = $atts['el_class'];
			$css_class[] = $atts['style'];
			$css_class[] = $atts['iconbox_custom_id'];
			$css_class[] = $atts['animate_on_scroll'];
			if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
				$css_class[] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $atts['css'], ' ' ), '', $atts );
			}
			$type_icon = isset( $atts['i_type'] ) ? $atts['i_type'] : '';
			if ( $type_icon == 'fontflaticon' ) {
				$class_icon = isset( $atts['icon_nexiocustomfonts'] ) ? $atts['icon_nexiocustomfonts'] : '';
			} else {
				$class_icon = isset( $atts['icon_fontawesome'] ) ? $atts['icon_fontawesome'] : '';
			}
			$iconbox_link = vc_build_link( $atts['link'] );
			if ( $iconbox_link['url'] ) {
				$link_url = $iconbox_link['url'];
			} else {
				$link_url = '#';
			}
			if ( $iconbox_link['target'] ) {
				$link_target = $iconbox_link['target'];
			} else {
				$link_target = '_self';
			}
			ob_start();
			?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <div class="iconbox-inner">
					<?php if ( $atts['numbercount'] && $atts['style'] == 'style-04'): ?>
                        <div class="number">
							<?php echo esc_html( $atts['numbercount'] ); ?>
                        </div>
					<?php endif; ?>
                    <div class="icon">
						<?php if ( $atts['iconimage'] == 'imagetype' && $atts['image'] ): ?>
							<?php echo wp_get_attachment_image( $atts['image'], 'full' ); ?>
						<?php else: ?>
                            <span class="<?php echo esc_attr( $class_icon ); ?>"></span>
						<?php endif; ?>
                    </div>
                    <div class="content">
						<?php if ( $atts['title'] ): ?>
                            <h4 class="title">
								<?php echo esc_html( $atts['title'] ); ?>
                            </h4>
						<?php endif; ?>
						<?php if ( $atts['des'] && ( $atts['style'] == 'style-01' || $atts['style'] == 'style-03' || $atts['style'] == 'style-04' || $atts['style'] == 'style-05' || $atts['style'] == 'style-06' || $atts['style'] == 'style-07' ) ): ?>
                            <div class="desc">
								<?php echo esc_html( $atts['des'] ); ?>
                            </div>
						<?php endif; ?>
	                    <?php if ( $iconbox_link['title'] && $atts['style'] == 'style-04') : ?>
                            <a class="button" target="<?php echo esc_attr( $link_target ); ?>"
                               href="<?php echo esc_url( $link_url ); ?>"><?php echo esc_html( $iconbox_link['title'] ); ?></a>
	                    <?php endif; ?>
                    </div>
                </div>
            </div>
			<?php
			$html = ob_get_clean();

			return apply_filters( 'Nexio_Shortcode_iconbox', $html, $atts, $content );
		}
	}
}