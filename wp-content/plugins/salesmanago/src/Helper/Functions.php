<?php

namespace bhr\Helper;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

abstract class Functions
{
    /**
     * @return mixed|string
     */
    public static function getUserIP() {
        $ipAddress = 'UNKNOWN';

        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if(isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        } else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if(isset($_SERVER['HTTP_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        } else if(isset($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }

        return $ipAddress;
    }

    /**
     * @param array $data
     * @return array
     */
    public static function filterData(array $data)
    {
        $data = array_map(function ($var) {
            return is_array($var) ? self::filterData($var) : $var;
        }, $data);
        $data = array_filter($data, function ($value) {
            return !empty($value) || $value === false;
        });
        return $data;
    }

    /**
     * Set string length to SM properties length standard
     *
     * @param string $string - properties
     * @param int $length
     * @return bool|string
     */
    public static function checkCropProperties($string, $length = 255)
    {
        $length = ($length > 255) ? 255 : $length;
        return substr($string, $length);
    }

    public static function createCookie($name, $value, $period = null)
    {
        /*if(isset($_COOKIE[$name])){
            unset($_COOKIE[$name]);
        }*/

        $period = ($period == null)
            ? time() + (3600 * 86400)
            : $period;

        $_SESSION[$name] = $value;
        setcookie($name, $value, $period, '/');
    }

    public static function deleteCookie($name)
    {
        unset($_COOKIE[$name]);

        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
        };

        setcookie($name, null, -1, '/');
    }

    public static function setIntervalValues($schedules, $n = 1){
        $schedules['every'.$n.'hours'] = [
            'interval' => $n * 3600,
            'display' => __('every'.$n.'hours')
        ];

        return $schedules;
    }
}
