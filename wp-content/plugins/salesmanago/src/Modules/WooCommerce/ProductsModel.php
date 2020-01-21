<?php

namespace bhr\Modules\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use \DateTime as DateTime;
use \bhr\Helper\Crypto;

use bhr\Modules\WooCommerce\HooksModel;

class ProductsModel
{
    private $single;
    private $HooksModel;

    public function __construct(HooksModel $model)
    {
        $this->single = true;
        $this->HooksModel = $model;
    }

    public function getProductsFromOrder($order)
    {
        $dt            = new DateTime('NOW');
        $items         = $order->get_items();

        $productsId   = [];
        $productsName = [];

        foreach ($items as $product) {
            $productsId[]   = $product['product_id'];
            $productsName[] = $product['name'];
            $productsQuantity[] = $product->get_quantity();
        }

        $product = array(
            'date'        => $dt->format('c'),
            'description' => $order->get_payment_method(),
            'products'    => implode(',', $productsId),
            'location'    => $this->HooksModel->getStoreId(get_home_url()),
            'value'       => $order->get_total(),
            'detail1'     => implode(',', $productsName),
            'detail2'     => $order->get_order_key(),
            'detail3'     => implode('/', $productsQuantity),
            'detail4'     => $order->get_customer_note(),
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

            $id = $product['data']->get_id();
            $WcProduct = wc_get_product($id);

            $productsId[]      = $WcProduct->get_id();
            $productsName[]    = $WcProduct->get_name();
            $cartTotalPrice    = $cartTotalPrice + ($product['quantity'] * $WcProduct->get_price());
            $recoveryCartObj[] = [
                'product_id'   => $WcProduct->get_id(),
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
            'location'    => $this->HooksModel->getStoreId(get_home_url()),
            'value'   => $cartTotalPrice,
            'detail1' => implode(',', $productsName),
            'detail2' => $recoveryCartUrl,
            'detail3' => implode('/', $productsQuantity),
        );

        return $products;
    }

    public function getProductFromCartAsPurchase(\WooCommerce $woocommerce)
    {
        $products = [];
        $cartProducts = $this->getProductFromCart($woocommerce);

        $products['description'] = $_POST['payment_method'];
        /*$products['detail2'] = ''//order key;*/
        $products['detail4'] = (isset($products['detail2'])) ? $products['detail2'] : '';
        $products['detail5'] = 'prepurchase';
        $products['externalId'] = '';
        $products['shopDomain'] = get_home_url();

        $cartProducts = array_merge($cartProducts, $products);

        return $cartProducts;
    }
}
