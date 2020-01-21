<?php

namespace bhr\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bhr\Helper\ModulesConfigManager;

class ModulesModel extends AbstractModel
{
    public function getModules()
    {
        $userData = $this->getUserData();

        return $userData['extensions']['active'];
    }

    public function setModules($userProperties)
    {
        $userData = $this->getUserData();

        $canBeActivated = ModulesConfigManager::getInstance()->getActivesByParents();

        foreach ($userProperties as $extension => $state) {
            if (array_key_exists($extension, $canBeActivated)) {
                $checkBool = filter_var($userProperties[$extension], FILTER_VALIDATE_BOOLEAN);
                $userData['extensions']['active'][$extension] = $checkBool;
            } else {
                $userData['extensions']['active'][$extension] = false;
            }
        }

        $value = $this
            ->prepareValue($userData)
            ->addUpdatedAtTime()
            ->buildValue();

        $this->setUserData($value);

        return $userData['extensions']['active'];
    }
}
