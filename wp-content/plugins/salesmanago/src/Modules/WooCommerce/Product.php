<?php

namespace bhr\Modules\WooCommerce;

use \DateTime as DateTime;
use bhr\Modules\Crypto;

class Product
{
    private $single;

    public function __construct()
    {
        $this->single = true;
    }

    public function getProductsFromOrder($order, $woocommerce)
    {
        global $single;

        $dt            = new DateTime('NOW');
        $items         = $order->get_items();
        $paymentMethod = '_payment_method_title';
        $paymentMethod = get_post_meta($order->get_id(), $paymentMethod, $single);

        $productsId   = [];
        $productsName = [];

        foreach ($items as $product) {
            $productsId[]   = $product['product_id'];
            $productsName[] = $product['name'];
        }

        $product = array(
            'date'        => $dt->format('c'),
            'description' => $paymentMethod,
            'products'    => implode(',', $productsId),
            'location'    => add_query_arg($woocommerce->query_string, '', home_url($woocommerce->request)),
            'value'       => $order->get_total(),
            'detail1'     => implode(',', $productsName),
            'detail2'     => $order->get_order_key(),
            'detail3'     => $order->get_customer_note(),
            'externalId'  => $order->get_id()
        );

        return $product;
    }

    public function getProductFromCart(\WooCommerce $woocommerce)
    {
        $productsId       = [];
        $productsName     = [];
        $productsQuantity = [];
        $recoveryCartObj  = [];
        $description      = "";
        $cartTotalPrice   = 0;

        $dt   = new DateTime('NOW');
        $cart = $woocommerce->cart->get_cart();

        foreach ($cart as $product) {
            //if recovered cart add description
            if (in_array(['cart_recover' => true], $product)) {
                $description = "Recovered cart";
            }
            $productsId[]      = $product['product_id'];
            $productsName[]    = $product['data']->name;
            $cartTotalPrice    = $cartTotalPrice + ($product['quantity'] * $product['data']->price);
            $recoveryCartObj[] = [
                'product_id'   => $product['product_id'],
                'quantity'     => $product['quantity'],
                'variation_id' => $product['variation_id'],
                'variation'    => $product['variation']
            ];
            $productsQuantity[] = $product['quantity'];
        }

        // url construction
        $recoveryCartUrl = add_query_arg($woocommerce->query_string, '', home_url($woocommerce->request)) . "/wp-json/salesmanago/v1/recover?";
        $recoveryCartUrl .= "cart=" . Crypto::encrypt($recoveryCartObj, true);

        $products = array(
            'date'        => $dt->format('c'),
            'description' => $description,
            'products'    => implode(',', $productsId),
            'location'    => add_query_arg(
                $woocommerce->query_string,
                '',
                home_url($woocommerce->request)
            ),
            'value'   => $cartTotalPrice,
            'detail1' => implode(',', $productsName),
            'detail2' => $recoveryCartUrl,
            'detail3' => implode('/', $productsQuantity),
        );

        return $products;
    }
}