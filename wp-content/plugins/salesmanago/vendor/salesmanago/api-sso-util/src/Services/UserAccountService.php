<?php

namespace SALESmanago\Services;

use SALESmanago\Entity\Settings;
use SALESmanago\Exception\SalesManagoException;
use SALESmanago\Exception\AccountActiveException;


class UserAccountService extends AbstractClient implements UserAccountInterface, UserCustomPropertiesInterface
{
    public function __construct(Settings $settings)
    {
        $this->setClient($settings);
    }

    protected function __getLiveChatData($name, $options)
    {
        $data = array(
            'chat' => array(
                'name'              => $name,
                'defaultConsultant' => array(
                    'name'   => $options['consultant'],
                    'avatar' => array(
                        'url' => 'https://s3-eu-west-1.amazonaws.com/salesmanago/chat/default_avatar.png'
                    )
                ),
                'inactive'          => array(
                    'colors' => array(
                        'main'           => $options['color']['main'],
                        'mainFont'       => $options['color']['mainFont'],
                        'additional'     => $options['color']['additional'],
                        'additionalFont' => $options['color']['additionalFont'],
                        'background'     => '#ffffff',
                        'info'           => '#9c9c9c'
                    ),
                    'fields' => array(
                        array(
                            'label'            => array(
                                'text'      => 'Imię i nazwisko',
                                'fontColor' => '#333333'
                            ),
                            'type'             => 'NAME',
                            'customDetailName' => null,
                            'placeholder'      => array(
                                'text'      => 'Jan Kowalski',
                                'fontColor' => null
                            ),
                            'confirmationTag'  => null,
                            'declinedTag'      => null,
                            'required'         => false
                        ),
                        array(
                            'label'            => array(
                                'text'      => 'Adres e-mail',
                                'fontColor' => '#333333'
                            ),
                            'type'             => 'EMAIL',
                            'customDetailName' => null,
                            'placeholder'      => array(
                                'text'      => 'email.address@example.com',
                                'fontColor' => null
                            ),
                            'confirmationTag'  => null,
                            'declinedTag'      => null,
                            'required'         => false
                        )
                    ),
                    'sendTo' => array($options['email'])
                ),
                'colors'            => array(
                    'main'           => $options['color']['main'],
                    'mainFont'       => $options['color']['mainFont'],
                    'additional'     => $options['color']['additional'],
                    'additionalFont' => $options['color']['additionalFont'],
                    'background'     => '#ffffff',
                    'info'           => '#9c9c9c'
                ),
                'active'            => true,
                'location'          => array(
                    'domain'     => [''],
                    'filterType' => 'NONE',
                    'url'        => [''],
                    'exurl'      => [''],
                    'mobile'     => true,
                ),
                'contactOwner'      => array(
                    'email' => $options['email']
                )
            )
        );
        return $data;
    }

    protected function __getBasicPopupData($name, $options)
    {
        $data = array(
            'popup' => array(
                'popupType'               => 'NEW_VISITORS_CONTACT_FORM',
                'contactFormContent'      => array(
                    'image'           =>
                        array(
                            'fileName' => 'logo.png',
                            'url'      => $options['logo'],
                            'cssClass' => 'fa-envelope',
                        ),
                    'title'           =>
                        array(
                            'text'      => $options['title'],
                            'fontColor' => '#333333',
                        ),
                    'content'         =>
                        array(
                            'text'      => $options['content'],
                            'fontColor' => '#333333',
                            'align '    => 'left ',
                        ),
                    'inputFields'     => array(
                        array(
                            'label'            =>
                                array(
                                    'text'      => $options['label']['name'],
                                    'fontColor' => '#333333',
                                ),
                            'type'             => 'NAME',
                            'customDetailName' => NULL,
                            'placeholder'      =>
                                array(
                                    'text'      => $options['placeholder']['name'],
                                    'fontColor' => NULL,
                                ),
                            'confirmationTag'  => NULL,
                            'declinedTag'      => NULL,
                            'required'         => false,
                        ),
                        array(
                            'label'            =>
                                array(
                                    'text'      => $options['label']['email'],
                                    'fontColor' => '#333333',
                                ),
                            'type'             => 'EMAIL',
                            'customDetailName' => NULL,
                            'placeholder'      =>
                                array(
                                    'text'      => $options['placeholder']['email'],
                                    'fontColor' => NULL,
                                ),
                            'confirmationTag'  => NULL,
                            'declinedTag'      => NULL,
                            'required'         => false,
                        ),
                        array(
                            'label'            =>
                                array(
                                    'text'      => $options['label']['company'],
                                    'fontColor' => '#333333',
                                ),
                            'type'             => 'COMPANY',
                            'customDetailName' => NULL,
                            'placeholder'      =>
                                array(
                                    'text'      => $options['placeholder']['company'],
                                    'fontColor' => NULL,
                                ),
                            'confirmationTag'  => NULL,
                            'declinedTag'      => NULL,
                            'required'         => false,
                        ),
                        array(
                            'label'            =>
                                array(
                                    'text'      => $options['label']['website'],
                                    'fontColor' => '#333333',
                                ),
                            'type'             => 'WEBSITE',
                            'customDetailName' => NULL,
                            'placeholder'      =>
                                array(
                                    'text'      => $options['placeholder']['website'],
                                    'fontColor' => NULL,
                                ),
                            'confirmationTag'  => NULL,
                            'declinedTag'      => NULL,
                            'required'         => false,
                        ),
                    ),
                    'backgroundColor' => '#EBEBEB',
                    'subscribeButton' =>
                        array(
                            'text'            => $options['button']['subscribe'],
                            'borderRadius'    => '5px',
                            'fontColor'       => $options['color']['mainFont'],
                            'backgroundColor' => $options['color']['main'],
                        ),
                    'closeButton'     =>
                        array(
                            'text'            => $options['button']['close'],
                            'borderRadius'    => '5px',
                            'fontColor'       => $options['color']['additional'],
                            'backgroundColor' => $options['color']['additionalFont'],
                        ),
                ),
                'thankYouPageContent'     => array(
                    'image'           => array(
                        'fileName' => 'logo.png',
                        'url'      => $options['logo'],
                        'cssClass' => 'fa-envelope',
                    ),
                    'message'         => array(
                        'text'      => $options['thankYouPage'],
                        'fontColor' => '#333333',
                        'align'     => 'left'
                    ),
                    'backgroundColor' => ''#EBEBEB'
                ),
                'confirmationPageContent' => array(
                    'image'           => array(
                        'fileName' => 'default.png',
                        'url'      => 'https://s3-eu-west-1.amazonaws.com/salesmanagoimg/ye4vodnswfo6zp75/36m0iryqk4wlt6wu/vi3qhhiwqc485flt.png',
                        'cssClass' => 'fa-envelope'
                    ),
                    'subject'         => $options['confirmation']['subject'],
                    'message'         => array(
                        'text'      => $options['confirmation']['text'],
                        'fontColor' => '#333333',
                        'align'     => 'left'
                    ),
                    'confirmButton'   => array(
                        'text'            => $options['button']['confirm'],
                        'borderRadius'    => '5px',
                        'backgroundColor' => $options['color']['main'],
                        'fontColor'       => $options['color']['mainFont']
                    ),
                    'confirmAction'   => 'DEFAULT',
                    'thankYouMessage' => array(
                        'text'  => $options['confirmation']['message'],
                        'align' => 'left'
                    ),
                    'thankYouUrl'     => ''
                ),
                'settings'                => array(
                    'confirmationEmailAccount' => array(
                        'email' => $options['email']
                    ),
                    'popupName'                => $name,
                    'subscribedTag'            => $options['subscribedTag'],
                    'confirmedTag'             => $options['confirmedTag'],
                    'hideOnMobileDevices'      => false,
                    'hideForMonitoredContacts' => false,
                    'displayWhen'              => array(
                        'mode'              => 'AFTER_X_SECONDS',
                        'siteScrollPercent' => 25,
                        'pageViews'         => 1,
                        'secondsSinceVisit' => 5,
                    ),
                    'displayWhere'             => array(
                        'mode'             => 'ON_ALL_PAGES',
                        'domain'           => '**',
                        'containedPhrases' => [],
                    ),
                ),
                'completedSteps'          => 5,
                'active'                  => true
            )
        );

        return $data;
    }

    protected function __getWebPushConsentData($name, $options)
    {
        $data = array(
            'webPushConsentForm' => array(
                'name'            => $name,
                'tags'            => [],
                'consentForm'     => array(
                    'title'      => $options['title'],
                    'body'       => $options['body'],
                    'imgUrl'     => $options['logo'],
                    'thanksText' => $options['thanksText'],
                    'thanksUrl'  => $options['thanksUrl']
                ),
                'buttonSettings'  => array(
                    'confirmationBackgroundColor' => $options['color']['main'],
                    'confirmationText'            => $options['confirmationText'],
                    'confirmationTextColor'       => $options['color']['mainFont'],
                    'rejectionText'               => $options['rejectionText']
                ),
                'consentFormId'   => null,
                'consentFormSize' => 'MEDIUM',
                'marginTop'       => 0,
                'active'          => true
            )
        );
        return $data;
    }

    public function getModules()
    {
        return $this->modules;
    }

    /**
     * @throws SalesManagoException
     * @var Settings $settings
     * @return array
     */
    public function userItems(Settings $settings)
    {
        $data = array(
            Settings::TOKEN   => $settings->getToken(),
            Settings::API_KEY => $settings->getApiKey()
        );

        $response = $this->request(self::METHOD_POST, self::METHOD_ACCOUNT_ITEMS, $data);
        return $this->validateResponse($response);
    }

    /**
     * @param Settings $settings
     * @return array
     * @throws AccountActiveException
     */
    public function refreshToken(Settings $settings)
    {
        try {
            $response = $this->request(self::METHOD_POST, self::REFRESH_TOKEN, $this->__getDefaultApiData($settings));
            return $this->validateResponse($response);
        } catch (SalesManagoException $e) {
            throw new AccountActiveException('Inactive account', 40);
        }
    }

    /**
     * @var Settings $settings
     * @return string
     */
    public function redirectApp(Settings $settings)
    {
        return $settings->getRequestEndpoint() . self::REDIRECT_APP . '?t=' . $settings->getToken();
    }

    /**
     * @throws SalesManagoException
     * @var Settings $settings
     * @param string $method
     * @param array $options
     * @return array
     */
    public function createProduct(Settings $settings, $method, $options = array())
    {
        switch ($method) {
            case self::METHOD_CREATE_LIVE_CHAT:
                $name = 'SSO Live Chat' . date(' Y-m-d H:i:s', time());
                $productProperties = $this->__getLiveChatData($name, $options);
                break;
            case self::METHOD_CREATE_BASIC_POPUP:
                $name = 'SSO Popup' . date(' Y-m-d H:i:s', time());
                $productProperties = $this->__getBasicPopupData($name, $options);
                break;
            case self::METHOD_CREATE_WEB_PUSH_CONSENT_FORM:
                $name = 'SSO ' . $options['title'] . date(' Y-m-d H:i:s', time());
                $productProperties = $this->__getWebPushConsentData($name, $options);
                break;
            default:
                throw new SalesManagoException('Unsupported data source type', 13);
        }

        $data = array_merge(
            $this->__getDefaultApiData($settings),
            $productProperties
        );

        $response = $this->request(self::METHOD_POST, $method, $data);
        $response['name'] = $name;
        return $this->validateResponse($response);
    }

    /**
     * @throws SalesManagoException
     * @var Settings $settings
     * @param array $modulesId
     * @return array
     */
    public function addSubscribeProducts(Settings $settings, $modulesId)
    {
        $data = array_merge($this->__getDefaultApiData($settings), array(
            'items' => json_encode($this->__getModulesData($modulesId))
        ));

        $response = $this->request(self::METHOD_POST, self::METHOD_ADD_SUBSCRIBE_PRODUCTS, $data);
        return $this->validateResponse($response);
    }

    /**
     * @throws SalesManagoException
     * @var Settings $settings
     * @return array
     */
    public function getUserCustomProperties(Settings $settings)
    {
        $data = array(
            Settings::TOKEN     => $settings->getToken(),
            Settings::CLIENT_ID => $settings->getClientId()
        );

        $response = $this->request(self::METHOD_POST, self::METHOD_GET_INTEGRATION_PROPERTIES, $data);
        if (isset($response['properties'])) {
            $response['properties'] = json_decode($response['properties'], true);
        }
        return $this->validateResponse($response);
    }

    /**
     * @throws SalesManagoException
     * @var Settings $settings
     * @param string $properties
     * @return array
     */
    public function setUserCustomProperties(Settings $settings, $properties)
    {
        $data = array(
            Settings::TOKEN     => $settings->getToken(),
            Settings::CLIENT_ID => $settings->getClientId(),
            'properties'        => json_encode($properties)
        );

        $response = $this->request(self::METHOD_POST, self::METHOD_SET_INTEGRATION_PROPERTIES, $data);
        return $this->validateResponse($response);
    }

    /**
     * @throws SalesManagoException
     * @var Settings $settings
     * @param array $upsertDetails
     * @return array
     */
    public function exportContacts(Settings $settings, $upsertDetails)
    {
        $data = array_merge($this->__getDefaultApiData($settings), array(
            'upsertDetails' => $upsertDetails,
        ));

        $response = $this->request(self::METHOD_POST, self::METHOD_BATCH_UPSERT, $data);
        return $this->validateResponse($response);
    }

    /**
     * @throws SalesManagoException
     * @var Settings $settings
     * @param array $events
     * @return array
     */
    public function exportContactExtEvents(Settings $settings, $events)
    {
        $data = array_merge($this->__getDefaultApiData($settings), array(
            'events' => $events,
        ));

        $response = $this->request(self::METHOD_POST, self::METHOD_BATCH_ADD_EXT_EVENT, $data);
        return $this->validateResponse($response);
    }

    /**
     * @param array $userData
     * @return array
     * @throws SalesManagoException
     */
    public function getAccountTypeWithContacts($userData)
    {
        $response = $this->request(self::METHOD_POST, self::METHOD_ACCOUNT_TYPE, $userData);
        return $this->validateResponse($response);
    }

    /**
     * @param Settings $settings
     * @param array $itemData
     * @return array
     * @throws SalesManagoException
     */
    public function itemAction(Settings $settings, $itemData)
    {
        $data = array_merge($this->__getDefaultApiData($settings), $itemData);

        $response = $this->request(self::METHOD_POST, self::METHOD_ITEM_ACTION, $data);
        return $this->validateResponse($response);
    }

    /**
     * @param array $userData
     * @return array
     * @throws SalesManagoException
     */
    public function consentFormCode($userData)
    {
        $response = $this->request(
            self::METHOD_POST,
            self::METHOD_CONSENT_FORM_CODE,
            array(
                'token'       => $userData['token'],
                'wizardIntId' => $userData['id']
            ));
        return $this->validateResponse($response);
    }

    /**
     * @param Settings $settings
     * @param $image
     * @return array
     * @throws SalesManagoException
     */
    public function uploadImage(Settings $settings, $image)
    {
        $this->setClient(
            $settings,
            array(
                'Content-Type' => 'multipart/form-data'
            )
        );

        $response = $this->requestCustomizable(
            self::METHOD_POST,
            self::METHOD_UPLOAD_IMAGE,
            array(
                'multipart' => array(
                    array(
                        'name'     => 'attachment',
                        'contents' => fopen($image['tmp_name'], 'r'),
                        'filename' => $image['name']
                    ),
                    array(
                        'name'     => Settings::CLIENT_ID,
                        'contents' => $settings->getClientId(),
                    ),
                    array(
                        'name'     => Settings::API_KEY,
                        'contents' => $settings->getApiKey(),
                    ),
                    array(
                        'name'     => Settings::SHA,
                        'contents' => $settings->getSha(),
                    ),
                    array(
                        'name'     => Settings::EMAIL,
                        'contents' => $settings->getOwner(),
                    )
                ),
            )
        );
        return $this->validateResponse($response);
    }

    /**
     * @param Settings $settings
     * @return array
     * @throws SalesManagoException
     */
    public function listUsersByClient(Settings $settings)
    {
        $response = $this->request(self::METHOD_POST, self::METHOD_LIST_USERS, $this->__getDefaultApiData($settings));
        return $this->validateResponse($response);
    }
}
