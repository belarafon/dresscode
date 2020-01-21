<?php

if (!class_exists('Nexio_Shortcode_Team')) {
    class Nexio_Shortcode_Team extends Nexio_Shortcode
    {
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'team';


        /**
         * Default $atts .
         *
         * @var  array
         */
        public $default_atts = array();


        public static function generate_css($atts)
        {
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('nexio_team', $atts) : $atts;
            // Extract shortcode parameters.
            extract($atts);
            $css = '';

            return $css;
        }


        public function output_html($atts, $content = null)
        {
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('nexio_team', $atts) : $atts;

            // Extract shortcode parameters.
            extract($atts);
            $css_class = array('nexio-team');
            $css_class[] = $atts['el_class'];
            $css_class[] = $atts['style'];
            $css_class[] = $atts['team_custom_id'];
	        $css_class[] = $atts['animate_on_scroll'];
            if (function_exists('vc_shortcode_custom_css_class')) {
                $css_class[] = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), '', $atts);
            }
            $team_link = vc_build_link($atts['link']);
            if ($team_link['url']) {
                $link_url = $team_link['url'];
                $link_target = $team_link['target'];
            } else {
                $link_target = '_self';
                $link_url = '#';
            }
            $social_team = (array)vc_param_group_parse_atts($atts['social_team']);
            ob_start();
            ?>
            <div class="<?php echo esc_attr(implode(' ', $css_class)); ?>">
                <div class="team-inner">
                    <?php if ($atts['image']): ?>
                        <div class="thumb">
                            <?php echo wp_get_attachment_image($atts['image'], 'full'); ?>
                            <?php if (!empty($social_team)): ?>
                                <div class="team-social">
                                    <?php foreach ($social_team as $team): ?>
                                        <?php if ($team['link_social'] != ''): ?>
                                            <?php $icon_html = $this->constructIcon($team); ?>
                                            <a href="<?php echo esc_url($team['link_social']) ?>"><?php echo wp_specialchars_decode($icon_html) ?></a>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
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

            return apply_filters('nexio_toolkit_shortcode_team', $html, $atts, $content);
        }
    }
}