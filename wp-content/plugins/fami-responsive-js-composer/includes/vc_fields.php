<?php
if( !class_exists('Responsive_Js_Composer_Fields')){
    class Responsive_Js_Composer_Fields{
        public function __construct(){
            add_action( 'vc_after_mapping', array( &$this, 'params' ) );
        }
        function  params(){
            vc_add_shortcode_param( 'responsive_js_composer_uniqid',array( &$this, 'uniqid_field' ) );
        }
        public function uniqid_field($settings, $value){
            if( ! $value){
                $value = uniqid(hash('crc32', $settings[ 'param_name' ]).'-');
            }
            $output = '<input type="text" class="wpb_vc_param_value textfield" name="'.$settings[ 'param_name' ].'" value="'.esc_attr($value).'" />';
            return $output;
        }
    }

    new Responsive_Js_Composer_Fields();
}