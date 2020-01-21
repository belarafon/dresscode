<?php

namespace bhr\Modules\Cf7;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bhr\Modules\Cf7\Model as Cf7Model;

use SALESmanago\Controller\ControllerTrait;
use SALESmanago\Controller\UserAccountController;
use SALESmanago\Entity\Settings;

class Controller extends UserAccountController
{
    use ControllerTrait;

    protected $cf7Model;
    protected $config;

    public function __construct(Settings $settings, $model, Cf7Model $cf7Model)
    {
        parent::__construct($settings, $model);
        $this->cf7Model = $cf7Model;
    }

    public function getConfig()
    {
        $data = $this->cf7Model->getConfig();
        $this->getUsersList($data);
        $buildResponse = $this->buildResponse();
        $response = $buildResponse
            ->addStatus(true)
            ->addArray($data)
            ->build();

        return $response;
    }

    public function setConfig($userProperties)
    {
        $status = $this->cf7Model->setConfig($userProperties);
        $buildResponse = $this->buildResponse();
        $response = $buildResponse
            ->addStatus($status)
            ->build();

        return $response;
    }

    private function getUsersList(&$data)
    {
        $usersList = $this->listUsersByClient();
        if ($usersList['success']) {
            $data['users'] = $usersList['users'];
        }
    }
}