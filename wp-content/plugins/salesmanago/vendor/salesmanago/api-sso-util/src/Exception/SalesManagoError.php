<?php

namespace SALESmanago\Exception;


class SalesManagoError
{
    protected static function resolveErrorCode($message)
    {
        $errorMessage = array(
            'User not authenticated',
            'Api key not specified',
            'Token not specified',
            'Not authorized',
            'Integration failed',
            'Not authenticated',
            'Not authenticated, not owner specified',
            'Not authenticated, invalid owner',
            'Authorization failed. Wrong Token',
            'Authorization failed. Wrong clientId',
            'Already registered',
            'Unable to create account: Already registered',
            'Cannot register appstore user',
            'Invalid account',
            'Token refreshed failed',
            'Items retrieving failed',
            'Cannot add appstore products',
            'Cannot save integration properties',
            'Integration properties retrieving failed',
            'Cannot retrieve properties',
            'No integration properties found',
            'Cannot create live chat. Permission denied to Live Chat Module',
            'Cannot create basic popup. Permission denied to Popup Module',
            'Cannot create consent form. Permission denied to Web Push Module',
            'Cannot create live chat',
            'Cannot create basic popup',
            'Cannot create web push consent form',
            'Invalid confirmation email account'
        );

        $errorCode = array(
            10,
            11,
            11,
            11,
            11,
            12,
            12,
            12,
            12,
            12,
            13,
            13,
            14,
            15,
            16,
            17,
            18,
            19,
            20,
            20,
            21,
            22,
            22,
            22,
            23,
            23,
            23,
            24
        );

        $code = (int)str_replace($errorMessage, $errorCode, trim($message));

        if (!is_int($code)) {
            $code = 9;
        }

        return $code;
    }

    protected static function resolveErrorCurl($error)
    {
        $errorCodeCurl = array(
            6,
            28
        );

        $errorCode = array(
            9,
            9
        );

        if (in_array($error, $errorCodeCurl)) {
            $code = (int)str_replace($errorCodeCurl, $errorCode, $error);
        } else {
            $code = 9;
        }

        return $code;
    }

    public static function handleError($message, $status = 0, $curl = false, $error = null)
    {
        if ($curl == true) {
            $code = self::resolveErrorCurl($error);
        } elseif ($status >= 400 && $status <= 500) {
            $code = 8;
        } elseif ($status >= 500) {
            $code = 9;
        } else {
            $code    = self::resolveErrorCode($message[0]);
            $message = trim(implode(' | ', $message));
        }

        return new SalesManagoException($message, $code, $status);
    }
}
