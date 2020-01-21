<?php

namespace bhr\Modules\WooCommerce;

use bhr\Model\HooksModel as GlobalHooksModel;
use bhr\Model\StoreTrait;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class HooksModel extends GlobalHooksModel
{
	use StoreTrait;

    public function getTags($const = null)
    {
        $userData = $this->getUserData();

        if ($const !== null) {
            return $userData['extensions'][Model::EXT_NAME][self::TAGS][$const];
        }

        return $userData['extensions'][Model::EXT_NAME][self::TAGS];
    }

    public function getWooCommerceCartUrl()
    {
        $userData = $this->getUserData();

        $url = $userData['extensions']['wc']['cartUrl'];

        if ($userData['extensions']['wc']['cartUrl'][0] == '/'
            && $userData['extensions']['wc']['cartUrl'][1] != '/'
        ) {
	        $url = substr($userData['extensions']['wc']['cartUrl'], 1);
        }

        return (wc_get_page_permalink($url))
	        ? (wc_get_page_permalink($url))
	        : $userData['extensions'][Model::EXT_NAME]['domain'] . $url;
    }

    public function getNewsletterCheckbox()
    {
        $userData = $this->getUserData();
        return $userData['newsletter'];
    }
}
