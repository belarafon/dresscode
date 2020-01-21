<?php

if ( ! class_exists( 'Nexio_Shortcode_banner' ) ) {
	class Nexio_Shortcode_banner extends Nexio_Shortcode {
		/**
		 * Shortcode name.
		 *
		 * @var  string
		 */
		public $shortcode = 'banner';


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
			$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'nexio_banner', $atts ) : $atts;

			// Extract shortcode parameters.
			extract( $atts );
			$css_class   = array( 'nexio-banner' );
			$css_class[] = $atts['style'];
			$css_class[] = $atts['el_class'];
			$css_class[] = $atts['content_position'];
			$css_class[] = $atts['banner_custom_id'];
			$css_class[] = $atts['animate_on_scroll'];

			if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
				$css_class[] = ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
			}
			$banner_link = vc_build_link( $atts['link'] );
			if ( $banner_link['url'] ) {
				$link_url = $banner_link['url'];
			} else {
				$link_url = '#';
			}
			if ( $banner_link['target'] ) {
				$link_target = $banner_link['target'];
			} else {
				$link_target = '_self';
			}
			$classes = array( 'banner-info' );
			if ( $atts['style'] == 'style-17' || $atts['style'] == 'style-18' ) {
				$classes[] = 'container';
			}
			ob_start();
			?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <div class="banner-inner">
					<?php if ( $atts['image'] ) : ?>
                        <figure class="banner-thumb">
                            <?php echo wp_get_attachment_image( $atts['image'], 'full' ); ?>
	                        <?php if ( $atts['title'] && ($atts['style'] == 'style-27' || $atts['style'] == 'style-25' || $atts['style'] == 'style-26' || $atts['style'] == 'style-34' || $atts['style'] == 'style-35') ): ?>
                                <h6 class="title">
			                        <span></span><?php echo esc_html( $atts['title'] ); ?>
                                </h6>
	                        <?php endif; ?>
	                        <?php if ( $banner_link['title'] && $atts['style'] == 'style-38' ) : ?>
                                <a class="button" target="<?php echo esc_attr( $link_target ); ?>"
                                   href="<?php echo esc_url( $link_url ); ?>">
                                    <span><?php echo esc_html( $banner_link['title'] ); ?></span>
                                </a>
	                        <?php endif; ?>
                        </figure>
					<?php endif; ?>
                    <div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
                        <div class="banner-info-inner"
	                        <?php if ($atts['color'] && ($atts['style'] == 'style-29' || $atts['style'] == 'style-30' || $atts['style'] == 'style-31' || $atts['style'] == 'style-32')) { ?>
                                style="color: <?php echo esc_attr($atts['color']); ?>"
	                        <?php } ?>>
							<?php if ( $atts['image_text'] && $atts['style'] == 'style-14' ) : ?>
                                <div class="image-text">
									<?php echo wp_get_attachment_image( $atts['image_text'], 'full' ); ?>
                                </div>
							<?php endif; ?>
							<?php if ( $atts['title'] && ( $atts['style'] == 'style-02' || $atts['style'] == 'style-09' || $atts['style'] == 'style-17' || $atts['style'] == 'style-18' || $atts['style'] == 'style-20' || $atts['style'] == 'style-23' || $atts['style'] == 'style-29' || $atts['style'] == 'style-31' || $atts['style'] == 'style-33' || $atts['style'] == 'style-37' || $atts['style'] == 'style-40' || $atts['style'] == 'style-41' ) ) : ?>
                                <h6 class="title">
									<?php echo esc_html( $atts['title'] ); ?>
                                </h6>
							<?php endif; ?>
							<?php if ( $atts['bigtitle'] && $atts['style'] != 'style-14' ) : ?>
                                <h3 class="bigtitle" <?php if ( $atts['font_big'] && ($atts['style'] == 'style-40') ): ?> style="font-size: <?php echo esc_attr($atts['font_big']); ?>"
                                	<?php  endif; ?>>
									<?php if ( $atts['style'] == 'style-13' || $atts['style'] == 'style-19' || $atts['style'] == 'style-21' || $atts['style'] == 'style-22' || $atts['style'] == 'style-36' ) : ?>
                                        <a target="<?php echo esc_attr( $link_target ); ?>"
                                           href="<?php echo esc_url( $link_url ); ?>">
                                            <span><?php echo wp_specialchars_decode( $atts['bigtitle'] ); ?></span>
                                        </a>
									<?php else: ?>
                                        <span><?php echo wp_specialchars_decode( $atts['bigtitle'] ); ?></span>
									<?php endif; ?>
                                </h3>
							<?php endif; ?>
							<?php if ( $atts['desc'] && ( $atts['style'] == 'style-06' || $atts['style'] == 'style-11' || $atts['style'] == 'style-24' || $atts['style'] == 'style-28' || $atts['style'] == 'style-38' || $atts['style'] == 'style-41') ): ?>
                                <div class="desc">
									<?php echo esc_html( $atts['desc'] ); ?>
                                </div>
							<?php endif; ?>
							<?php if ( $banner_link['title'] && ($atts['style'] != 'style-13' && $atts['style'] != 'style-22' && $atts['style'] != 'style-36' && $atts['style'] != 'style-38') ) : ?>
                                <a class="button" target="<?php echo esc_attr( $link_target ); ?>"
                                   href="<?php echo esc_url( $link_url ); ?>">
                                    <span><?php echo esc_html( $banner_link['title'] ); ?></span>
                                </a>
							<?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
			<?php
			$html = ob_get_clean();

			return apply_filters( 'Nexio_Shortcode_banner', $html, $atts, $content );
		}
	}
}