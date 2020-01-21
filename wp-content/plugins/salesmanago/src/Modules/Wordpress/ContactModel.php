<?php

namespace bhr\Modules\Wordpress;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bhr\Helper\Contact\Contact;

class ContactModel
{
	const
        NICK_N = 'nickname',
        F_NAME = 'first_name',
        L_NAME = 'last_name',
        DESC   = 'description',
        LOCALE = 'locale';

    private $single;

    public function __construct()
    {
        $this->single = true;
    }

    /**
     * @return Contact $Contact
    */
    public function get($uId, $getType = 'id', $user_login = null)
    {
        switch ($getType) {
            case 'login':
                $currentUser = get_user_by('login', $user_login);
                $email = $currentUser->user_email;
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

        $name = array(
            get_user_meta($id, self::F_NAME, $single),
            get_user_meta($id, self::L_NAME, $single)
        );

	    if (empty($name)
	        && empty($name)
	    ) {
		    $name = array(get_user_meta($id, self::NICK_N, $single));
	    }

        if (isset($_REQUEST['action'])
            && $_REQUEST['action'] == 'save_account_details'
        ) {
            $name = empty($name)
                ? array_merge(
                    $name,
                    array(
                        $_REQUEST['account_first_name']),
                    $_REQUEST['account_last_name']
                )
                : $name;
        }

        $Contact = new Contact(
            $email,
            '',
            $name
        );

        return $Contact;
    }
}
