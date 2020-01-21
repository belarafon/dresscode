<?php

if (!class_exists('Nexio_Shortcode_Newsletter')) {
    class Nexio_Shortcode_Newsletter extends Nexio_Shortcode
    {
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'newsletter';

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
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('nexio_newsletter', $atts) : $atts;

            // Extract shortcode parameters.
            extract($atts);

            $css_class = array('nexio-newsletter');
            $css_class[] = $atts['style'];
            $css_class[] = $atts['el_class'];
            $css_class[] = $atts['newsletter_custom_id'];
            $css_class[] = $atts['animate_on_scroll'];
            if (function_exists('vc_shortcode_custom_css_class')) {
                $css_class[] = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), '', $atts);
            }
	        $type_icon = isset($atts['i_type']) ? $atts['i_type'] : '';
	        if ($type_icon == 'fontflaticon') {
		        $class_icon = isset($atts['icon_nexiocustomfonts']) ? $atts['icon_nexiocustomfonts'] : '';
	        } else {
		        $class_icon = isset($atts['icon_fontawesome']) ? $atts['icon_fontawesome'] : '';
	        }
            ob_start();
            ?>
            <div class="<?php echo esc_attr(implode(' ', $css_class)); ?>">
                <div class="newsletter-inner">
                    <div class="newsletter-top">
	                    <?php if($atts['style'] == 'style-08' || $atts['style'] == 'style-09'):?>
                            <div class="icon">
                                <span class="<?php echo esc_attr($class_icon); ?>"></span>
                            </div>
	                    <?php endif; ?>
	                    <?php if ($atts['heading'] && $atts['style'] == 'style-10'): ?>
                            <h2 class="newsletter-heading"><?php echo esc_html($atts['heading']); ?></h2>
	                    <?php endif; ?>
                        <?php if ($atts['title'] && ($atts['style'] == 'style-01' || $atts['style'] == 'style-02' || $atts['style'] == 'style-03' || $atts['style'] == 'style-04' || $atts['style'] == 'style-06' || $atts['style'] == 'style-07' || $atts['style'] == 'style-10' || $atts['style'] == 'style-11' || $atts['style'] == 'style-12' || $atts['style'] == 'style-13' || $atts['style'] == 'style-14')): ?>
                            <h3 class="newsletter-title"><?php echo esc_html($atts['title']); ?></h3>
                        <?php endif; ?>
                        <?php if ($atts['description'] && $atts['style'] != 'style-09'): ?>
                            <div class="newsletter-description"><?php echo wp_specialchars_decode($atts['description']); ?></div>
                        <?php endif; ?>
                    </div>
                    <form class="newsletter-form-wrap">
                        <input class="email" type="email" name="email"
                               placeholder="<?php echo esc_attr($atts['placeholder_text']); ?>">
                        <button type="submit" name="submit_button"
                                class="btn-submit submit-newsletter">
	                        <?php if ($atts['button_text'] && ($atts['style'] == 'style-01' || $atts['style'] == 'style-02' || $atts['style'] == 'style-05'  || $atts['style'] == 'style-06' || $atts['style'] == 'style-08'  || $atts['style'] == 'style-09' || $atts['style'] == 'style-11' || $atts['style'] == 'style-12' || $atts['style'] == 'style-13' || $atts['style'] == 'style-14')): ?>
                                <?php echo esc_html($atts['button_text']); ?>
	                        <?php elseif($atts['style'] == 'style-03' || $atts['style'] == 'style-04'):?>
                                <i class="flaticon-right-arrow"></i>
	                        <?php else:?>
                                <i class="flaticon-envelope"></i>
	                        <?php endif; ?>
                        </button>
                    </form>
                </div>
            </div>
            <?php
            $html = ob_get_clean();

            return apply_filters('Nexio_Shortcode_newsletter', $html, $atts, $content);
        }
    }
}