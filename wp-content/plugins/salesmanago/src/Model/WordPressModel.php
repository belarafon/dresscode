<?php

namespace bhr\Model;

class WordPressModel extends AbstractModel
{
    public function getExtensions()
    {
        $userData = $this->getUserData();

        return $userData['extensions']['active'];
    }

    public function setExtensions($userProperties)
    {
        $userData = $this->getUserData();
        $userData['extensions']['active']['wc'] = filter_var($userProperties['wc'], FILTER_VALIDATE_BOOLEAN);
        $userData['extensions']['active']['news'] = filter_var($userProperties['news'], FILTER_VALIDATE_BOOLEAN);

        $value = $this
            ->prepareValue($userData)
            ->addUpdatedAtTime()
            ->buildValue();

        $this->setUserData($value);

        return $userData['extensions']['active'];
    }

    public function getWooCommerceConfig()
    {
        $userData = $this->getUserData();

        return $userData['extensions']['wc'];
    }

    public function setWooCommerceConfig($userProperties)
    {
        $value = $this
            ->getValue()
            ->addUpdatedAtTime()
            ->extendValue(array('extensions' => $userProperties))
            ->buildValue();

        $status = $this->setUserData($value);

        return $status;
    }
}
