<?php
add_action('wp_enqueue_scripts', function () {

    $uri = get_stylesheet_directory_uri();
    $theme = wp_get_theme();
    $version = $theme->get('Version');

    wp_enqueue_style("child-style", $uri . "/public/css/child-styles.css", ['nexio-main-style', 'bootstrap'], $version);
    wp_enqueue_script("child-scripts", $uri . "/public/js/scripts.js", ['nexio-frontend'], $version);
});

// Add a customizer option for custom translates in theme.
function your_theme_new_customizer_settings($wp_customize) {
// add a setting for translates
$wp_customize -> add_setting('theme_translates');
// Add a control to type translates in
$wp_customize -> add_control( new WP_Customize_Image_Control( $wp_customize, 'your_theme_logo',
array(
'label' => 'Переклади',
'section' => 'title_tagline',
'settings'  => 'theme_translates',
'type'        => 'textarea',
) ) );
}
add_action('customize_register', 'your_theme_new_customizer_settings');

function getTranslates() {
    $translate_texts = get_theme_mod('theme_translates');
    $translate_trim_line = preg_split('/\r\n|\r|\n/', $translate_texts);
    $result = [];

    foreach ($translate_trim_line as $line) {
       $key_val = explode("/", $line);

       $result[strval($key_val[0])] = strval($key_val[1]);
    }
    return $result;
}

if (get_bloginfo('language') == "uk") {
    function ra_change_translate_text_multiple($translated)
    {
        $text = getTranslates();
        $translated = str_ireplace(array_keys($text), $text, $translated);
        return $translated;
    }

    add_filter('gettext', 'ra_change_translate_text_multiple', 20);
}

// Remove billing address info
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
add_filter( 'woocommerce_billing_fields' , 'custom_override_billing_fields' );

function custom_override_checkout_fields( $fields ) {
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_address_1']);
    return $fields;
}

function custom_override_billing_fields( $fields ) {
    unset($fields['billing_postcode']);
    unset($fields['billing_state']);
    unset($fields['billing_country']);
    unset($fields['billing_address_1']);
    return $fields;
}

add_action( 'woocommerce_product_meta_end', 'action_product_meta_add_gender' );
function action_product_meta_add_gender() {
    global $product;

    $term_gender = wp_get_post_terms( $product->get_id(), 'gender', array('fields' => 'ids') );

    echo get_the_term_list( $product->get_id(), 'gender', '<span class="ord-3">' . _n( 'Для кого:', 'Для кого:', count( $term_gender ), 'woocommerce' ) . ' ', ', ', '</span>' );
}

add_action( 'woocommerce_product_meta_end', 'action_product_meta_add_product_type' );
function action_product_meta_add_product_type() {
    global $product;

    $term_product_type = wp_get_post_terms( $product->get_id(), 'type_of_product', array('fields' => 'ids') );

    echo get_the_term_list( $product->get_id(), 'type_of_product', '<span class="ord-2">' . _n( 'Товар:', 'Товар:', count( $term_product_type ), 'woocommerce' ) . ' ', ', ', '</span>' );
}
