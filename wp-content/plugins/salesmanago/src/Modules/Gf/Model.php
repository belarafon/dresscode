<?php

namespace bhr\Modules\Gf;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use \bhr\Model\AbstractModel;
use \RGFormsModel;

class Model extends AbstractModel
{
    const
        EXT_NAME = 'gf',
        CONTACT_PREFIX = 'sm-',
        FORCE_OPTIN = 'forceOptIn',
        CONF = 'confirmation',
        CONF_DOUBLE = 'double',
        OPTIN_FIELD = 'optin',
        PROPERTIES_ARR = 'properties',
        CONTACT_ARR = 'contact',
        OPTIONS_ARR = 'options',
        LASTNAME_FIELD = 'lastname',
        ADDRESS2_FIELD = 'address2',
        WP_PLUGIN = 'gravityforms/gravityforms.php';

    public static function getDefaultConfig()
    {
        return [
            self::EXT_NAME => [
                'confirmation' => [
                    'double' => '',
                    'template' => '',
                    'email' => '',
                    'topic' => '',
                ],
                'forms' => [],
                'form' => [],
                'users' => [],
                'forceOptIn' => ''
            ]
        ];
    }

    public static function isFeederActive()
    {
        $active = in_array(
            self::WP_PLUGIN,
            apply_filters(
                'active_plugins',
                get_option('active_plugins')
            )
        );

        if (get_site_option('active_sitewide_plugins')) {
            $active = (!$active)
                ? array_key_exists(
                    self::WP_PLUGIN,
                    get_site_option('active_sitewide_plugins')
                )
                : $active;
        }

        return $active;
    }

    public function getConfig()
    {
        $userData = $this->getUserData();
        $userData['extensions'][self::EXT_NAME]['forms'] = $this->getGfFormsData();
        $config = $userData['extensions'][self::EXT_NAME];

        ($config['forceOptIn'] == "false" || empty($config['forceOptIn']))
            ? $config['forceOptIn'] = false
            : $config['forceOptIn'] = true;
        ($config['confirmation']['double'] == "false" || empty($config['confirmation']['double']))
            ? $config['confirmation']['double'] = false
            : $config['confirmation']['double'] = true;

        return $config;
    }

    public function setConfig($userProperties)
    {
        if (!isset($userProperties['form'])) {
            $userProperties['form'] = array();
        }

        $value = $this
            ->getValue()
            ->addUpdatedAtTime()
            ->updateExtension(self::EXT_NAME, $userProperties)
            ->buildValue();

        $status = $this->setUserData($value);

        return $status;
    }

    private function updateExtension($key, $value)
    {
        $this->userValue['extensions'][$key] = $value;
        return $this;
    }

    private function getGfFormsData()
    {
        $formsData = array();
        if ($formsList = RGFormsModel::get_forms()) {
            foreach ($formsList as $form) {
                $formsData[] = ['id' => $form->id, 'title' => $form->title];
            }
        }

        return $formsData;
    }
}
