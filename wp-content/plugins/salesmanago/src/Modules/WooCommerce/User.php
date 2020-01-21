<?php

namespace bhr\Modules\WooCommerce;

class User
{
    private $single;

    public function __construct()
    {
        $this->single = true;
    }

    public function getUser($uId, $getType = 'id', $user_login = null)
    {
        switch ($getType) {
            case 'login':
                $currentUser = get_user_by('login', $user_login);
                $email       = $currentUser->user_email;
                break;
            default:
                $currentUser = \WP_User::get_data_by('id', $uId);

                if (!$currentUser) {
                    return false;
                }

                $email = $currentUser->user_email;
                break;
        }

        $id = $currentUser->ID;

        $single = $this->single;

        $firstName           = 'billing_first_name';
        $lastName            = 'billing_last_name';
        $billingCity         = 'billing_city';
        $billingCompany      = 'billing_company';
        $billingPhone        = 'billing_phone';
        $billingPostcode     = 'billing_postcode';
        $billingStreet       = 'billing_address_1';
        $billingStreetNumber = 'billing_address_2';
        $billingCountry      = 'billing_country';

        $userName = get_user_meta($id, $firstName, $single);
        $userName .= " ";
        $userName .= get_user_meta($id, $lastName, $single);

        $userStreetAddress = get_user_meta($id, $billingStreet, $single);
        $userStreetAddress .= " ";
        $userStreetAddress .= get_user_meta($id, $billingStreetNumber, $single);

        if (isset($_REQUEST['action'])
            && $_REQUEST['action'] == 'save_account_details'
        ) {
            $userName = $_REQUEST['account_first_name'];
            $userName .= empty($userName)
                ? $_REQUEST['account_last_name']
                : " " . $_REQUEST['account_last_name'];
        }

        $user = array(
            'name'    => $userName,
            'email'   => $email,
            'company' => get_user_meta($id, $billingCompany, $single),
            'phone'   => get_user_meta($id, $billingPhone, $single),
            "address" => array(
                'zipCode'       => get_user_meta($id, $billingPostcode, $single),
                'streetAddress' => $userStreetAddress,
                'city'          => get_user_meta($id, $billingCity, $single),
                'country'       => get_user_meta($id, $billingCountry, $single)
            )
        );

        return $user;
    }

    public function getPurchaseNoAccount($id)
    {
        $single                     = $this->single;
        $purchaseOrderId            = $id;
        $purchaseNoAccountEmail     = '_billing_email';
        $purchaseNoAccountCompany   = '_billing_company';
        $purchaseNoAccountFirstName = '_billing_first_name';
        $purchaseNoAccountLastName  = '_billing_last_name';
        $purchaseNoAccountPhone     = '_billing_phone';
        $purchaseNoAccountAddress1  = '_billing_address_1';
        $purchaseNoAccountAddress2  = '_billing_address_2';
        $purchaseNoAccountPostcode  = '_billing_postcode';
        $purchaseNoAccountCity      = '_billing_city';
        $purchaseNoAccountCountry   = '_billing_country';

        $userName = get_post_meta($purchaseOrderId, $purchaseNoAccountFirstName, $single);
        $userName .= " ";
        $userName .= get_post_meta($purchaseOrderId, $purchaseNoAccountLastName, $single);

        $userAddress = get_post_meta($purchaseOrderId, $purchaseNoAccountAddress1, $single);
        $userAddress .= " ";
        $userAddress .= get_post_meta($purchaseOrderId, $purchaseNoAccountAddress2, $single);

        $user = array(
            'email'   => get_post_meta($purchaseOrderId, $purchaseNoAccountEmail, $single),
            'name'    => $userName,
            'company' => get_post_meta($purchaseOrderId, $purchaseNoAccountCompany, $single),
            'phone'   => get_post_meta($purchaseOrderId, $purchaseNoAccountPhone, $single),
            "address" => array(
                'zipCode'       => get_post_meta($purchaseOrderId, $purchaseNoAccountPostcode, $single),
                'streetAddress' => $userAddress,
                'city'          => get_post_meta($purchaseOrderId, $purchaseNoAccountCity, $single),
                'country'       => get_post_meta($purchaseOrderId, $purchaseNoAccountCountry, $single)
            )
        );

        return $user;
    }

    public function getPurchaseNoAccountFromPost($post)
    {
        if (!isset($post['billing_email'])
            && empty($post['billing_email'])
        ) {
            return false;
        }

        $name = (
            !empty(trim($post['billing_first_name']))
            && !empty(trim($post['billing_last_name']))
        )
            ? $post['billing_first_name']." ". $post['billing_last_name']
            : $post['billing_first_name'].$post['billing_last_name'];

        $user = array(
            'email'   => $post['billing_email'],
            'name'    => $name,
            'company' => $post['billing_company'],
            'phone'   => $post['billing_phone'],
            "address" => array(
                'zipCode'       => $post['shipping_postcode'],
                'streetAddress' => (empty(trim($post['shipping_address_2'])))
                    ? $post['shipping_address_1']
                    : $post['shipping_address_1']." ".$post['shipping_address_2'],
                'city'          => $post['shipping_city'],
                'country'       => $post['shipping_country']
            )
        );

        return $user;
    }
}
