<?php

namespace SALESmanago\Controller;

use SALESmanago\Model\IntegrationInterface;


class IntegrationController
{
    use ControllerTrait;

    protected $model;

    public function __construct(IntegrationInterface $model)
    {
        $this->model = $model;
    }

    public function logout($userProperties = array())
    {
        $status = $this->model->delete($userProperties);

        $buildResponse = $this->buildResponse();
        $response = $buildResponse
            ->addStatus($status)
            ->build();

        return $response;
    }

    public function getAccountUserData($userProperties = array())
    {
        $data = $this->model->getAccountUserData($userProperties);

        $buildResponse = $this->buildResponse();
        $response = $buildResponse
            ->addStatus(true)
            ->addArray($data)
            ->build();

        return $response;
    }

    public function setAccountUserData($userProperties = array())
    {
        $status = $this->model->setAccountUserData($userProperties);

        $buildResponse = $this->buildResponse();
        $response = $buildResponse
            ->addStatus($status)
            ->build();

        return $response;
    }

    public function getPlatformUserData($userProperties = array())
    {
        $data = $this->model->getPlatformUserData($userProperties);

        $buildResponse = $this->buildResponse();
        $response = $buildResponse
            ->addStatus(true)
            ->addArray($data)
            ->build();

        return $response;
    }

    public function setPlatformUserData($userProperties = array())
    {
        $status = $this->model->setPlatformUserData($userProperties);

        $buildResponse = $this->buildResponse();
        $response = $buildResponse
            ->addStatus($status)
            ->build();

        return $response;
    }
}
