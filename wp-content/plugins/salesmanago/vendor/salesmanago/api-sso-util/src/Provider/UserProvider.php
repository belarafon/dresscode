<?php

namespace SALESmanago\Provider;

use SALESmanago\DependencyManagement\IoC as Container;
use SALESmanago\Entity\Settings;
use SALESmanago\Exception\SalesManagoException;
use SALESmanago\Exception\UserAccessException;
use SALESmanago\Model\SettingsInterface;


class UserProvider
{
    const USER_NAME = "user-settings";

    protected $settings;

    /**
     * @return Settings object
     **/
    public static function settingsAccount()
    {
        $settingsAccount = new Settings();
        $settingsAccount
            ->setEndpoint("app2.salesmanago.pl")
            ->setClientId("8da75fbd-46bd-4a38-8893-d0219d23e75c")
            ->setApiSecret("52548bb9-ba43-11e7-a157-0cc47a1254ce")
            ->setDefaultApiKey()
            ->setOwner("appstore.partner@test.pl");

        return $settingsAccount;
    }

    /**
     * @throws SalesManagoException
     * @return Settings object
     **/
    public static function settingsUser()
    {
        $container = Container::init();

        $container::register(self::USER_NAME, function () {
            $settings = new Settings();
            $settings
                ->setEndpoint("app2.salesmanago.pl");
            return $settings;
        });

        return $container::resolve(self::USER_NAME);
    }

    /**
     * @throws SalesManagoException
     * @var Settings $settings
     **/
    public static function settingsUserExtend(Settings $settings)
    {
        $container = Container::init();

        $container::extend(self::USER_NAME, function () use ($settings) {
            return $settings;
        });

    }

    /**
     * @throws UserAccessException
     * @throws SalesManagoException
     * @var SettingsInterface $model
     * @param array $userProperties
     * @return Settings object
     **/
    public static function initSettingsUser(SettingsInterface $model, $userProperties = array())
    {
        $userData = $model->getUserSettings($userProperties);

        $container = Container::init();

        $container::register(self::USER_NAME, function () use ($userData) {
            $settings = new Settings();
            $settings
                ->setClientId($userData[Settings::CLIENT_ID])
                ->setApiKey($userData[Settings::API_KEY])
                ->setEndpoint($userData[Settings::ENDPOINT])
                ->setOwner($userData[Settings::OWNER])
                ->setSha($userData[Settings::SHA])
                ->setToken($userData[Settings::TOKEN]);
            return $settings;
        });

        return $container::resolve(self::USER_NAME);
    }

    /**
     * @param string $name
     * @param array $userData
     */
    public static function createSettingsContainer($name, $userData)
    {
        $container = Container::init();
        $container::register($name, function () use ($userData) {
            $settings = new Settings();
            $settings
                ->setEndpoint($userData[Settings::ENDPOINT])
                ->setClientId($userData[Settings::CLIENT_ID])
                ->setApiSecret($userData[Settings::API_SECRET])
                ->setOwner($userData[Settings::EMAIL])
                ->setToken($userData[Settings::TOKEN])
                ->setDefaultApiKey();
            return $settings;
        });
    }

    /**
     * @throws SalesManagoException
     * @param string $name
     * @return Settings object
     */
    public static function getSettingsContainer($name)
    {
        $container = Container::init();

        return $container::resolve($name);
    }

    /**
     * @var Settings $settings
     * @return array
     **/
    public static function getConfig(Settings $settings)
    {
        return $settings->getConfig();
    }

    /**
     * @var Settings $settings
     * @param array $extra
     * @return array
     **/
    public static function mergeConfig($settings, $extra)
    {
        return array_merge(self::getConfig($settings), $extra);
    }
}
