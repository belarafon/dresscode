<?php

namespace SALESmanago\Model;


interface UserInterface
{
    public function getUserToken($userProperties);

    public function refreshUserToken($userProperties);

    public function setCustomProperties($userProperties);

    public function getDataForAccountType($userProperties);
}
