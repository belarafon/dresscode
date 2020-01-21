<?php
/*
     Name: Product style 02
     Slug: content-product-style-02
*/

$args = isset($args) ? $args : null;
remove_action( 'woocommerce_before_shop_loop_item_title', 'nexio_group_flash', 5 );
?>
<div class="product-inner">
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
        <?php
        do_action( 'woocommerce_shop_loop_item_title' );
        /**
         * woocommerce_after_shop_loop_item_title hook.
         *
         * @hooked woocommerce_template_loop_rating - 5
         * @hooked woocommerce_template_loop_price - 10
         */
        do_action('woocommerce_after_shop_loop_item_title');
        do_action('nexio_function_shop_loop_item_countdown');
        ?>
    </div>
</div>
<?php
add_action( 'woocommerce_before_shop_loop_item_title', 'nexio_group_flash', 5 );