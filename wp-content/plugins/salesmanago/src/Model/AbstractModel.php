<?php

namespace bhr\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bhr\Helper\ModulesConfigManager;
use \DateTime as DTime;
use SALESmanago\Exception\Exception;
use SALESmanago\Exception\SalesManagoException;

/**
 * @abstract
 */
abstract class AbstractModel
{
    const
	    SETTINGS          = "salesmanago_settings",
	    SETTINGS_STORE_ID = "salesmanago_settings_store_id",

	    API_DOUBLE_OPT_IN = 'apiDoubleOptIn',
	    DOUBLE_OPT_IN     = 'doubleOptIn',

        SYNC_RULE         = 'synchronizeRule';

    /**
     * @var object
     */
    protected $db;
    protected $defaultValue = [];
    protected $userValue    = [];

    public function __construct()
    {
        $this->db  = $GLOBALS['wpdb'];
        $timestamp = time();

        $this->defaultValue = [
            'createdAt'  => $timestamp,
            'updatedAt'  => $timestamp

        ];

        $this->defaultValue['extensions'] = array_merge(
            ModulesConfigManager::getInstance()->getDefaultConfig(),
            ModulesConfigManager::getInstance()->getDefaultActive()
        );
    }

    final protected function arrayMergeRecursiveDistinct(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->arrayMergeRecursiveDistinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
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

    public function prepareValue($settings, $default = false)
    {
        $this->userValue = $settings;
        if ($default) {
            $this->userValue = $this->arrayMergeRecursiveDistinct($this->defaultValue, $settings);
        }

        return $this;
    }

    public function buildValue()
    {
        return json_encode($this->userValue);
    }

    public function decodeValue($settings)
    {
        return json_decode($settings, true);
    }

    public function addUpdatedToken($token)
    {
        $this->userValue['token'] = $token;

        return $this;
    }

    public function addUpdatedAtTime()
    {
        $this->userValue['createdAt'] = time();

        return $this;
    }

    public function addExpiresTokeTime()
    {
        $timestamp                         = time();
        $now                               = date("Y-m-d H:i:s", $timestamp);
        $this->userValue['expiresTokenAt'] = strtotime("$now +29 Days + 20 Hours");

        return $this;
    }

    public function extendValue($extend)
    {
        $this->userValue = $this->arrayMergeRecursiveDistinct($this->userValue, $extend);

        return $this;
    }

    public function getValue()
    {
        $stmt = $this->db->get_row($this->db->prepare("SELECT option_value FROM {$this->db->options} WHERE option_name = %s LIMIT 1", self::SETTINGS), ARRAY_A);

        $this->userValue = json_decode($stmt['option_value'], true);

        return $this;
    }

    public function getUserData()
    {
        $stmt = $this->db->get_row($this->db->prepare("SELECT option_value FROM {$this->db->options} WHERE option_name = %s LIMIT 1", self::SETTINGS), ARRAY_A);

        return json_decode($stmt['option_value'], true);
    }

    public function setUserData($value)
    {
        return (boolean)$this->db->query($this->db->prepare("UPDATE {$this->db->options} SET option_value = %s WHERE option_name = %s", array($value, self::SETTINGS)));
    }

	public function getApiDoubleOptIn()
	{
		$userData = $this->getUserData();

		$apiDoubleOptIn = isset($userData[self::API_DOUBLE_OPT_IN])
			? $userData[self::API_DOUBLE_OPT_IN]
			: false;

		$doubleOptIn = ($apiDoubleOptIn && !empty($userData[self::DOUBLE_OPT_IN]))
			? $userData[self::DOUBLE_OPT_IN]
			: false;

		return $apiDoubleOptIn
			? array(
				'useApiDoubleOptIn'=> $apiDoubleOptIn,
				'apiDoubleOptInEmailTemplateId' => isset($doubleOptIn['template']) ? $doubleOptIn['template'] : '',
				'apiDoubleOptInEmailAccountId' => isset($doubleOptIn['email']) ? $doubleOptIn['email'] : '',
				'apiDoubleOptInEmailSubject' => isset($doubleOptIn['topic']) ? $doubleOptIn['topic'] : '',
			)
			: $apiDoubleOptIn;
	}

	/**
	 * @return mixed - in WP synchronize rule is enabled by default to not allow
	 * to overwrite contact statuses in SM and based on apiDoubleOptIn
     * @return mixed
     * @throws mixed
	*/
	public function getSynchronizeRule()
	{
        $userData = $this->getUserData();

        $synchronizeRule = isset($userData[self::SYNC_RULE])
            ? $userData[self::SYNC_RULE]
            : false;

        $apiDoubleOptIn = $this->getApiDoubleOptIn();

		if ($synchronizeRule || $apiDoubleOptIn) {
		    $dateTime = new DTime('NOW');

			return [
				'createdOn' => $dateTime->format('c') /*it's not like in others platform */,
				'synchronizeRule' => true
			];
		} else {
			return false;
		}
	}
}
