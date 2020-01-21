<?php

namespace SALESmanago\Controller;

use SALESmanago\Entity\Settings;
use SALESmanago\Services\LoginAccountService;
use SALESmanago\Exception\SalesManagoException;
use SALESmanago\Exception\AccountActiveException;
use SALESmanago\Model\LoginInterface;
use SALESmanago\Provider\UserProvider;


class LoginAccountController
{
    use ControllerTrait;

    protected $settings;
    protected $service;
    protected $model;

    public function __construct(Settings $settings, LoginInterface $model)
    {
        $this->service  = new LoginAccountService($settings);
        $this->settings = $settings;
        $this->model    = $model;
    }

    public function loginUser($user, $modelOptions = array())
    {
        try {
            $responseData = $this->service->accountAuthorize($user);

            UserProvider::settingsUserExtend(
                $this->settings
                    ->setOwner($user['username'])
                    ->setDefaultApiKey()
                    ->setToken($responseData[Settings::TOKEN])
                    ->setEndpoint($responseData['endpoint'])
            );

            $settingsIntegrationData = $this->service->accountIntegrationSettings($this->settings);

            UserProvider::settingsUserExtend(
                $this->settings
                    ->setClientId($settingsIntegrationData['shortId'])
                    ->setSha($settingsIntegrationData['sha1'])
            );

            $this->service->checkIfAccountIsActive($this->settings);

            $this->updateUserSettings($modelOptions);

            $integration = $this->service->getUserCustomProperties($this->settings);

            $buildResponse = $this->buildResponse();

            $buildResponse
                ->addStatus(true)
                ->addField(Settings::TOKEN, $responseData[Settings::TOKEN])
                ->addField('properties', array('success' => false));

            if ($integration['success'] == true) {
                $this->updateUserCustomProperties($modelOptions, $integration['properties']);

                $buildResponse->addField(
                    'properties',
                    array(
                        'success' => true,
                        'lang'    => $integration['properties']['lang'],
                        'color'   => $integration['properties']['color']
                    )
                );
            }

            return $buildResponse->build();
        } catch (SalesManagoException $e) {
            return $e->getSalesManagoMessage();
        } catch (AccountActiveException $e) {
            return $e->getExceptionMessage();
        }
    }

    protected function updateUserCustomProperties($modelOptions, $properties)
    {
        $userSettings = UserProvider::mergeConfig(
            $this->settings,
            $modelOptions
        );

        $this->model->updateProperties($userSettings, $properties);
    }

    protected function updateUserSettings($modelOptions)
    {
        $userSettings = UserProvider::mergeConfig(
            $this->settings,
            $modelOptions
        );

        $id = $this->model->checkUser($userSettings);
        if ($id) {
            $this->model->update($id, $userSettings);
        } else {
            $this->model->insert($userSettings);
        }
    }
}
