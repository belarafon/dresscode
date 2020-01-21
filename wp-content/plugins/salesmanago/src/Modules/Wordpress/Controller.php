<?php

namespace bhr\Modules\Wordpress;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use SALESmanago\Controller\ControllerTrait;

class Controller
{
    use ControllerTrait;

    protected $model;
    protected $config;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getConfig()
    {
        $data = $this->model->getConfig();

        $buildResponse = $this->buildResponse();
        $response = $buildResponse
            ->addStatus(true)
            ->addArray($data)
            ->build();

        return $response;
    }

    public function setConfig($userProperties)
    {
        $status = $this->model->setConfig($userProperties);

        $buildResponse = $this->buildResponse();
        $response = $buildResponse
            ->addStatus($status)
            ->build();

        return $response;
    }
}
