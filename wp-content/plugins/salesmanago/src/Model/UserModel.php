<?php

namespace bhr\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use SALESmanago\Entity\Settings;
use SALESmanago\Exception\UserAccessException;

use SALESmanago\Model\UserInterface;
use SALESmanago\Model\SettingsInterface;

class UserModel extends AbstractModel implements UserInterface, SettingsInterface
{
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

    public function getUserToken($userProperties)
    {
        $userData = $this->getUserData();

        $status = (strlen($userData[Settings::TOKEN]) == 64) ? true : false;

        $refresh = (time() > $userData['expiresTokenAt']) ? true : false;

        return array(
            'success'       => $status,
            Settings::TOKEN => $userData[Settings::TOKEN],
            'refresh'       => $refresh,
            'properties'    => isset($userData['properties'])
                ? $userData['properties']
                : false
        );
    }

    public function refreshUserToken($userProperties)
    {
        $value = $this
            ->getValue()
            ->addUpdatedToken($userProperties['token'])
            ->addExpiresTokeTime()
            ->addUpdatedAtTime()
            ->buildValue();

        $this->setUserData($value);
    }

    public function setCustomProperties($userProperties)
    {
        $value = $this
            ->getValue()
            ->addUpdatedAtTime()
            ->extendValue(array('properties' => $userProperties))
            ->buildValue();

        $this->setUserData($value);
    }

    public function getDataForAccountType($userProperties)
    {
        $userData = $this->getUserData();
        return array(
            Settings::TOKEN => $userData[Settings::TOKEN],
            Settings::EMAIL => $userData[Settings::OWNER],
        );
    }
}
