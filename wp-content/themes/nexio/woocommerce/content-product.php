<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;
// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$nexio_woo_product_style = nexio_get_option( 'nexio_shop_product_style', 1 );
$enable_products_sizes   = nexio_get_option( 'enable_products_sizes', false );
/*
 * 5 items: col-bg-15 col-lg-15 col-md-15 col-sm-3 col-xs-4 col-ts-6
 * 4 items: col-bg-3 col-lg-3 col-md-4 col-sm-4 col-xs-6 col-ts-12
 * 3 items: col-bg-4 col-lg-4 col-md-6 col-sm-6 col-xs-6 col-ts-12
 */
$nexio_woo_bg_items = 3;     // 15
$nexio_woo_lg_items = 3;     // 15
$nexio_woo_md_items = 4;     // 15
$nexio_woo_sm_items = 6;     // 3
$nexio_woo_xs_items = 6;     // 4
$nexio_woo_ts_items = 12;    // 6

$enable_single_product_mobile = nexio_get_option( 'enable_single_product_mobile', true );
if ( $enable_single_product_mobile && nexio_is_mobile() ) {
	$nexio_woo_bg_items      = 15;     // 15
	$nexio_woo_lg_items      = 15;     // 15
	$nexio_woo_md_items      = 15;     // 15
	$nexio_woo_sm_items      = 3;      // 3
	$nexio_woo_xs_items      = 4;      // 4
	$nexio_woo_ts_items      = 6;      // 6
	$nexio_woo_product_style = 1;      // Always use product style 1 on real mobile
}

// Custom columns
if ( ! $enable_products_sizes ) {
	$nexio_woo_bg_items = nexio_get_option( 'nexio_woo_bg_items', 3 );
	$nexio_woo_lg_items = nexio_get_option( 'nexio_woo_lg_items', 3 );
	$nexio_woo_md_items = nexio_get_option( 'nexio_woo_md_items', 4 );
	$nexio_woo_sm_items = nexio_get_option( 'nexio_woo_sm_items', 4 );
	$nexio_woo_xs_items = nexio_get_option( 'nexio_woo_xs_items', 6 );
	$nexio_woo_ts_items = nexio_get_option( 'nexio_woo_ts_items', 6 );
}
$animate_class = 'famiau-wow-continuous nexio-wow fadeInUp';
$classes[]     = 'product-item';
$classes[]     = 'rows-space-40';
$classes[]     = 'col-bg-' . $nexio_woo_bg_items;
$classes[]     = 'col-lg-' . $nexio_woo_lg_items;
$classes[]     = 'col-md-' . $nexio_woo_md_items;
$classes[]     = 'col-sm-' . $nexio_woo_sm_items;
$classes[]     = 'col-xs-' . $nexio_woo_xs_items;
$classes[]     = 'col-ts-' . $nexio_woo_ts_items;
$classes[]     = $animate_class;

$template_style    = 'style-' . $nexio_woo_product_style;
$classes[]         = 'style-' . $nexio_woo_product_style;
$shop_display_mode = 'grid';
if ( $shop_display_mode == "list" ) {
	$classes[] = 'style-1';
}
?>

<li <?php post_class( $classes ); ?>>
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );
	
	if ( $shop_display_mode == "list" ) {
		wc_get_template_part( 'product-styles/content-product', 'style-1' );
	} else {
		wc_get_template_part( 'product-styles/content-product', $template_style );
	}
	
	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 * @hooked woocommerce_template_loop_add_to_cart - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item' );
	?>
</li>
