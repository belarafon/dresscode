<?php

    if( !class_exists('Responsive_Js_Composer_Vc_column_inner')){
        class Responsive_Js_Composer_Vc_column_inner extends Responsive_Js_Composer_Shortcode{
            public $shortcode = 'vc_column_inner';

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
                    $vc_column_inner_reponsive = array();
                    if( isset($atts['vc_column_inner_reponsive'])){
                        $vc_column_inner_reponsive = vc_param_group_parse_atts( $atts['vc_column_inner_reponsive'] );
                    }

                    if( $vc_column_inner_reponsive && count($vc_column_inner_reponsive) > 0 ){
                        foreach ($vc_column_inner_reponsive as $item ){

                            $screen = '';
                            $unit = '';
                            if( isset( $item['screen']) && is_numeric( $item['screen'] ) && $item['screen'] > 0 ){
                                $screen = $item['screen'];
                            }elseif ( isset($item['screen']) && $item['screen'] =='custom'){
                                if( isset( $item['screen_custom'] ) && is_numeric($item['screen_custom']) && $item['screen_custom'] > 0 ){
                                    $screen = $item['screen_custom'];
                                }
                            }
                            if( isset($item['unit']) && $item['unit'] !="" ){
                                $unit = $item['unit'];
                            }elseif( isset( $item['unit_custom'] ) &&  $item['unit_custom'] != ""){
                                $unit = $item['unit_custom'];
                            }
                            if( $screen !='' && is_numeric($screen) && $screen > 0 && $item['value']!= "" ){
                                if( $unit =='other'){
                                    $css .='@media (max-width: '.$screen.'px){ .'.$atts['responsive_js_composer_custom_id'].' { width:'.$item['value'].'!important; float:left;} }';
                                }else{
                                    $css .='@media (max-width: '.$screen.'px){ .'.$atts['responsive_js_composer_custom_id'].' { width:'.$item['value'].$unit.'!important; float:left;} }';
                                }

                            }
                        }
                    }
                }

                return $css;
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
                        'param_name' => 'vc_column_inner_reponsive',
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
                                'type'        => 'dropdown',
                                'heading'     => esc_html__( 'Unit', 'responsive-js-composer' ),
                                'param_name'  => 'unit',
                                'value'       => array(
                                    esc_html__( '%', 'responsive-js-composer' ) => '%',
                                    esc_html__('px', 'responsive-js-composer')    => 'px',
                                    esc_html__('Custom', 'responsive-js-composer')  => 'custom',
                                    esc_html__('Other', 'responsive-js-composer')    => 'other',
                                ),
                                'std'=>'%',
                                'admin_label' => true,
                            ),
                            array(
                                "type"        => "textfield",
                                "heading"     => esc_html__("Unit Custom", 'responsive-js-composer'),
                                "param_name"  => "unit_custom",
                                "dependency"  => array("element" => "unit", "value" => array( 'custom' )),
                            ),
                            array(
                                "type"        => "textfield",
                                "heading"     => esc_html__("Value", 'responsive-js-composer'),
                                "param_name"  => "value",
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
                vc_add_params( 'vc_column_inner', $attributes );
            }
        }
        new Responsive_Js_Composer_Vc_column_inner();
    }