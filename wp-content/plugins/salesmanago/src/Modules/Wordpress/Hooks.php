<?php

namespace bhr\Modules\Wordpress;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use SALESmanago\Provider\UserProvider;
use SALESmanago\Controller\ConnectSalesManagoController;

use bhr\Modules\Newsletter\Context;
use bhr\Modules\Newsletter\Newsletter;

use bhr\Helper\Tagger;

class Hooks
{
    private $model;
    private $controller;
    private $ContactModel;
    private $NewsletterContext;
    private $Tagger;

    public function __construct()
    {
        $this->model      = new HooksModel();
        $this->controller = new ConnectSalesManagoController(
            UserProvider::initSettingsUser(
                new HooksModel()
            )
        );

        $this->ContactModel = new ContactModel();
        $this->Tagger       = new Tagger($this->model);

	    $Context = new Context(new Newsletter);
	    $this->NewsletterContext = $Context->getContext();

        $this->initHooks();
    }

    public function initHooks()
    {
        add_action('user_register', array($this, 'registerUser'));
        add_action('wp_login', array($this, 'loginUser'));
    }

    public function registerUser($user_id)
    {
        try {
            $Contact = $this->ContactModel->get($user_id);

	        $Contact = $this->NewsletterContext
	           ->setContact($Contact)
               ->setContactOptStates();

	        $Contact = $this->Tagger
	            ->setContact($Contact)
	            ->setTags(Tagger::T_REGISTER);

            if ($synchronizeRule = $this->model->getSynchronizeRule()) {
                $Contact->getOptions()->setCustomOptions($synchronizeRule);
            }

            $contact = $Contact->getDataOptions();

            $response = $this->controller->contactUpsert($contact['data'], $contact['options']);

            if (!empty($response['contactId'])) {
                $this->controller->createCookie(
                    ConnectSalesManagoController::COOKIES_CLIENT,
                    $response['contactId']
                );
            }
        } catch (\Exception $e) {
            error_log(print_r($e->getMessage(), true));
        }
    }

    public function loginUser($userLogin)
    {
        try {
            if (!$this->checkUserLevel($userLogin)) {
                return true;
            }

            $Contact = $this->ContactModel->get('', 'login', $userLogin);

	        $Contact = $this->NewsletterContext
		        ->setContact($Contact)
		        ->setContactOptStates();

	        $Contact = $this->Tagger
		        ->setContact($Contact)
		        ->setTags(Tagger::T_LOGIN);

	        $contact = $Contact->getDataOptions();
            $response = $this->controller->contactUpsert($contact['data'], $contact['options']);

            if (!empty($response['contactId'])) {
                $this->controller->createCookie(
                    ConnectSalesManagoController::COOKIES_CLIENT,
                    $response['contactId']
                );
            }
        } catch (\Exception $e) {
            error_log(print_r($e->getMessage(), true));
        }
    }

    protected function checkUserLevel($contactIdentify)
    {
        $contact = (!get_user_by('email', $contactIdentify))
            ? get_user_by('login', $contactIdentify)
            : get_user_by('email', $contactIdentify);

        if (empty($contact)) {
            return true;
        }

        $contact = $contact->get_role_caps();

        if ($contact['level_4']
            || $contact['level_4']
            || $contact['level_5']
            || $contact['level_6']
            || $contact['level_7']
            || $contact['level_8']
            || $contact['level_9']
            || $contact['level_10']
        ) {
            return false;
        }

        return true;
    }
}
