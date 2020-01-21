<?php get_header();

$enable_single_product_mobile = nexio_get_option( 'enable_single_product_mobile', true );

/* Shop layout */
$nexio_woo_shop_layout  = nexio_get_option( 'sidebar_shop_page_position', 'left' );
$nexio_woo_shop_sidebar = nexio_get_option( 'shop_page_sidebar', 'shop-widget-area' );
$filter_shop_page      = nexio_get_option( 'filter_shop_page', 'top_sidebar' );
if ( is_product() ) {
	$nexio_woo_shop_layout  = nexio_get_option( 'sidebar_product_position', 'left' );
	$nexio_woo_shop_sidebar = nexio_get_option( 'single_product_sidebar', 'product-widget-area' );
}

// Always full width on real mobile
if ( $enable_single_product_mobile && nexio_is_mobile() ) {
	$nexio_woo_shop_layout = 'full';
}

if ( ! is_active_sidebar( $nexio_woo_shop_sidebar ) ) {
	$nexio_woo_shop_layout = 'full';
}

/* Main container class */
$main_container_class   = array();
$main_container_class[] = 'main-container shop-page';
$page_banner_type       = nexio_get_option( 'shop_banner_type', 'no_background' );

$enable_categories    = nexio_get_option( 'shop_panel', false );
$style_categories     = nexio_get_option( 'style-categories', 'cate-image' );
$list_categories      = nexio_get_option( 'panel-categories', array() );

if ( is_shop() || is_product_category() || is_product_tag() ) {
	if ( $page_banner_type == 'has_background' ) {
		$main_container_class[] = 'shop-bg';
	}
	if( $list_categories && $enable_categories && $style_categories) {
		$main_container_class[] = 'has-panel-categories';
	}
}

if ( $nexio_woo_shop_layout == 'full' ) {
	$main_container_class[] = 'no-sidebar';
} else {
	$main_container_class[] = $nexio_woo_shop_layout . '-sidebar';
}

/* Setting single product */
$main_content_class   = array();
$main_content_class[] = 'main-content';
$main_widget_class    = array();
$main_widget_class[]  = 'widget-shop-wrap';
$main_product_wrap    = array();
$main_product_wrap[]  = 'main-product-wrap';

if ( $filter_shop_page == 'drawer_sidebar' ) {
	$main_widget_class[] = 'drawer_sidebar_elem';
	$main_product_wrap[] = 'drawer_sidebar_elem';
}

if ( $nexio_woo_shop_layout == 'full' ) {
	$main_content_class[] = 'col-sm-12';
} else {
	$main_content_class[] = 'col-lg-9 col-md-8 col-sm-12 has-sidebar';
}

$sidebar_class   = array();
$sidebar_class[] = 'sidebar';
if ( ( $nexio_woo_shop_layout == 'left' ) || ( $nexio_woo_shop_layout == 'right' ) ) {
	$sidebar_class[] = 'col-lg-3 col-md-4 col-sm-12 sidebar-' . $nexio_woo_shop_layout;
}

$product_inner_class[] = 'nexio-single-container';
$enable_extend_sidebar = nexio_get_option( 'enable_extend_single_product', false );
$product_style         = nexio_get_option( 'nexio_woo_single_product_layout', 'default' );
$product_meta = get_post_meta(get_the_ID(), '_custom_product_metabox_theme_options', true);
if (isset($product_meta['product_style'])) {
	$product_style = $product_meta['product_style'];
}
if ( $enable_extend_sidebar ) {
	$product_inner_class[] = 'container-extend';
}
if ( $product_style == 'slider_large' || $product_style == 'center_slider' ) {
	$product_inner_class[] = 'container-extend-large';
}
?>
    <div class="<?php echo esc_attr( implode( ' ', $main_container_class ) ); ?>">
		<?php if ( ! is_single() ) { ?>
		<?php }else{ ?>
        <div class="<?php echo esc_attr( implode( ' ', $product_inner_class ) ); ?>">
			<?php }; ?>
	        <?php
	        /**
	         * nexio_before_shop_loop hook.
	         *
	         * @hooked nexio_shop_top_control - 10
	         */
	        do_action( 'nexio_before_shop_loop' );
	        ?>
            <div class="container">
                <div class="row <?php echo esc_attr( $filter_shop_page ); ?>">
                    <div class="<?php echo esc_attr( implode( ' ', $main_content_class ) ); ?>">
                        <div class="<?php echo esc_attr( implode( ' ', $main_product_wrap ) ); ?>">
							<?php
							/**
							 * nexio_woocommerce_before_main_content hook
							 */
							do_action( 'nexio_woocommerce_before_main_content' );
							?>
                            <div class="main-product">
								<?php

								woocommerce_content();

								?>
                            </div> <!-- End .main-product-->
                        </div>
                    </div>
					<?php if ( ! is_product() && ( $nexio_woo_shop_layout == 'full' ) && ( $filter_shop_page == 'drawer_sidebar' || $filter_shop_page == 'offcanvas_sidebar' ) ): ?>
						<?php if ( is_active_sidebar( $nexio_woo_shop_sidebar ) ) : ?>
                            <div class="<?php echo esc_attr( implode( ' ', $main_widget_class ) ); ?>">
                                <div class="widget-shop-inner">
                                    <div id="widget-area" class="widget-area shop-sidebar">
										<?php dynamic_sidebar( $nexio_woo_shop_sidebar ); ?>
                                    </div><!-- .widget-area -->
                                </div>
                            </div>
                            <div class="main-widget-overlay"></div>
						<?php endif; ?>
					<?php endif; ?>
					<?php if ( ( $nexio_woo_shop_layout == 'left' ) || ( $nexio_woo_shop_layout == 'right' ) ): ?>
                        <div class="<?php echo esc_attr( implode( ' ', $sidebar_class ) ); ?>">
							<?php if ( is_active_sidebar( $nexio_woo_shop_sidebar ) ) : ?>
                                <div id="widget-area" class="widget-area shop-sidebar">
									<?php dynamic_sidebar( $nexio_woo_shop_sidebar ); ?>
                                </div><!-- .widget-area -->
							<?php endif; ?>
                        </div>
					<?php endif; ?>
                </div>
            </div>
			<?php if ( ! is_single() ) { ?>
			<?php }else{ ?>
        </div>
	<?php }; ?>
    </div>
<?php get_footer(); ?>