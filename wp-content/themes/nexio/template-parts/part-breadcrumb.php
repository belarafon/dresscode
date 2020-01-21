<?php
$args = array(
	'container'     => 'div',
	'before'        => '',
	'after'         => '',
	'show_on_front' => true,
	'network'       => false,
	'show_title'    => true,
	'show_browse'   => false,
	'post_taxonomy' => array(),
	'echo'          => true,
	'separator' => array(
        'content' => 'fwefw'
    ),
);
if ( ! is_front_page()) {
	$show_breadcrumb_trail = true;
    if ( class_exists( 'WooCommerce' ) ) {
        if ( is_woocommerce() ) {
            $show_breadcrumb_trail = true;
            $show_breadcrumb_wc    = false;
            if ( is_product() ) {
                $enable_single_product_mobile = nexio_get_option( 'enable_single_product_mobile', true );
                if ( $enable_single_product_mobile && nexio_is_mobile() ) {
                    $show_breadcrumb_wc = false;
                }
            }
            if ( $show_breadcrumb_wc ) {
                woocommerce_breadcrumb();
            }
        }
    }
    if ( $show_breadcrumb_trail ) {
        nexio_breadcrumb( $args );
    }
}