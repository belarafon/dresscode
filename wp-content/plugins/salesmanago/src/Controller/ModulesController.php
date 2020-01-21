<?php

namespace bhr\Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use SALESmanago\Controller\ControllerTrait;
use bhr\Model\ModulesModel;
use bhr\Helper\ModulesConfigManager;

class ModulesController
{
    use ControllerTrait;

    public $model;

    public function __construct()
    {
        $this->model = new ModulesModel();
    }

    public function getModules()
    {
        $modules = $this->model->getModules();

        $buildResponse = $this->buildResponse();

        $response = $buildResponse
            ->addStatus(true)
            ->addField('modules', $modules)
            ->build();

        return $response;
    }

    public function setModules($userProperties)
    {
        $modules = $this->model->setModules($userProperties);

        $buildResponse = $this->buildResponse();

        $response = $buildResponse
            ->addStatus(true)
            ->addField('modules', $modules)
            ->build();

        return $response;
    }

    public function getParentState($extensionKey)
    {
        $activesByParents = ModulesConfigManager::getInstance()->getActivesByParents();
        $buildResponse = $this->buildResponse();

        if (array_key_exists($extensionKey, $activesByParents)) {
            $extArr = [$extensionKey => $activesByParents[$extensionKey]];
            $status = true;
        } else {
            $extArr = [
                $extensionKey => false,
                'code' => 31
            ];
            $status = false;
        }

        $response = $buildResponse
            ->addArray($extArr)
            ->addStatus($status)
            ->build();

        return $response;
    }
}
