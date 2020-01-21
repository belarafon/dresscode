<?php

namespace SALESmanago\Services;

use SALESmanago\Entity\Settings;
use SALESmanago\Exception\SalesManagoError;
use SALESmanago\Exception\SalesManagoException;


trait ClientTrait
{
    private $statusCode;

    protected $modules = array("EMAIL_MARKETING", "LIVE_CHAT", "WEB_PUSH", "CF_P_LP");

    /**
     * @return int $statusCode
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    private function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    final protected function __getDefaultApiData(Settings $settings)
    {
        $data = array(
            Settings::CLIENT_ID => $settings->getClientId(),
            Settings::API_KEY   => $settings->getApiKey(),
            Settings::SHA       => $settings->getSha(),
            Settings::OWNER     => $settings->getOwner(),
            'requestTime'       => time(),
        );
        return $data;
    }

    final protected function __getModulesData($modulesId)
    {
        $modules = array();

        foreach ($modulesId as $value) {
            $obj = array("name" => $this->modules[$value]);

            if ($value == 0) {
                $obj = array_merge($obj, array(
                    "contactLimit" => 1000
                ));
            }
            array_push($modules, $obj);
        }

        return $modules;
    }

    final public function filterData($data)
    {
        $data = array_map(function ($var) {
            return is_array($var) ? $this->filterData($var) : $var;
        }, $data);
        $data = array_filter($data, function ($value) {
            return !empty($value) || $value === false;
        });
        return $data;
    }

    /**
     * @throws SalesManagoException
     * @param array $response
     * @return array
     */
    protected function validateResponse($response)
    {
        if (is_array($response)
            && array_key_exists('success', $response)
            && $response['success'] == true
        ) {
            return $response;
        } else {
            throw SalesManagoError::handleError($response['message'], $this->getStatusCode());
        }
    }

    /**
     * @throws SalesManagoException
     * @param array $response
     * @param array $statement
     * @return array
     */
    protected function validateCustomResponse($response, $statement = array())
    {
        $condition = array(is_array($response), array_key_exists('success', $response), $response['success'] == true);
        $condition = array_merge($condition, $statement);

        if (!in_array(false, $condition)) {
            return $response;
        } else {
            throw SalesManagoError::handleError($response['message'], $this->getStatusCode());
        }
    }

    /**
     * @return string
     */
    public function getContactIp()
    {
        if (getenv('HTTP_CLIENT_IP'))
            $ip = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ip = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ip = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ip = getenv('HTTP_FORWARDED');
        else
            $ip = getenv('REMOTE_ADDR');

        return $ip;
    }
}
