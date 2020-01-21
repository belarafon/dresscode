<?php

namespace bhr\Helper\Contact;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Address
{
    const
        ADDRESS   = 'address',
        STREET_AD = 'streetAddress',
        ZIP_CODE  = 'zipCode',
        CITY      = 'city',
        COUNTRY   = 'country';

    private $streetAddress;
    private $zipCode;
    private $city;
    private $country;

    public function __construct(
        $streetAddress = '',
        $zipCode = '',
        $city = '',
        $country = ''
    ) {
        $this->streetAddress = $streetAddress;
        $this->zipCode       = $zipCode;
        $this->city          = $city;
        $this->country       = $country;
    }

    public function setStreetAddress($param)
    {
        $this->streetAddress = $param;
        return $this;
    }

    public function setZipCode($param)
    {
        $this->zipCode = $param;
        return $this;
    }

    public function setCity($param)
    {
        $this->city = $param;
        return $this;
    }

    public function setCountry($param)
    {
        $this->country = $param;
        return $this;
    }

    public function get()
    {
        return $this->filterData(array(
            self::ADDRESS => array(
                self::STREET_AD => $this->setStrFromArr($this->streetAddress),
                self::ZIP_CODE  => $this->zipCode,
                self::CITY      => $this->city,
                self::COUNTRY   => $this->country
            )
        ));
    }

    protected function setStrFromArr($param, $glue = ' ')
    {
        if (!is_array($param)) {
            return $param;
        }

        $param = $this->filterArr($param);
        if (!empty($param)) {
            return implode($glue, $param);
        }

        return '';
    }

    protected function filterArr($array)
    {
        return array_filter(
            $array,
            function ($value) {
                return !empty($value);
            }
        );
    }

    final protected function filterData($data)
    {
        $data = array_map(function ($var) {
            return is_array($var) ? $this->filterData($var) : $var;
        }, $data);
        $data = array_filter($data, function ($value) {
            return !empty($value) || $value === false;
        });
        return $data;
    }
}
