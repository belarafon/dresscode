<?php
/**
 * The sidebar containing the main widget area
 *
 */
?>
<?php

$nexio_woo_shop_used_sidebar = nexio_get_option( 'shop_page_sidebar', 'shop-widget-area' );
if( is_product() ){
    $nexio_woo_shop_used_sidebar = nexio_get_option('single_product_sidebar','product-widget-area');
}
?>

<?php if ( is_active_sidebar( $nexio_woo_shop_used_sidebar ) ) : ?>
    <div id="widget-area" class="widget-area shop-sidebar">
        <?php dynamic_sidebar( $nexio_woo_shop_used_sidebar ); ?>
    </div><!-- .widget-area -->
<?php endif; ?>
