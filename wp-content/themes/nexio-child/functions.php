<?php
add_action('wp_enqueue_scripts', function () {

    $uri = get_stylesheet_directory_uri();
    $theme = wp_get_theme();
    $version = $theme->get('Version');

    wp_enqueue_style("child-style", $uri . "/public/css/styles.css", ['nexio-main-style'], $version);
});

if (get_bloginfo('language') == "uk") {
    function ra_change_translate_text_multiple($translated)
    {
        $text = array(
            'Filter' => 'Фільтр',
            'Add to compare' => 'Додати до порівняння',
            'Share' => 'Поширити',
            'Read more' => 'Читати далі'
        );
        $translated = str_ireplace(array_keys($text), $text, $translated);
        return $translated;
    }

    add_filter('gettext', 'ra_change_translate_text_multiple', 20);
}