<?php

namespace bhr\Modules\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bhr\Helper\Contact\Contact;
use bhr\Helper\Contact\Address;

class ContactModel
{
	const
		NICK_N = 'nickname',
		F_NAME = 'first_name',
		L_NAME = 'last_name',
		DESC   = 'description',
		LOCALE = 'locale',

		B_F_NAME    = 'billing_first_name',
		B_L_NAME    = 'billing_last_name',
		B_CITY      = 'billing_city',
		B_COMPANY   = 'billing_company',
		B_PHONE     = 'billing_phone',
		B_POSTCODE  = 'billing_postcode',
		B_STREET    = 'billing_address_1',
		B_STREET_NR = 'billing_address_2',
		B_COUNTRY   = 'billing_country',

		P_NO_ACC_EMAIL     = '_billing_email',
		P_NO_ACC_COMPANY   = '_billing_company',
		P_NO_ACC_F_NAME    = '_billing_first_name',
		P_NO_ACC_L_NAME    = '_billing_last_name',
		P_NO_ACC_PHONE     = '_billing_phone',
		P_NO_ACC_ADDRESS_1 = '_billing_address_1',
		P_NO_ACC_ADDRESS_2 = '_billing_address_2',
		P_NO_ACC_POSTCODE  = '_billing_postcode',
		P_NO_ACC_CITY      = '_billing_city',
		P_NO_ACC_COUNTRY   = '_billing_country';

    private $single;

    public function __construct()
    {
        $this->single = true;
    }

    public function get($uId, $getType = 'id', $user_login = null)
    {
	    switch ( $getType ) {
		    case 'login':
			    $currentUser = get_user_by( 'login', $user_login );
			    $email       = $currentUser->user_email;
			    break;
		    default:
			    $currentUser = \WP_User::get_data_by( 'id', $uId );

			    if ( ! $currentUser ) {
				    return false;
			    }

			    $email = $currentUser->user_email;
			    break;
	    }

	    $id = $currentUser->ID;
	    $single = $this->single;

	    $contFirstName = get_user_meta( $id, self::B_F_NAME, $single );
	    $contLastName  = get_user_meta( $id, self::B_L_NAME, $single );

	    if ( isset( $_REQUEST['action'] )
	         && $_REQUEST['action'] == 'save_account_details'
	    ) {
		    $contFirstName = $_REQUEST['account_first_name'];
		    $contLastName  = $_REQUEST['account_last_name'];
	    }

	    $Contact = new Contact(
		    $email,
		    '',
		    array( $contFirstName, $contLastName ),
		    get_user_meta( $id, self::B_PHONE, $single ),
		    '',
		    get_user_meta( $id, self::B_COMPANY, $single ),
		    '',
		    ''
	    );

	    $Contact->setAddress(
		    new Address(
			    array(
				    get_user_meta( $id, self::B_STREET, $single ),
				    get_user_meta( $id, self::B_STREET_NR, $single )
			    ),
			    get_user_meta( $id, self::B_POSTCODE, $single ),
			    get_user_meta( $id, self::B_CITY, $single ),
			    get_user_meta( $id, self::B_COUNTRY, $single )
		    )
	    );

        return $Contact;
    }

    public function getPurchaseNoAccount($purchaseOrderId)
    {
        $single = $this->single;

        $Contact = new Contact(
	        get_post_meta( $purchaseOrderId, self::P_NO_ACC_EMAIL, $single ),
	        '',
	        array(
		        get_post_meta( $purchaseOrderId, self::P_NO_ACC_F_NAME, $single ),
		        get_post_meta( $purchaseOrderId, self::P_NO_ACC_L_NAME, $single )
	        ),
	        get_post_meta( $purchaseOrderId, self::P_NO_ACC_PHONE, $single ),
	        '',
	        get_post_meta( $purchaseOrderId, self::P_NO_ACC_COMPANY, $single )
        );

        $Contact->setAddress(
	        new Address(
		        array(
			        get_post_meta( $purchaseOrderId, self::P_NO_ACC_ADDRESS_1, $single ),
			        get_post_meta( $purchaseOrderId, self::P_NO_ACC_ADDRESS_2, $single )
		        ),
		        get_post_meta( $purchaseOrderId, self::P_NO_ACC_POSTCODE, $single ),
		        get_post_meta( $purchaseOrderId, self::P_NO_ACC_CITY, $single ),
		        get_post_meta( $purchaseOrderId, self::P_NO_ACC_COUNTRY, $single )
	        )
        );

        return $Contact;
    }

    public function getPurchaseNoAccountFromPost($post)
    {
        if (!isset($post['billing_email'])
            && empty($post['billing_email'])
        ) {
            return false;
        }

        $Contact = new Contact(
	        $post['billing_email'],
	        '',
	        array(
	        	$post['billing_first_name'],
		        $post['billing_last_name']
	        ),
	        $post['billing_phone'],
	        '',
	        $post['billing_company'],
	        '',
	        ''
        );

        $Contact->setAddress(
        	new Address(
		        array(
			        $post['shipping_address_1'],
			        $post['shipping_address_2']
		        ),
		        $post['shipping_postcode'],
		        $post['shipping_city'],
		        $post['shipping_country']
	        )
        );

        return $Contact;
    }
}
