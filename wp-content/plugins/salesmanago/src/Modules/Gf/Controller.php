<?php

namespace bhr\Modules\Gf;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use SALESmanago\Controller\ControllerTrait;
use SALESmanago\Controller\UserAccountController;
use SALESmanago\Entity\Settings;

class Controller extends UserAccountController
{
    use ControllerTrait;

    protected $gfModel;
    protected $config;

    public function __construct(Settings $settings, $model)
    {
        parent::__construct($settings, $model);
        $this->gfModel = new Model();
    }

    public function getConfig()
    {
        $data = $this->gfModel->getConfig();
        $this->getUsersList($data);
        $buildResponse = $this->buildResponse();
        $response      = $buildResponse
            ->addStatus( true )
            ->addArray( $data )
            ->build();

        return $response;
    }

    public function setConfig($userProperties)
    {
        $status = $this->gfModel->setConfig($userProperties);

        $buildResponse = $this->buildResponse();
        $response      = $buildResponse
            ->addStatus( $status )
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
