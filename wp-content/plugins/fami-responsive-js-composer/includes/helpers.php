<?php
if ( ! function_exists( 'responsive_js_composer_shortcode_replace_post_callback' ) ) {

    function responsive_js_composer_shortcode_replace_post_callback( $matches ) {
        // Generate a random string to use as element ID.
        $id = 'responsive_js_composer_custom_css_' . mt_rand();
        return $matches[1] . '="' . $id . '"';
    }
}

if( !function_exists('responsive_js_composer_shortcode_print_inline_css') ){
    function responsive_js_composer_shortcode_print_inline_css() {
        // Get all custom inline CSS.
        if ( is_singular()  ) {
            $post_custom_css = get_post_meta( get_the_ID(), RESPONSIVE_JS_COMPOSER_METAKEY, true );
            $inline_css[] = $post_custom_css;
            $inline_css = apply_filters( 'responsive_js_composer_inline_css', $inline_css );
            if ( count( $inline_css ) ) {
                echo '<style  type="text/css">' . trim( implode( ' ', $inline_css ) ) . "</style>\n";
            }
        }


    }
}

add_action( 'wp_head', 'responsive_js_composer_shortcode_print_inline_css', 99999 );

if( !function_exists('responsive_js_composer_get_string_between')){
    function responsive_js_composer_get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}
