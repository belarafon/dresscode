<?php

if ( ! class_exists( 'Nexio_Shortcode_video' ) ) {
	class Nexio_Shortcode_video extends Nexio_Shortcode {
		/**
		 * Shortcode name.
		 *
		 * @var  string
		 */
		public $shortcode = 'video';


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
			$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'nexio_video', $atts ) : $atts;

			// Extract shortcode parameters.
			extract( $atts );
			$css_class   = array( 'nexio-video' );
			$css_class[] = $atts['style'];
			$css_class[] = $atts['el_class'];
			$css_class[] = $atts['video_custom_id'];
			$css_class[] = $atts['animate_on_scroll'];

			if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
				$css_class[] = ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
			}
			$loop     = '';
			$autoplay = '';
			$video_link = vc_build_link( $atts['link'] );
			if ( $video_link['url'] ) {
				$link_url = $video_link['url'];
			} else {
				$link_url = '#';
			}
			if ( $video_link['target'] ) {
				$link_target = $video_link['target'];
			} else {
				$link_target = '_self';
			}
			ob_start();
			?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <div class="video-inner">
					<?php if ( $atts['style'] == 'style-01' ): ?>
						<?php if ( $atts['video'] ) { ?>
							<?php if ( $atts['type'] == 'html5' ) {
								if ( $atts['loop'] == 'yes' ) {
									$loop = 'loop="true"';
								}
								if ( $atts['autoplay'] == 'yes' ) {
									$autoplay = 'autoplay="autoplay"';
								}
								?>
                                <video <?php echo esc_attr( $loop ); ?>
                                        class="video-html5" <?php echo esc_attr( $autoplay ) ?>>
                                    <source src="<?php echo esc_url( $atts['video'] ) ?>" type="video/mp4">
                                </video>
							<?php } elseif ( $atts['type'] == 'vimeo' ) {
								if ( $atts['loop'] == 'yes' ) {
									$loop = 'loop=1&';
								}
								if ( $atts['autoplay'] == 'yes' ) {
									$autoplay = 'autoplay=1';
								}
								$video_link = 'https://player.vimeo.com/video/' . $atts['video'] . '?' . $loop . $autoplay . '';
								?>
                                <div class="video-wrap">
                                    <iframe src="<?php echo esc_url( $video_link ) ?>" frameborder="0"
                                            allowfullscreen></iframe>
                                </div>
							<?php } else {
								if ( $atts['loop'] == 'yes' ) {
									$loop = 'loop=1&';
								}
								if ( $atts['autoplay'] == 'yes' ) {
									$autoplay = 'autoplay=1';
								}
								$video_link = 'https://www.youtube.com/embed/' . $atts['video'] . '?control=0&' . $loop . $autoplay . '';
								?>
                                <div class="video-wrap">
                                    <iframe src="<?php echo esc_url( $video_link ) ?>" frameborder="0"
                                            allowfullscreen></iframe>
                                </div>
							<?php } ?>
						<?php } ?>
					<?php else: ?>
						<?php if ( $atts['image'] ) : ?>
							<?php echo wp_get_attachment_image( $atts['image'], 'full' ); ?>
						<?php endif; ?>
                        <div class="video-info">
                            <?php if ( $atts['title'] && ($atts['style'] == 'style-03' || $atts['style'] == 'style-04')) : ?>
                                <h3><?php echo wp_specialchars_decode($atts['title'])?></h3>
                            <?php endif; ?>
                            <?php if ( $video_link['title'] ) : ?>
                                <div class="nexio-bt-video">
                                    <a target="<?php echo esc_attr( $link_target ); ?>"
                                       href="<?php echo esc_url( $link_url ); ?>">
                                        <span class="nexio-video-icon"></span>
                                        <span class="video-text"><?php echo esc_html( $video_link['title'] ); ?></span>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
					<?php endif; ?>
                </div>
            </div>
			<?php
			$html = ob_get_clean();

			return apply_filters( 'Nexio_Shortcode_video', $html, $atts, $content );
		}
	}
}