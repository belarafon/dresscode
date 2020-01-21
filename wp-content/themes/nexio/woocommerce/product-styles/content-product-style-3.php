<?php
/*
     Name: Product style 3
     Slug: content-product-style-3
*/

$args = isset($args) ? $args : null;
?>
<div class="product-inner vertical-tooltip">
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
        <div class="button-loop-action">
            <?php
            do_action('fami_wccp_shop_loop');
            do_action('nexio_function_shop_loop_item_compare');
            do_action('nexio_function_shop_loop_item_wishlist');
            do_action('nexio_function_shop_loop_item_quickview');
            ?>
        </div>
    </div>
    <div class="product-info equal-elem">
        <?php
        do_action( 'woocommerce_shop_loop_item_title' );
        
        /**
         * woocommerce_after_shop_loop_item_title hook.
         *
         * @hooked woocommerce_template_loop_rating - 5
         * @hooked woocommerce_template_loop_price - 10
         */
        do_action('woocommerce_after_shop_loop_item_title');
        ?>
        <div class="add-to-cart">
	        <?php do_action( 'nexio_loop_add_to_cart_btn' ); ?>
        </div>
    </div>
</div>
