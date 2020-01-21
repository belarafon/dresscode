<?php

namespace SALESmanago\Services;

use SALESmanago\Exception\SalesManagoException;
use SALESmanago\Entity\Settings;


class CreateAccountService extends AbstractClient implements CreateAccountInterface
{
    public function __construct(Settings $settings)
    {
        $this->setClient($settings);
    }

    /**
     * @throws SalesManagoException
     * @var Settings $settings
     * @param array $user
     * @param array $modulesId
     * @return array
     */
    public function createAccount(Settings $settings, $user, $modulesId)
    {
        $data = array_merge($this->__getDefaultApiData($settings), array(
            'email'    => $user['email'],
            'password' => $user['password'],
            'lang'     => $user['lang'],
            'items'    => json_encode($this->__getModulesData($modulesId))
        ));

        if (isset($user['tags'])) {
            $data['tags'] = $user['tags'];
        }

        $response = $this->request(self::METHOD_POST, self::METHOD_CREATE_ACCOUNT, $data);
        return $this->validateResponse($response);
    }

    /**
     * @param Settings $settings
     * @param array $userDetails
     * @return array
     * @throws SalesManagoException
     */
    public function contactToSupport(Settings $settings, $userDetails)
    {
        $data = array_merge($this->__getDefaultApiData($settings), $userDetails);

        if ($settings->count($settings->getTags()) > 0) {
            $data['tags'] = $settings->getTags();
        }

        if ($settings->count($settings->getRemoveTags()) > 0) {
            $data['removeTags'] = $settings->getRemoveTags();
        }

        if ($settings->count($settings->getProperties()) > 0) {
            $data['properties'] = $settings->getProperties();
        }

        $response = $this->request(self::METHOD_POST, self::METHOD_CONTACT_SUPPORT, $data);
        return $this->validateCustomResponse($response, array(array_key_exists('contactId', $response)));
    }
}
