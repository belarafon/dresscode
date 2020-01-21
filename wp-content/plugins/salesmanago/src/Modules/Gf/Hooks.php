<?php

namespace bhr\Modules\Gf;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use SALESmanago\Controller\ConnectSalesManagoController;
use SALESmanago\Exception\SalesManagoException;
use SALESmanago\Provider\UserProvider;

class Hooks
{
    private $controller;
    private $settings;

    private $model;
    private $ContactModel;

    private $form;
    private $gfConfig;
    private $formConfig;
    private $entry;

    public function __construct()
    {
        $this->model = new HooksModel();
        $this->settings = UserProvider::initSettingsUser($this->model);
        $this->controller = new ConnectSalesManagoController($this->settings);
        $this->ContactModel = new ContactModel($this->controller, $this->model);
        $this->initHooks();
    }

    public function initHooks()
    {
        add_action('gform_after_submission', array($this, 'execute'), 10, 2);
    }

    /**
     * Hooks method
     * @param array $entry
     * @param array $form
     * @return boolean
    */
    public function execute($entry, $form)
    {
        try {
            if (!isset($entry) || empty($entry)) {
                return false;
            }

            $this->entry = $entry;
            $this->form = $form;

            $this->gfConfig = $this->model->getConfig();

            if (isset($this->form)) {
                $config = $this->getCurrentFormConf($this->form);
                if ($config) {
                    $this->formConfig = $config;
                }
            }

            $this->setOwner();
            $this->contactUpsert();
            return true;
        } catch (\Exception $e) {
            error_log(print_r($e->getMessage(), true));
            return false;
        }
    }

    /**
     * Return configuration of form if it exist
     * @param array $form current form
     * @return mixed (array || boolean)
     */
    private function getCurrentFormConf($form = null)
    {
        $form = ($form === null && isset($this->form))
            ? $this->form
            : '';

        $gfConfig = $this->model->getConfig();

        if (empty($form)) {
            return false;
        }

        foreach ($gfConfig['form'] as $formConfig) {
            if ($formConfig['name'] == $form['title']) {
                $this->formConfig = $formConfig;
                return $formConfig;
            }
        }

        return false;
    }

    /**
     * Form contact upsert to SM
    */
    private function contactUpsert()
    {
        try {
            $gfConfig = (!isset($this->gfConfig))
                ? $this->model->getConfig()
                : $this->gfConfig;

            if ($formConfig = $this->getCurrentFormConf()) {
                $contact = $this->ContactModel
                    ->setParameters(
                        [
                            'entry' => $this->entry,
                            'form' => $this->form,
                            'gfConfig' => $gfConfig,
                            'formConfig' => $this->formConfig
                        ]
                    )
                    ->get()
                    ->getDataOptions();
            }

            if (!isset($contact)) {
                return false;
            }

            $result = $this->controller->contactUpsert($contact['data'], $contact['options']);

            if ($result['success'] && isset($result['contactId'])) {
                $this->controller->createCookie(
                    ConnectSalesManagoController::COOKIES_CLIENT,
                    $result['contactId']
                );
                return true;
            }
        } catch (SalesManagoException $e) {
            error_log(print_r($e->getMessage(), true));
        }
    }

    /**
     * Add special owner for form contact
     * @return boolean true if contact sets false otherwise;
    */
    private function setOwner()
    {
        if (
            isset($this->formConfig)
            && isset($this->formConfig['owner'])
            && $this->formConfig['owner'] != ''
        ) {
            $this->settings->setOwner($this->formConfig['owner']);
            return true;
        }

        return false;
    }
}
