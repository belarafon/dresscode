<?php
/*
     Name: Product style 01
     Slug: content-product-style-01
*/

$args = isset($args) ? $args : null;
remove_action( 'woocommerce_before_shop_loop_item_title', 'nexio_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'nexio_group_flash', 5 );
add_action( 'woocommerce_before_shop_loop_item_title', 'nexio_gallery_product_thumbnail', 10, 1 );
?>
<div class="product-inner" data-items="2">
    <div class="product-thumb">
        <?php
        /**
         * woocommerce_before_shop_loop_item_title hook.
         *
         * @hooked woocommerce_show_product_loop_sale_flash - 10
         * @hooked woocommerce_template_loop_product_thumbnail - 10
         */
        do_action('woocommerce_before_shop_loop_item_title', $args);
        ?>
    </div>
    <div class="product-info">
        <h2 class="deal-title"><?php echo esc_html__('Deal of the day.', 'nexio') ?></h2>
        <?php
        /**
         * woocommerce_after_shop_loop_item_title hook.
         *
         * @hooked woocommerce_template_loop_rating - 5
         * @hooked woocommerce_template_loop_price - 10
         */
        do_action( 'woocommerce_shop_loop_item_title' );
        do_action('woocommerce_after_shop_loop_item_title');
        do_action('nexio_function_shop_loop_item_countdown');
        ?>
    </div>
</div>
<?php
add_action( 'woocommerce_before_shop_loop_item_title', 'nexio_template_loop_product_thumbnail', 10 );
add_action( 'woocommerce_before_shop_loop_item_title', 'nexio_group_flash', 5 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'nexio_gallery_product_thumbnail', 10 );