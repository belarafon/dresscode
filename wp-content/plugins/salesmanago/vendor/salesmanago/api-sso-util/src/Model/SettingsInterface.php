<?php

namespace SALESmanago\Model;

use SALESmanago\Exception\UserAccessException;


interface SettingsInterface
{
    /**
     * @throws UserAccessException
     * @param $userProperties
     * @return mixed
     */
    public function getUserSettings($userProperties);
}
