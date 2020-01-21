<?php
/**
 * Plugin Name: Fami - Responsive Visual Composer
 * Plugin URI:  https://famithemes.com
 * Description: Custom responsive all shortcode of Visual Composer.
 * Version:     1.1
 * Author:      Fami Themes
 * Author URI:  https://famithemes.com
 * License:     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: fami-responsive-js-composer
 */

// Define url to this plugin file.
define( 'RESPONSIVE_JS_COMPOSER_URL', plugin_dir_url( __FILE__ ) );

// Define path to this plugin file.
define( 'RESPONSIVE_JS_COMPOSER_PATH', plugin_dir_path( __FILE__ ) );
define( 'RESPONSIVE_JS_COMPOSER_METAKEY','_responsive_js_composer_shortcode_custom_css' );


if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if( !class_exists('Responsive_Js_Composer')){
    class Responsive_Js_Composer{
        public function __construct(){
            add_action( 'init', array($this,'load_textdomain') );
            if ( ! function_exists( 'Vc_Manager' ) ) {
                add_action( 'admin_notices',array($this,'install_js_composer_admin_notice') );
            }
            if( ! function_exists( 'Vc_Manager' ) ){
                return;
            }

            $this->includes();

            add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, array($this,'add_css_classes'), 10, 3 );
        }
        function load_textdomain() {
            load_plugin_textdomain( 'fami-responsive-js-composer', false, RESPONSIVE_JS_COMPOSER_PATH . '/languages' );
        }

        public function add_css_classes( $class_string, $tag ,$atts) {
            if( isset( $atts['responsive_js_composer_custom_id'] ) && $atts['responsive_js_composer_custom_id'] != ""){
                $class_string.= ' '.$atts['responsive_js_composer_custom_id'];
            }
            return $class_string;
        }

        public function includes(){
            include_once( RESPONSIVE_JS_COMPOSER_PATH . '/includes/shortcodes.php' );
            include_once( RESPONSIVE_JS_COMPOSER_PATH . '/includes/helpers.php' );
            include_once( RESPONSIVE_JS_COMPOSER_PATH . '/includes/vc_fields.php' );
            include_once( RESPONSIVE_JS_COMPOSER_PATH . '/includes/shortcodes/vc_custom_heading.php' );
            include_once( RESPONSIVE_JS_COMPOSER_PATH . '/includes/shortcodes/vc_column.php' );
            include_once( RESPONSIVE_JS_COMPOSER_PATH . '/includes/shortcodes/vc_column_inner.php' );
        }

        function install_js_composer_admin_notice() {
            ?>
            <div class="error">
                <p><?php _e( 'Responsive JS_Composer is enabled but not effective. It requires WPBakery Page Builder in order to work.', 'responsive-js-composer' ); ?></p>
            </div>
            <?php
        }
    }

}
if( !function_exists('Responsive_Js_Composer')){
    function Responsive_Js_Composer(){
        new Responsive_Js_Composer();
    }
}

add_action('plugins_loaded','Responsive_Js_Composer',10);
