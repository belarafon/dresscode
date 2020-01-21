<?php

namespace SALESmanago\Controller;

use SALESmanago\Entity\Settings;
use SALESmanago\Services\UserAccountService;
use SALESmanago\Exception\SalesManagoException;
use SALESmanago\Exception\AccountActiveException;
use SALESmanago\Model\UserInterface;


class UserAccountController
{
    protected $settings;
    protected $service;
    protected $model;

    public function __construct(Settings $settings, UserInterface $model)
    {
        $this->service  = new UserAccountService($settings);
        $this->settings = $settings;
        $this->model    = $model;
    }

    /**
     * @return array
     * @throws AccountActiveException
     */
    public function refreshToken()
    {
        return $this->service->refreshToken($this->settings);
    }

    public function getToken($userProperties = array())
    {
        try {
            $responseData = $this->refreshToken();

            $userProperties['token'] = $responseData['token'];
            $this->model->refreshUserToken($userProperties);

            return $this->model->getUserToken($userProperties);
        } catch (AccountActiveException $e) {
            return $e->getExceptionMessage();
        }
    }

    public function userIntegration($userProperties)
    {
        $this->model->setCustomProperties($userProperties);
        return $this->setUserCustomProperties($userProperties);
    }

    public function createMonitorVisitorsCode($webPush = '')
    {
        $code
            = "<script>var _smid ='{$this->settings->getClientId()}';
             (function(w, r, a, sm, s ) {
             w['SalesmanagoObject'] = r; 
             w[r] = w[r] || function () {( w[r].q = w[r].q || [] ).push(arguments)};
             sm = document.createElement('script'); 
             sm.type = 'text/javascript'; sm.async = true; sm.src = a;
             s = document.getElementsByTagName('script')[0]; 
             s.parentNode.insertBefore(sm, s);
             })(window, 'sm', ('https:' == document.location.protocol ? 'https://' : 'http://')
             + '{$this->settings->getEndpoint()}/static/sm.js');{$webPush}</script>";

        return trim(preg_replace('/\s+/', ' ', $code));
    }

    public function getUserCustomProperties()
    {
        try {
            $responseData = $this->service->getUserCustomProperties($this->settings);
            return $responseData;
        } catch (SalesManagoException $e) {
            return $e->getSalesManagoMessage();
        }
    }

    public function setUserCustomProperties($properties)
    {
        try {
            $responseData = $this->service->setUserCustomProperties($this->settings, $properties);
            return $responseData;
        } catch (SalesManagoException $e) {
            return $e->getSalesManagoMessage();
        }
    }

    public function getUserItems()
    {
        try {
            $responseData = $this->service->userItems($this->settings);
            $responseData['redirect'] = $this->getRedirectApp();
            return $responseData;
        } catch (SalesManagoException $e) {
            return $e->getSalesManagoMessage();
        }
    }

    public function getRedirectApp()
    {
        $responseData = $this->service->redirectApp($this->settings);
        return $responseData;
    }

    public function addSubscribeProducts($module_name)
    {
        try {
            $products = $this->service->getModules();
            if (in_array($module_name, $products)) {
                $modulesId = array(
                    0 => array_search($module_name, $products)
                );
            } else {
                throw new SalesManagoException('Cannot add appstore products', 13);
            }

            $responseData = $this->service->addSubscribeProducts($this->settings, $modulesId);
            return $responseData;
        } catch (SalesManagoException $e) {
            return $e->getSalesManagoMessage();
        }
    }

    public function createProduct($module_name, $properties)
    {
        try {
            switch ($module_name) {
                case "LIVE_CHAT":
                    $response = $this->service->createProduct(
                        $this->settings,
                        UserAccountService::METHOD_CREATE_LIVE_CHAT,
                        array_merge(
                            $properties,
                            array(
                                'email' => $this->settings->getOwner()
                            )
                        )
                    );
                    break;
                case "WEB_PUSH":
                    $response = $this->service->createProduct(
                        $this->settings,
                        UserAccountService::METHOD_CREATE_WEB_PUSH_CONSENT_FORM,
                        array_merge(
                            $properties,
                            array(
                                'logo'      => 'https://s3-eu-west-1.amazonaws.com/salesmanagoimg/ye4vodnswfo6zp75/36m0iryqk4wlt6wu/vi3qhhiwqc485flt.png',
                                'thanksUrl' => 'https://www.domain.com/target_url',
                                'email'     => $this->settings->getOwner()
                            )
                        )
                    );
                    break;
                case "CF_P_LP":
                    $response = $this->service->createProduct(
                        $this->settings,
                        UserAccountService::METHOD_CREATE_BASIC_POPUP,
                        array_merge(
                            $properties,
                            array(
                                'email' => $this->settings->getOwner()
                            )
                        )
                    );
                    break;
                default:
                    throw new SalesManagoException('Unsupported data source type', 13);
            }

            if (is_array($response)
                && $response['success'] == false
                && $response['type'] == 'STORE'
            ) {
                $this->addSubscribeProducts($module_name);
                return $this->createProduct($module_name, $properties);
            }

            return $response;
        } catch (SalesManagoException $e) {
            return $e->getSalesManagoMessage();
        }
    }

    public function exportContacts($data)
    {
        try {
            $response = $this->service->exportContacts($this->settings, $data);
            return json_encode($response);
        } catch (SalesManagoException $e) {
            return $e->getSalesManagoMessage();
        }
    }

    public function exportContactExtEvents($data)
    {
        try {
            $response = $this->service->exportContactExtEvents($this->settings, $data);
            return json_encode($response);
        } catch (SalesManagoException $e) {
            return $e->getSalesManagoMessage();
        }
    }

    public function getAccountTypeWithContacts($userProperties = array())
    {
        $data = $this->model->getDataForAccountType($userProperties);
        try {
            $response = $this->service->getAccountTypeWithContacts($data);
            return $response;
        } catch (SalesManagoException $e) {
            return $e->getSalesManagoMessage();
        }
    }

    public function itemAction($itemData)
    {
        try {
            $response = $this->service->itemAction($this->settings, $itemData);
            return $response;
        } catch (SalesManagoException $e) {
            return $e->getSalesManagoMessage();
        }
    }

    public function getConsentFormCode($userData)
    {
        try {
            $response = $this->service->consentFormCode($userData);
            return $response;
        } catch (SalesManagoException $e) {
            return $e->getSalesManagoMessage();
        }
    }

    public function uploadImage($image)
    {
        try {
            $response = $this->service->uploadImage($this->settings, $image);
            return $response;
        } catch (SalesManagoException $e) {
            return $e->getSalesManagoMessage();
        }
    }

    public function listUsersByClient()
    {
        try {
            $response = $this->service->listUsersByClient($this->settings);
            return $response;
        } catch (SalesManagoException $e) {
            return $e->getSalesManagoMessage();
        }
    }
}
