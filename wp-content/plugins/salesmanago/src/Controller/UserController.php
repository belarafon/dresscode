<?php

namespace bhr\Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use SALESmanago\Controller\IntegrationController;

class UserController extends IntegrationController
{
    public function __construct($model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getUserExtensions()
    {
        $data = $this->model->getExtensions();

        $buildResponse = $this->buildResponse();
        $response = $buildResponse
            ->addStatus(true)
            ->addArray($data)
            ->build();

        return $response;
    }

    public function setUserExtensions($userProperties)
    {
        $buildResponse = $this->buildResponse();

        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            $data = $this->model->setExtensions($userProperties);

            $response = $buildResponse
                ->addStatus(true)
                ->addArray($data)
                ->build();

            return $response;
        }

        $response = $buildResponse
            ->addStatus(false)
            ->addField('code', 31)
            ->build();

        return $response;
    }
}
