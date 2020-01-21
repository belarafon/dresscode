<?php

if( !class_exists('Responsive_Js_Composer_Vc_custom_heading')){
    class Responsive_Js_Composer_Vc_custom_heading extends Responsive_Js_Composer_Shortcode{
        public $shortcode = 'vc_custom_heading';

        public function __construct(){
            parent::__construct();
            add_action( 'vc_after_mapping', array( &$this, 'add_param' ) );

        }
        /**
         * Generate custom CSS.
         *
         * @param   array  $atts  Shortcode parameters.
         *
         * @return  string
         */
        public static function generate_css( $atts ) {
            $css = '';
            if( isset( $atts['enbale_extend_reponsive'] ) && $atts['enbale_extend_reponsive'] =='yes'){
                $vc_custom_heading_reponsive = array();
                if( isset($atts['vc_custom_heading_reponsive'])){
                    $vc_custom_heading_reponsive = vc_param_group_parse_atts( $atts['vc_custom_heading_reponsive'] );
                }


                if( $vc_custom_heading_reponsive && count($vc_custom_heading_reponsive) > 0 ){
                    foreach ($vc_custom_heading_reponsive as $item ){
                        $styles = array();
                        if( isset( $item['font_container'])){
                            $font_container_obj = new Vc_Font_Container();
                            $font_container_field_settings = isset( $font_container_field['settings'], $font_container_field['settings']['fields'] ) ? $font_container_field['settings']['fields'] : array();
                            $font_container_data = $font_container_obj->_vc_font_container_parse_attributes( $font_container_field_settings, $item['font_container'] );
                            $styles = self::get_styles($font_container_data);
                        }
                        $screen = '';
                        if( isset( $item['screen']) && is_numeric( $item['screen'] ) && $item['screen'] > 0 ){
                            $screen = $item['screen'];
                        }elseif ( isset($item['screen']) && $item['screen'] =='custom'){
                            if( isset( $item['screen_custom'] ) && is_numeric($item['screen_custom']) && $item['screen_custom'] > 0 ){
                                $screen = $item['screen_custom'];
                            }
                        }
                        $styles[] = '';
                        if( $screen !='' && is_numeric($screen) && $screen > 0  && is_array( $styles ) && count( $styles ) > 0){
                            $css .='@media (max-width: '.$screen.'px){ .'.$atts['responsive_js_composer_custom_id'].' { '.implode( '!important;', $styles ).' }}';
                        }
                    }
                }
            }
            return $css;
        }

        static  function get_styles( $font_container_data ){
            $styles = array();
            if ( ! empty( $font_container_data ) && isset( $font_container_data['values'] ) ) {

                foreach ( $font_container_data['values'] as $key => $value ) {
                    if ( 'tag' !== $key && strlen( $value ) ) {
                        if ( preg_match( '/description/', $key ) ) {
                            continue;
                        }
                        if ( 'font_size' === $key || 'line_height' === $key ) {
                            $value = preg_replace( '/\s+/', '', $value );
                        }
                        if ( 'font_size' === $key ) {
                            $pattern = '/^(\d*(?:\.\d+)?)\s*(px|\%|in|cm|mm|em|rem|ex|pt|pc|vw|vh|vmin|vmax)?$/';
                            // allowed metrics: http://www.w3schools.com/cssref/css_units.asp
                            $regexr = preg_match( $pattern, $value, $matches );
                            $value = isset( $matches[1] ) ? (float) $matches[1] : (float) $value;
                            $unit = isset( $matches[2] ) ? $matches[2] : 'px';
                            $value = $value . $unit;
                        }
                        if ( strlen( $value ) > 0 ) {
                            $styles[] = str_replace( '_', '-', $key ) . ': ' . $value;
                        }
                    }
                }
            }

            return $styles;
        }

        public function add_param(){
            $attributes = array(
                array(
                    'type'        => 'checkbox',
                    'heading'     => esc_html__('Enbale Extend Reponsive', 'azora'),
                    'param_name'  => 'enbale_extend_reponsive',
                    'value'       => array(esc_html__('Yes', 'responsive-js-composer') => 'yes'),
                    'std'         => '',
                    'group'       => esc_html__( 'Responsive Options', 'responsive-js-composer' ),
                ),
                array(
                    'type' => 'param_group',
                    'value' => '',
                    'param_name' => 'vc_custom_heading_reponsive',
                    "heading"     => esc_html__("Extend Responsive Options", 'responsive-js-composer'),
                    'params' => array(
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__( 'Screen Device', 'responsive-js-composer' ),
                            'param_name'  => 'screen',
                            'value'       => array(
                                esc_html__( '1366px', 'responsive-js-composer' ) => '1366',
                                esc_html__( '1280px', 'responsive-js-composer' ) => '1280',
                                esc_html__('991px', 'responsive-js-composer')    => '991',
                                esc_html__('767px ', 'responsive-js-composer')   => '767',
                                esc_html__('480px ', 'responsive-js-composer')   => '480',
                                esc_html__('320px ', 'responsive-js-composer')   => '320',
                                esc_html__('Custom ', 'responsive-js-composer')  => 'custom',
                            ),
                            'std'=>'1366',
                            'admin_label' => true,
                        ),
                        array(
                            "type"        => "textfield",
                            "heading"     => esc_html__("Screen Custom", 'responsive-js-composer'),
                            "param_name"  => "screen_custom",
                            "suffix"      => esc_html__("px", 'responsive-js-composer'),
                            "dependency"  => array("element" => "screen", "value" => array( 'custom' )),
                        ),
                        array(
                            'type' => 'font_container',
                            'param_name' => 'font_container',
                            'settings' => array(
                                'fields' => array(
                                    'text_align',
                                    'font_size',
                                    'line_height',
                                    'color',
                                    'text_align_description'  => __('Select text alignment.', 'responsive-js-composer'),
                                    'font_size_description'   => __('Enter font size.', 'responsive-js-composer'),
                                    'line_height_description' => __('Enter line height.', 'responsive-js-composer'),
                                    'color_description'       => __( 'Select heading color.', 'responsive-js-composer' ),
                                ),
                            ),
                        ),
                    ),
                    'group'       => esc_html__( 'Responsive Options', 'responsive-js-composer' ),
                    "dependency"  => array(
                        "element" => "enbale_extend_reponsive", "value" => array( 'yes' ),
                    ),
                ),
                array(
                    'param_name'       => 'responsive_js_composer_custom_id',
                    'heading'          => esc_html__('Hidden ID', 'responsive-js-composer'),
                    'type'             => 'responsive_js_composer_uniqid',
                    'edit_field_class' => 'hidden',
                ),
            );
            vc_add_params( 'vc_custom_heading', $attributes );
        }
    }
    new Responsive_Js_Composer_Vc_custom_heading();
}