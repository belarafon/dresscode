<?php

namespace SALESmanago\Model;


interface IntegrationInterface
{
    public function delete($userProperties);

    public function getAccountUserData($userProperties);

    public function setAccountUserData($userProperties);

    public function getPlatformUserData($userProperties);

    public function setPlatformUserData($userProperties);
}
