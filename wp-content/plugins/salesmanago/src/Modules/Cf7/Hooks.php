<?php

namespace bhr\Modules\Cf7;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use SALESmanago\Controller\ConnectSalesManagoController;
use SALESmanago\Exception\SalesManagoException;
use SALESmanago\Provider\UserProvider;
use WPCF7_Submission;

class Hooks
{
    public $controller;
    public $settings;
    public $model;

    private $ContactModel;
    private $currentFormConf = false;

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
        add_action('wpcf7_mail_sent', array($this, 'execute'), 5);
    }

    public function execute($contact_form)
    {
        try {
            $submission = WPCF7_Submission::get_instance();

            if (!$submission) {
                return false;
            }

            $this->currentFormConf = $this->model->getCurrentFormConfig($contact_form->title());

            if (!$this->currentFormConf) {
                return false;
            }

            $Contact = $this->ContactModel
                ->setParameters(
                    [
                        'formData' => $submission->get_posted_data(),
                        'currentFormConf' => $this->currentFormConf,
                        'config' => $this->model->getConfig()
                    ]
                )->get();


            $contact = $Contact->getDataOptions();

            if ($this->currentFormConf) {
                if ($this->currentFormConf['owner']) {
                    $this->settings->setOwner($this->currentFormConf['owner']);
                }
                $result = $this->controller->contactUpsert(
                    $contact['data'],
                    $contact['options']
                );

                if ($result['success'] && isset($result['contactId'])) {

                    $this->controller->createCookie(
                        ConnectSalesManagoController::COOKIES_CLIENT,
                        $result['contactId']
                    );

                    return true;
                }
            }
        } catch (\Exception $e) {
            error_log(print_r($e->getMessage(), true));
            return false;
        } catch (SalesManagoException $e) {
            error_log(print_r($e->getMessage(), true));
            return false;
        }
    }
}
