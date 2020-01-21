<?php

if (!class_exists('Nexio_Shortcode_Tabs')) {
    class Nexio_Shortcode_Tabs extends Nexio_Shortcode
    {
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'tabs';


        /**
         * Default $atts .
         *
         * @var  array
         */
        public $default_atts = array(
            'style' => '',
            'css_animation' => '',
            'el_class' => '',
            'css' => '',
            'ajax_check' => 'no',
            'tabs_custom_id' => '',
            'active_section' => '',
            'title_style' => '',
            'des' => '',
        );


        public static function generate_css($atts)
        {
            // Extract shortcode parameters.
            extract($atts);
            $css = '';

            return $css;
        }

        public function output_html($atts, $content = null)
        {
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('nexio_tabs', $atts) : $atts;
            // Extract shortcode parameters.
            extract(
                shortcode_atts(
                    $this->default_atts,
                    $atts
                )
            );
            $css_class = 'nexio-tabs ' . $atts['el_class'] . ' ' . $atts['style'] . ' ' . $atts['tabs_custom_id'] . ' ' . $atts['animate_on_scroll'];
            if (function_exists('vc_shortcode_custom_css_class')) {
                $css_class .= ' ' . apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($css, ' '), '', $atts);
            }
            $sections = $this->get_all_attributes('vc_tta_section', $content);
            $tabs_link = vc_build_link($atts['link']);
            if ($tabs_link['url']) {
                $link_url = $tabs_link['url'];
            } else {
                $link_url = '#';
            }
            if ($tabs_link['target']) {
                $link_target = $tabs_link['target'];
            } else {
                $link_target = '_self';
            }

            $container = '';
            if ($atts['style'] == 'style-01') {
                $container = 'container';
            }
            ob_start();
            ?>
            <div class="<?php echo esc_attr($css_class); ?>">
                <?php if ($sections && is_array($sections) && count($sections) > 0): ?>
                    <div class="tab-head clearfix">
                        <?php if ($atts['title_tabs']): ?>
                            <h3 class="tab-title"><?php echo esc_html($atts['title_tabs']) ?></h3>
                        <?php endif; ?>
                        <ul class="tab-link clearfix">
                            <?php
                            $i = 0;
                            ?>
                            <?php foreach ($sections as $section): ?>
                                <?php
                                $i++;
                                /* Get icon from section tabs */
                                $type_icon = isset($section['i_type']) ? $section['i_type'] : '';
                                $add_icon = isset($section['add_icon']) ? $section['add_icon'] : '';

                                if ($type_icon == 'fontflaticon') {
                                    $class_icon = isset($section['icon_nexiocustomfonts']) ? $section['icon_nexiocustomfonts'] : '';
                                } else {
                                    $class_icon = isset($section['icon_fontawesome']) ? $section['icon_fontawesome'] : '';
                                }
                                $position_icon = isset($section['i_position']) ? $section['i_position'] : '';
                                ?>
                                <li class="<?php if ($i == $atts['active_section']): ?>active<?php endif; ?>">
                                    <a <?php if ($i == $atts['active_section']) {
                                        echo 'class="loaded"';
                                    } ?> data-ajax="<?php echo esc_attr($atts['ajax_check']) ?>"
                                         data-id='<?php echo esc_attr(get_the_ID()); ?>'
                                         data-animate="<?php echo esc_attr($atts['css_animation']); ?>"
                                         data-toggle="tab"
                                         href="#<?php echo esc_attr($section['tab_id']); ?>">
                                        <?php if ($add_icon == true && $position_icon != 'right') : ?><i
                                            class="before-icon <?php echo esc_attr($class_icon); ?>"></i><?php endif; ?>
                                        <?php echo esc_html($section['title']); ?>
                                        <?php if (isset($section['image'])) : ?>
                                            <img alt="<?php echo $section['image']?>" src="<?php echo wp_get_attachment_url( $section['image'] ); ?>"/>
                                        <?php endif; ?>
                                        <?php if ($add_icon == true && $position_icon == 'right') : ?><i
                                            class="after-icon <?php echo esc_attr($class_icon); ?>"></i><?php endif; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="tab-container <?php echo esc_attr($container); ?>">
                        <?php $i = 0; ?>
                        <?php foreach ($sections as $section): ?>
                            <?php $i++; ?>
                            <div class="tab-panel <?php if ($i == $atts['active_section']): ?>active<?php endif; ?>"
                                 id="<?php echo esc_attr($section['tab_id']); ?>">
                                <?php
                                if ($atts['ajax_check'] == '1') {
                                    if ($i == $atts['active_section']) {
                                        echo do_shortcode($section['content']);
                                    }
                                } else {
                                    echo do_shortcode($section['content']);
                                }
                                ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if ($tabs_link['title'] && ($atts['style'] == 'style-03' || $atts['style'] == 'style-04')) : ?>
                    <a class="button-tabs" target="<?php echo esc_attr($link_target); ?>"
                       href="<?php echo esc_url($link_url); ?>"><?php echo esc_html($tabs_link['title']); ?></a>
                <?php endif; ?>
            </div>
            <?php
            $html = ob_get_clean();

            return apply_filters('Nexio_Shortcode_tabs', $html, $atts, $content);
        }
    }
}