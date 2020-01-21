<?php

namespace bhr\Modules\Wordpress;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bhr\Model\AbstractModel;

class Model extends AbstractModel
{
    const EXT_NAME = 'wp';

    public static function getDefaultConfig()
    {
        return [
            self::EXT_NAME => [
                'tags' => [
                    'registration' => "wp_register",
                    'login'        => "wp_login",
	                'newsletter'   => "wp_newsletter"
                ]
            ]
        ];
    }

    public static function isFeederActive()
    {
        return true;
    }

    public function getConfig()
    {
        $userData = $this->getUserData();

        return $userData['extensions']['wp'];
    }

    public function setConfig($userProperties)
    {
        $value = $this
            ->getValue()
            ->addUpdatedAtTime()
            ->extendValue(array('extensions' => $userProperties))
            ->buildValue();

        $status = $this->setUserData($value);

        return $status;
    }
}