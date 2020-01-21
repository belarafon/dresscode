<?php

namespace SALESmanago\Controller;

use SALESmanago\Entity\Settings;
use SALESmanago\Services\CreateAccountService;
use SALESmanago\Exception\SalesManagoException;
use SALESmanago\Provider\UserProvider;
use SALESmanago\Model\CreateInterface;


class CreateAccountController
{
    use ControllerTrait;

    protected $settings;
    protected $service;
    protected $model;

    public function __construct(Settings $settings, CreateInterface $model)
    {
        $this->service  = new CreateAccountService($settings);
        $this->settings = $settings;
        $this->model    = $model;
    }

    /**
     * @param $consentArray
     * @return array
     */
    protected function createConsentSchema($consentArray)
    {
        $ip = $this->settings->getUserIP();
        foreach ($consentArray as $key => &$value) {
            $value['ip'] = $ip;
        }

        return $consentArray;
    }

    public function createAccount($user, $modulesId, $modelOptions = array())
    {
        try {
            /**
             * @var array $userData
             */
            $userData = $this->service->createAccount($this->settings, $user, $modulesId);

            $userData['endpoint'] = $this->settings->getEndpoint();

            if (isset($user['email'])) {
                $userData['email'] = $user['email'];
            }

            UserProvider::createSettingsContainer(UserProvider::USER_NAME, $userData);
            $settings = UserProvider::getSettingsContainer(UserProvider::USER_NAME);

            $modelOptions['lang'] = $user['lang'];

            $this->model->insert(
                UserProvider::mergeConfig(
                    $settings,
                    $modelOptions
                )
            );

            $settings->setTags(
                implode(',', array(
                    'SM-REGISTER',
                    'SSO_' . str_replace("-","", strtoupper($user['platform'])),
                    ($user['lang'] == "PL") ? "SALESMANAGO-R-B2C-PRO_PL,SALESMANAGO-R_PL" : "SALESMANAGO-R-B2C-PRO_EN,SALESMANAGO-R_ENG",
                    ))
                );

            $settings->setProperties(array(
                'website' => $user['website'],
            ));

            $this->service->contactToSupport(
                $settings,
                array(
                   'name' => $user['name'],
                   'phone' => $user['phone'],
                   'consentDetails' => $this->createConsentSchema($user['consentDetails']),
                )
            );

            $buildResponse = $this->buildResponse();
            $buildResponse
                ->addStatus($userData['success'])
                ->addField(Settings::TOKEN, $userData[Settings::TOKEN]);

            return $buildResponse->build();
        } catch (SalesManagoException $e) {
            return $e->getSalesManagoMessage();
        }
    }
}
