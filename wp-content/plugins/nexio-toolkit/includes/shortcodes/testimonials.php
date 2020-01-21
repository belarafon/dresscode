<?php

if (!class_exists('Nexio_Shortcode_Testimonials')) {
	class Nexio_Shortcode_Testimonials extends Nexio_Shortcode
	{
		/**
		 * Shortcode name.
		 *
		 * @var  string
		 */
		public $shortcode = 'testimonials';


		/**
		 * Default $atts .
		 *
		 * @var  array
		 */
		public $default_atts = array();


		public static function generate_css($atts)
		{
			$atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('nexio_testimonials', $atts) : $atts;
			// Extract shortcode parameters.
			extract($atts);
			$css = '';

			return $css;
		}


		public function output_html($atts, $content = null)
		{
			$atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('nexio_testimonials', $atts) : $atts;

			// Extract shortcode parameters.
			extract($atts);
			$css_class = array('nexio-testimonial');
			$css_class[] = $atts['el_class'];
			$css_class[] = $atts['style'];
			$css_class[] = $atts['testimonials_custom_id'];
			$css_class[] = $atts['animate_on_scroll'];
			if (function_exists('vc_shortcode_custom_css_class')) {
				$css_class[] = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), '', $atts);
			}
			$testimonial_link = vc_build_link($atts['link']);
			if ($testimonial_link['url']) {
				$link_url = $testimonial_link['url'];
				$link_target = $testimonial_link['target'];
			} else {
				$link_target = '_self';
				$link_url = '#';
			}
			ob_start();
			?>
            <div class="<?php echo esc_attr(implode(' ', $css_class)); ?>">
                <div class="testimonial-inner">
                    <div class="testimonial-wrap equal-elem">
						<?php if ($atts['rating'] && $atts['style'] == 'style-01') : ?>
                            <div class="rating <?php echo esc_attr($atts['rating']); ?>"><span></span></div>
						<?php endif; ?>
						<?php if ($atts['desc']) : ?>
                            <div class="content"><?php echo wp_specialchars_decode($atts['desc']); ?></div>
						<?php endif; ?>
                    </div>
					<?php if ($atts['image'] && $atts['style'] == 'style-01'): ?>
                        <div class="thumb">
							<?php echo wp_get_attachment_image($atts['image'], 'full'); ?>
                        </div>
					<?php endif; ?>
					<?php if ($atts['name']) : ?>
                        <h3 class="name">
                            <a href="<?php echo esc_url($link_url); ?>"
                               target="<?php echo esc_attr($link_target); ?>">
								<?php echo esc_html($atts['name']); ?>
                            </a>
                        </h3>
					<?php endif; ?>
					<?php if ($atts['position']): ?>
                        <div class="position">
							<?php echo esc_html($atts['position']); ?>
                        </div>
					<?php endif; ?>
                </div>
            </div>
			<?php
			$html = ob_get_clean();

			return apply_filters('nexio_toolkit_shortcode_testimonials', $html, $atts, $content);
		}
	}
}