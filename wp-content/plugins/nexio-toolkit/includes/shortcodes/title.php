<?php

if (!class_exists('Nexio_Shortcode_Title')) {
    class Nexio_Shortcode_Title extends Nexio_Shortcode
    {
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'title';

        /**
         * Default $atts .
         *
         * @var  array
         */
        public $default_atts = array();


        public static function generate_css($atts)
        {
            // Extract shortcode parameters.
            extract($atts);
            $css = '';

            return $css;
        }


        public function output_html($atts, $content = null)
        {
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('nexio_title', $atts) : $atts;

            // Extract shortcode parameters.
            extract($atts);

            $css_class = array('nexio-title');
            $css_class[] = $atts['style'];
            $css_class[] = $atts['el_class'];
            $css_class[] = $atts['title_custom_id'];
	        $css_class[] = $atts['animate_on_scroll'];
            if (function_exists('vc_shortcode_custom_css_class')) {
                $css_class[] = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), '', $atts);
            }
	        $title_link = vc_build_link($atts['link']);
	        if ($title_link['url']) {
		        $link_url = $title_link['url'];
	        } else {
		        $link_url = '#';
	        }
	        if ($title_link['target']) {
		        $link_target = $title_link['target'];
	        } else {
		        $link_target = '_self';
	        }
            ob_start();
            ?>
            <div class="<?php echo esc_attr(implode(' ', $css_class)); ?>">
                <div class="title-inner">
	                <?php if ($atts['smalltitle'] && ($atts['style'] == 'style-03' || $atts['style'] == 'style-10' || $atts['style'] == 'style-17')): ?>
                        <h3 class="small-title">
			                <?php echo esc_html($atts['smalltitle']); ?>
                        </h3>
	                <?php endif; ?>
                    <?php if ($atts['title']): ?>
                        <h3 class="block-title">
                            <?php echo esc_html($atts['title']); ?>
                        </h3>
                    <?php endif; ?>
                    <?php if ($atts['desc'] && ( $atts['style'] == 'style-01' ||  $atts['style'] == 'style-02' ||  $atts['style'] == 'style-03' ||  $atts['style'] == 'style-06' ||  $atts['style'] == 'style-07' ||  $atts['style'] == 'style-09' ||  $atts['style'] == 'style-10' ||  $atts['style'] == 'style-13' ||  $atts['style'] == 'style-16' ||  $atts['style'] == 'style-18' ||  $atts['style'] == 'style-19' ||  $atts['style'] == 'style-21' ||  $atts['style'] == 'style-22' ||  $atts['style'] == 'style-23' ||  $atts['style'] == 'style-24' )): ?>
                        <div class="block-desc">
                            <?php echo wp_specialchars_decode($atts['desc']); ?>
                        </div>
                    <?php endif; ?>
	                <?php if ($title_link['title'] && ( $atts['style'] == 'style-01' ||  $atts['style'] == 'style-03' ||  $atts['style'] == 'style-04' ||  $atts['style'] == 'style-07' ||  $atts['style'] == 'style-10' ||  $atts['style'] == 'style-19')) : ?>
                        <a class="button" target="<?php echo esc_attr($link_target); ?>"
                           href="<?php echo esc_url($link_url); ?>"><?php echo esc_html($title_link['title']); ?></a>
	                <?php endif; ?>
                </div>
            </div>

            <?php
            $html = ob_get_clean();

            return apply_filters('Nexio_Shortcode_title', $html, $atts, $content);
        }
    }
}