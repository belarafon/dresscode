<?php
add_action('wp_enqueue_scripts', function () {

    $uri = get_stylesheet_directory_uri();
    $theme = wp_get_theme();
    $version = $theme->get('Version');

    wp_enqueue_style("child-style", $uri . "/public/css/styles.css", ['nexio-main-style'], $version);
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

