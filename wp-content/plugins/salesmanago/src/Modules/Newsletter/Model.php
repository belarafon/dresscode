<?php

namespace bhr\Modules\Newsletter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bhr\Model\AbstractModel;

class Model extends AbstractModel
{
    const EXT_NAME = 'news';
    
    public static function getDefaultConfig()
    {
        return [
            self::EXT_NAME => [
                'type' => '',
                'mappedName' => '',
                'newsletterContent' => [
                    'default' => 'Subscribe to newsletter'
                ]
            ]
        ];
    }

    public static function isFeederActive() {
        return true;
    }

    public function setNewsletterConfig($userProperties)
    {
        $config = [
            'news' => $userProperties
        ];

        $value = $this
            ->getValue()
            ->addUpdatedAtTime()
            ->extendValue(array('extensions' => $config))
            ->buildValue();

        $status = $this->setUserData($value);

        return $status;
    }

    public function getNewsletterConfig()
    {
        $userData = $this->getUserData();

        $avLangs = get_available_languages();

        $userLocal = get_user_locale();
        $userLocal = (strlen($userLocal) > 3)
            ? substr($userLocal, 3, 2)
            : strtoupper($userLocal);

        foreach ($avLangs as $key => $lang) {
            $avLangs[$key] = (strlen($lang) > 3)
                    ? substr($lang, 3, 2)
                    : strtoupper($lang);
        }

        $langs = [
            'availableLangs' => $avLangs,
            'activeLang' => (!isset($userData['extensions']['news']['newsletterContent'][$userLocal])
                || empty($userData['extensions']['news']['newsletterContent'][$userLocal]))
                ? 'default' : $userLocal
        ];

        if (isset($userData['extensions']['active']['news'])
            && $userData['extensions']['active']['news'] == true
            && isset($userData['extensions']['news'])
        ) {
            return array_merge($userData['extensions']['news'], $langs);
        } else {
            return $langs;
        }
    }

    public function getDefaultContactState($smOptIn = null)
    {
        if ($smOptIn != null) {
            return [
                'forceOptIn' => $smOptIn,
                'forceOptOut' => !$smOptIn,
                'forcePhoneOptIn' => $smOptIn,
                'forcePhoneOptOut' => !$smOptIn
            ];
        }

        return [
            'forceOptIn' => false,
            'forceOptOut' => true,
            'forcePhoneOptIn' => false,
            'forcePhoneOptOut' => true
        ];
    }
}
