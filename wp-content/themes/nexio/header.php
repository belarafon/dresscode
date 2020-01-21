<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link       https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package    WordPress
 * @subpackage Nexio
 * @since      1.0
 * @version    1.0
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
$enable_header_mobile = nexio_get_option( 'enable_header_mobile', false );
$wrapper_class        = 'page-wrapper';
$menu_sticky          = nexio_get_option( '_sticky_menu', 'smart' );
$data_body            = get_post_meta( get_the_ID(), '_custom_metabox_theme_options', true );
$bg_body              = isset( $data_body['bg_body'] ) ? $data_body['bg_body'] : '';
$css                  = '';
$a                    = wp_get_attachment_image_url( $bg_body, 'full' );

if ( $bg_body && $bg_body != '' ) {
	$css = 'background-image:url(' . wp_get_attachment_image_url( $bg_body, 'full' ) . ')';
}

$single_id = nexio_get_single_page_id();
if ( $single_id > 0 ) {
	$enable_custom_header = false;
	$meta_data            = get_post_meta( $single_id, '_custom_metabox_theme_options', true );
	if ( $enable_custom_header ) {
		$menu_sticky = $meta_data['enable_sticky_menu'];
	}
}

if ( isset( $data_body['enable_boxed_body'] ) && $data_body['enable_boxed_body'] == 1 ) {
	$wrapper_class .= ' has-boxed';
}

if ( $menu_sticky == 'normal' ) {
	$wrapper_class .= ' wrapper_menu-sticky-nomal';
} elseif ( $menu_sticky == 'smart' ) {
	$wrapper_class .= ' wrapper_menu-sticky ';
}

$sticky_info_w              = '';
$enable_info_product_single = nexio_get_option( 'enable_info_product_single', false );
if ( $enable_info_product_single ) {
	$sticky_info_w = 'sticky-info_single_wrap';
}

if ( class_exists( 'WooCommerce' ) ) {
	if ( is_product() ) {
		$product_style = nexio_get_option( 'nexio_woo_single_product_layout', 'default' );
		$product_meta  = get_post_meta( get_the_ID(), '_custom_product_metabox_theme_options', true );
		if ( isset( $product_meta['product_style'] ) ) {
			if ( trim( $product_meta['product_style'] != '' ) && $product_meta['product_style'] != 'global' ) {
				$product_style = $product_meta['product_style'];
			}
		}
		if ( $product_style == 'unique' ) {
			$wrapper_class .= ' unique-wrap ';
		}
		
	}
	$quickview_style = nexio_get_option( 'quickview_style', '' );
	$wrapper_class   .= $quickview_style;
}
?>
<?php
    $page_title       = nexio_get_option( 'page_title', '' );
    $page_breadcrumb  = nexio_get_option( 'page_breadcrumb', '' );
    $wrapper_id   = array('page-wrapper');
    $wrapper_id[] = $page_title;
    $wrapper_id[] = $page_breadcrumb;
?>
<?php do_action( 'nexio_before_page_wrapper' ); ?>

<div id="<?php echo esc_attr( implode( ' ', $wrapper_id ) ); ?>"
     class="<?php echo esc_attr( $wrapper_class ); ?> <?php echo esc_attr( $sticky_info_w ); ?>"
     style="<?php echo esc_attr( $css ) ?>">
    <div class="body-overlay"></div>
    <div class="search-canvas-overlay"></div>
    <div class="sidebar-canvas-overlay"></div>
	<?php if ( ! $enable_header_mobile || ( $enable_header_mobile && ! nexio_is_mobile() ) ) { ?>
        <div id="box-mobile-menu" class="box-mobile-menu full-height">
            <a href="javascript:void(0);" id="back-menu" class="back-menu"><i class="pe-7s-angle-left"></i></a>
            <span class="box-title"><?php echo esc_html__( 'Menu', 'nexio' ); ?></span>
            <a href="javascript:void(0);" class="close-menu"><i class="pe-7s-close"></i></a>
            <div class="box-inner"></div>
        </div>
	<?php } ?>
	<?php nexio_get_header(); ?>
	<?php
	if ( is_singular( 'product' ) ):
		do_action( 'nexio_product_toolbar' );
	endif;
	?>
    <div class="boxed-wrap">
