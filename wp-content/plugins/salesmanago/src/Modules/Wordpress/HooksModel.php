<?php

namespace bhr\Modules\Wordpress;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bhr\Model\HooksModel as GlobalHooks;

class HooksModel extends GlobalHooks
{
    const
        TAGS       = 'tags',
        T_NEWS     = 'newsletter',
        T_REGISTER = 'registration',
        T_LOGIN    = 'login';

    public function getTags($const = null)
    {
        $userData = $this->getUserData();

        if ($const !== null) {
            return $userData['extensions'][Model::EXT_NAME][self::TAGS][$const];
        }

        return $userData['extensions'][Model::EXT_NAME][self::TAGS];
    }

    public function getNewsletterCheckbox()
    {
        $userData = $this->getUserData();
        return $userData['newsletter'];
    }
}
