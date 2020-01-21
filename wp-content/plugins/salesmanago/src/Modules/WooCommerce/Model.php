<?php

namespace bhr\Modules\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bhr\Model\AbstractModel;


class Model extends AbstractModel
{
    const
        EXT_NAME = 'wc',
        WP_PLUGIN = 'woocommerce/woocommerce.php',
        HOOK_1 = 'woocommerce_order_status_changed',
        HOOK_2 = 'woocommerce_checkout_update_order_meta',
        HOOK_3 = 'woocommerce_payment_complete',
        HOOK_4 = 'woocommerce_pre_payment_complete',
        HOOK_5 = 'woocommerce_checkout_order_processed';

    public static function getDefaultConfig()
    {
        return [
            self::EXT_NAME => [
                'domain'  => home_url(),
                'cartUrl' => '/cart',
                'tags' => [
                    'registration' => "woocommerce_register",
                    'login'        => "woocommerce_login",
                    'purchase'     => "woocommerce_purchase",
                    'newsletter'   => "woocommerce_newsletter"
                ]
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

        if(empty($userData['extensions']['wc']['event_config'])){
            $userData['extensions']['wc']['event_config'] = [
                'type'         => 'hooks',
                'hookConfig'   => 'woocommerce_order_status_changed'
            ];
        }

        $userData['extensions']['wc']['domain'] = ($userData['extensions']['wc']['domain'][strlen($userData['extensions']['wc']['domain'])-1] === '/')
	        ? substr($userData['extensions']['wc']['domain'], 0, -1)
	        : $userData['extensions']['wc']['domain'];

        $userData['extensions']['wc']['list_hooks'] = [
            self::HOOK_1,
            self::HOOK_2,
            self::HOOK_3,
            self::HOOK_4,
            self::HOOK_5
        ];

        if(isset($userData['extensions']['wc']['event_config']['cronConfig']) && empty($userData['extensions']['wc']['event_config']['hookConfig'])){
            $userData['extensions']['wc']['event_config']['hookConfig'] = self::HOOK_1;
        }

        if($userData['extensions']['wc']['event_config']['type'] !== 'cron'){
            $userData['extensions']['wc']['event_config']['cronConfig'] = null;
        } else {
            $userData['extensions']['wc']['event_config']['cronConfig'] = is_int($userData['extensions']['wc']['event_config']['cronConfig']) ? $userData['extensions']['wc']['event_config']['cronConfig'] : intval($userData['extensions']['wc']['event_config']['cronConfig']);
        }

        return $userData['extensions']['wc'];
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
