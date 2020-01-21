<?php

namespace bhr\Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use SALESmanago\Controller\UserAccountController;
use SALESmanago\Entity\Settings;

class IntegrationController extends UserAccountController
{
    public $userProperties;

    public function __construct(Settings $settings, $model)
    {
        parent::__construct($settings, $model);
        $this->model = $model;
    }

    public function setUserIntegration($userProperties)
    {
        $this->userProperties = $userProperties;
        $this->model->setCustomProperties($userProperties['properties']);

        return $this->userIntegration($userProperties['properties']);
    }
}
