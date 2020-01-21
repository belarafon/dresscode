<?php

if (!class_exists('Nexio_Shortcode_Contact')) {
    class Nexio_Shortcode_Contact extends Nexio_Shortcode
    {
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'contact';


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
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('nexio_contact', $atts) : $atts;

            // Extract shortcode parameters.
            extract($atts);

            $css_class = array('nexio-contact');
            $css_class[] = $atts['el_class'];
            $css_class[] = $atts['style'];
            $css_class[] = $atts['contact_custom_id'];
	        $css_class[] = $atts['animate_on_scroll'];
            if (function_exists('vc_shortcode_custom_css_class')) {
                $css_class[] = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), '', $atts);
            }
            $contact_item = (array)vc_param_group_parse_atts($atts['contact_item']);
            ob_start();
            ?>
            <div class="<?php echo esc_attr(implode(' ', $css_class)); ?>">
                <div class="contact-inner">
	                <?php if ($atts['title']): ?>
                        <h3 class="block-title">
			                <?php echo esc_html($atts['title']); ?>
                        </h3>
	                <?php endif; ?>
	                <?php if ($atts['desc']): ?>
                        <div class="block-desc">
			                <?php echo esc_html($atts['desc']); ?>
                        </div>
	                <?php endif; ?>
                    <?php if (!empty($contact_item)):?>
                        <ul>
                            <?php foreach ($contact_item as $item): ?>
                                <?php if (isset($item['title_item']) && $item['title_item'] != ''): ?>
                                    <li>
                                        <?php if (array_key_exists('link_item', $item)):
                                            $item_link = vc_build_link($item['link_item']);
                                            if ($item_link['target'] == '') {
                                                $item_link['target'] = '_self';
                                            }
                                            if ($item_link['title'] != ''): ?>
	                                            <span><?php echo esc_html($item['title_item']); ?></span>
                                                <a href="<?php echo esc_url($item_link['url']) ?>"
                                                   target="<?php echo esc_attr($item_link['target']) ?>">
                                                    <?php echo esc_html($item_link['title']); ?>
                                                </a>
                                            <?php endif; ?>
                                        <?php else: ?>
	                                        <?php echo esc_html($item['title_item']); ?>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            $html = ob_get_clean();

            return apply_filters('Nexio_Shortcode_contact', $html, $atts, $content);
        }
    }
}