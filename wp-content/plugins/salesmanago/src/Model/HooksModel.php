<?php

namespace bhr\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use SALESmanago\Entity\Settings;
use SALESmanago\Exception\UserAccessException;
use SALESmanago\Model\SettingsInterface;

class HooksModel extends AbstractModel implements SettingsInterface
{
	use StoreTrait;

    const
        TAGS       = 'tags',
        T_NEWS     = 'newsletter',
        T_REGISTER = 'registration',
        T_LOGIN    = 'login',
        T_PURCHASE = 'purchase';

    /**
     * @param $userProperties
     * @throws UserAccessException
     * @return mixed
     */
    public function getUserSettings($userProperties)
    {
        $stmt = $this->db->get_row($this->db->prepare("SELECT option_value FROM {$this->db->options} WHERE option_name = %s LIMIT 1", self::SETTINGS), ARRAY_A);

        if ($stmt == null) {
            throw new UserAccessException('User not exist');
        }

        return $this->decodeValue($stmt['option_value']);
    }

    /**
     * @return array
     * @throws UserAccessException
     */
    public function monitorVisitorsData()
    {
        $stmt = $this->db->get_row($this->db->prepare("SELECT option_value FROM {$this->db->options} WHERE option_name = %s LIMIT 1", self::SETTINGS), ARRAY_A);

        if ($stmt == null) {
            throw new UserAccessException('User not exist');
        }

        $userData = $this->decodeValue($stmt['option_value']);

        return array(
            Settings::CLIENT_ID => $userData[Settings::CLIENT_ID],
            Settings::ENDPOINT  => $userData[Settings::ENDPOINT]
        );
    }

    public function getModules()
    {
        $userData = $this->getUserData();

        if (isset($userData['extensions']['active'])) {
            return $userData['extensions']['active'];
        }

        return $this->defaultValue['extensions']['active'];
    }
}
