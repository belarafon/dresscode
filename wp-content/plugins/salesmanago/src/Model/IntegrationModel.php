<?php

namespace bhr\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use SALESmanago\Entity\Settings;
use SALESmanago\Model\IntegrationInterface;
use SALESmanago\Model\UserInterface;

class IntegrationModel extends AbstractModel implements IntegrationInterface
{
	use StoreTrait;

    public function delete($userProperties)
    {
        return (boolean)$this->db->query($this->db->prepare("DELETE FROM {$this->db->options} WHERE option_name = %s", self::SETTINGS));
    }

    public function getUserConfig($userProperties)
    {
        $stmt = $this->db->get_row($this->db->prepare("SELECT option_value FROM {$this->db->options} WHERE option_name = %s LIMIT 1", self::SETTINGS), ARRAY_A);

        return $this->decodeValue($stmt['option_value']);
    }

    public function getAccountUserData($userProperties)
    {
        $stmt = $this->db->get_row($this->db->prepare("SELECT option_value FROM {$this->db->options} WHERE option_name = %s LIMIT 1", self::SETTINGS), ARRAY_A);

        $userData = $this->decodeValue($stmt['option_value']);

        $data = array(
	        Settings::CLIENT_ID => $userData[ Settings::CLIENT_ID ],
	        Settings::ENDPOINT => $userData[ Settings::ENDPOINT ],
	        Settings::OWNER => $userData[ Settings::OWNER ],
	        'storeId' => $this->getStoreId(get_home_url()),
	        self::API_DOUBLE_OPT_IN => isset( $userData[ self::API_DOUBLE_OPT_IN ] )
		        ? filter_var( $userData[ self::API_DOUBLE_OPT_IN ], FILTER_VALIDATE_BOOLEAN )
		        : false,
	        self::DOUBLE_OPT_IN => (isset($userData[self::DOUBLE_OPT_IN])&& ! empty($userData[self::DOUBLE_OPT_IN]))
		        ? $userData[ self::DOUBLE_OPT_IN ]
		        : false
        );

        if (isset($userData['newsletter'])
        && !empty($userData['newsletter'])
        ) {
            $data['newsletter'] = $userData['newsletter'];
        }

        if (isset($userData['synchronizeRule'])
            && filter_var(
                $userData['synchronizeRule'],
                FILTER_VALIDATE_BOOLEAN
            ) == true
        ) {
            $data['synchronizeRule'] = $userData['synchronizeRule'];
        }

        return $data;
    }

    public function setAccountUserData($userProperties)
    {
        $value = $this
            ->getValue()
            ->addUpdatedAtTime()
            ->extendValue(
                array(
                    Settings::OWNER    => $userProperties[Settings::OWNER],
                    'newsletter'     => isset($userProperties['newsletter'])
                        ? $userProperties['newsletter']
                        : '',
                    'synchronizeRule'  => isset($userProperties['synchronizeRule'])
                        ? $userProperties['synchronizeRule']
                        : false,
                    self::API_DOUBLE_OPT_IN => isset($userProperties[self::API_DOUBLE_OPT_IN])
	                    ? filter_var($userProperties[self::API_DOUBLE_OPT_IN], FILTER_VALIDATE_BOOLEAN)
	                    : false,
                    self::DOUBLE_OPT_IN => isset($userProperties[self::DOUBLE_OPT_IN])
	                    ? $userProperties[self::DOUBLE_OPT_IN]
	                    : false
                )
            )
            ->buildValue();

        return $this->setUserData($value);
    }

    public function getPlatformUserData($userProperties)
    {
    }

    public function setPlatformUserData($userProperties)
    {
    }
}
