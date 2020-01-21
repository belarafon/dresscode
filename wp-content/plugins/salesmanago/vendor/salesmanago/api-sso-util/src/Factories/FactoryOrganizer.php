<?php

namespace SALESmanago\Factories;

use SALESmanago\Controller\ConnectSalesManagoController;
use SALESmanago\Controller\CreateAccountController;
use SALESmanago\Controller\IntegrationController;
use SALESmanago\Controller\LoginAccountController;
use SALESmanago\Controller\UserAccountController;
use SALESmanago\Exception\UserAccessException;

class FactoryOrganizer
{
    const CONNECT_SALESMANAGO_C = 'ConnectSalesManagoC';
    const CREATE_ACCOUNT_C      = 'CreateAccountC';
    const INTEGRATION_C         = 'IntegrationC';
    const LOGIN_ACCOUNT_C       = 'LoginAccountC';
    const USER_ACCOUNT_C        = 'UserAccountC';

    const USER_ACCESS_E         = 'UserAccessE';

    const COOKIES_CLIENT       = ConnectSalesManagoController::COOKIES_CLIENT;
    const COOKIES_EXT_EVENT    = ConnectSalesManagoController::COOKIES_EXT_EVENT;

    public function getInst($const, $paramsFirst = null, $paramsSecond = null)
    {
        switch ($const) {
            case self::CONNECT_SALESMANAGO_C:
                return new ConnectSalesManagoController($paramsFirst);
            break;
            case self::CREATE_ACCOUNT_C:
                return new CreateAccountController($paramsFirst, $paramsSecond);
            break;
            case self::INTEGRATION_C:
                return new IntegrationController($paramsFirst);
            break;
            case self::LOGIN_ACCOUNT_C:
                return new LoginAccountController($paramsFirst, $paramsSecond);
            break;
            case self::USER_ACCOUNT_C:
                return new UserAccountController($paramsFirst, $paramsSecond);
            break;
            case self::USER_ACCESS_E:
                return new UserAccessException($paramsFirst);
                break;
            default:
                return false;
            break;
        }
    }
}
