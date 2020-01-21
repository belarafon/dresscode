<?php

namespace bhr\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bhr\Helper\Functions;
use bhr\Helper\HooksFiltersManager as HFM;

use bhr\Controller\AbstractExportController;
use bhr\Modules\WooCommerce\CronExportController;
use SALESmanago\Exception\Exception;
use SALESmanago\Provider\UserProvider;
use SALESmanago\Exception\UserAccessException;
use SALESmanago\Exception\SalesManagoException;
use SALESmanago\Controller\CreateAccountController as Create;
use SALESmanago\Controller\LoginAccountController as Login;

use bhr\Helper\ModulesConfigManager;

use bhr\Controller\UserController as WordpressUserController;
use bhr\Controller\IntegrationController as WordpressIntegrationController;

use bhr\Model\UserModel;
use bhr\Model\LoginModel;
use bhr\Model\CreateModel;
use bhr\Model\IntegrationModel;
use bhr\Modules\WooCommerce\PurchaseModel;

use bhr\Controller\ModulesController;

use bhr\Modules\Wordpress\Controller as WordpressController;
use bhr\Modules\Wordpress\Model as WordpressModel;

use bhr\Modules\WooCommerce\Controller as WooCommerceController;
use bhr\Modules\WooCommerce\Model as WooCommerceModel;

use bhr\Modules\WooCommerce\Hooks as WcHooks;

use bhr\Modules\Cf7\Controller as Cf7Controller;
use bhr\Modules\Cf7\Model as Cf7Model;

use bhr\Modules\Gf\Controller as GfController;
use bhr\Modules\Gf\Model as GfModel;

use bhr\Modules\Newsletter\Controller as NewsletterController;


class RestApi
{
    public function __construct()
    {
        add_action('rest_api_init', array(get_class($this), 'registerEndpoints'));
        $cron = new PurchaseModel();

        if($cron->type = 'cron'){
            if($cron->config == 1){
                if (! wp_next_scheduled( 'salesmanagoCronHook' ) ) {
                    wp_schedule_event( time(), 'hourly', 'salesmanagoCronHook' );
                }
            } elseif ($cron->config == 12){
                if (! wp_next_scheduled( 'salesmanagoCronHook' ) ) {
                    wp_schedule_event( time(), 'twicedaily', 'salesmanagoCronHook' );
                }
            } elseif ($cron->config == 24){
                if (! wp_next_scheduled( 'salesmanagoCronHook' ) ) {
                    wp_schedule_event( time(), 'daily', 'salesmanagoCronHook' );
                }
            } else {
                wp_clear_scheduled_hook('salesmanagoCronHook');
            }

            add_action('salesmanagoCronHook', array($this, 'exportCronOrders'));
        }
    }

    public static function registerEndpoints()
    {
    	try {
    		/* use method from WooCommerce Hooks */
	        register_rest_route('salesmanago/v1', '/recover', array(
	            'methods'  => \WP_REST_Server::READABLE,
	            'callback' => array('bhr\Admin\RestApi', 'recoverWooCommerceCart'),
	        ));

	        register_rest_route('salesmanago/v1', '/create', array(
	            'methods'  => \WP_REST_Server::CREATABLE,
	            'callback' => array('bhr\Admin\RestApi', 'createAccount'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

            register_rest_route('salesmanago/v1', '/login', array(
                'methods' => \WP_REST_Server::CREATABLE,
                'callback' => array('bhr\Admin\RestApi', 'login'),
                'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
                'args' => [
                    'username' => [
                        'required' => true,
                        'type' => 'string'
                    ],
                    'password' => [
                        'required' => true,
                        'type' => 'string'
                    ]
                ]
            ));

	        register_rest_route('salesmanago/v1', '/logout', array(
	            'methods'  => \WP_REST_Server::READABLE,
	            'callback' => array('bhr\Admin\RestApi', 'logout'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/token', array(
	            'methods'  => \WP_REST_Server::READABLE,
	            'callback' => array('bhr\Admin\RestApi', 'token'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/extensions', array(
	            'methods'  => \WP_REST_Server::READABLE,
	            'callback' => array('bhr\Admin\RestApi', 'getExtensions'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/extensions', array(
	            'methods'  => \WP_REST_Server::CREATABLE,
	            'callback' => array('bhr\Admin\RestApi', 'setExtensions'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/woocommerce', array(
	            'methods'  => \WP_REST_Server::READABLE,
	            'callback' => array('bhr\Admin\RestApi', 'getWooCommerce'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/woocommerce', array(
	            'methods'  => \WP_REST_Server::CREATABLE,
	            'callback' => array('bhr\Admin\RestApi', 'setWooCommerce'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

		    register_rest_route('salesmanago/v1', '/wordpress', array(
			    'methods'  => \WP_REST_Server::READABLE,
			    'callback' => array('bhr\Admin\RestApi', 'getWordPress'),
			    'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
		    ));

		    register_rest_route('salesmanago/v1', '/wordpress', array(
			    'methods'  => \WP_REST_Server::CREATABLE,
			    'callback' => array('bhr\Admin\RestApi', 'setWordPress'),
			    'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
		    ));

	        register_rest_route('salesmanago/v1', '/account', array(
	            'methods'  => \WP_REST_Server::READABLE,
	            'callback' => array('bhr\Admin\RestApi', 'getAccount'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/listUsers', array(
	            'methods'  => \WP_REST_Server::READABLE,
	            'callback' => array('bhr\Admin\RestApi', 'getListUsers'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/account', array(
	            'methods'  => \WP_REST_Server::CREATABLE,
	            'callback' => array('bhr\Admin\RestApi', 'setAccount'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/shopSettings', array(
	            'methods'  => \WP_REST_Server::READABLE,
	            'callback' => array('bhr\Admin\RestApi', 'getShopSettings'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/createProduct', array(
	            'methods'  => \WP_REST_Server::CREATABLE,
	            'callback' => array('bhr\Admin\RestApi', 'createProduct'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/integration', array(
	            'methods'  => \WP_REST_Server::CREATABLE,
	            'callback' => array('bhr\Admin\RestApi', 'setIntegration'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/picture', array(
	            'methods'  => \WP_REST_Server::CREATABLE,
	            'callback' => array('bhr\Admin\RestApi', 'setPicture'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/accountType', array(
	            'methods'  => \WP_REST_Server::READABLE,
	            'callback' => array('bhr\Admin\RestApi', 'accountType'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/itemAction', array(
	            'methods'  => \WP_REST_Server::CREATABLE,
	            'callback' => array('bhr\Admin\RestApi', 'itemAction'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/consentFormCode', array(
	            'methods'  => \WP_REST_Server::CREATABLE,
	            'callback' => array('bhr\Admin\RestApi', 'consentFormCode'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/newsletter', array(
	            'methods'  => \WP_REST_Server::READABLE,
	            'callback' => array('bhr\Admin\RestApi', 'getNewsletter'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/newsletter', array(
	            'methods'  => \WP_REST_Server::CREATABLE,
	            'callback' => array('bhr\Admin\RestApi', 'setNewsletter'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/cf7', array(
	            'methods'  => \WP_REST_Server::READABLE,
	            'callback' => array('bhr\Admin\RestApi', 'getCf7'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/cf7', array(
	            'methods'  => \WP_REST_Server::CREATABLE,
	            'callback' => array('bhr\Admin\RestApi', 'setCf7'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/gf', array(
	            'methods'  => \WP_REST_Server::READABLE,
	            'callback' => array('bhr\Admin\RestApi', 'getGf'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/gf', array(
	            'methods'  => \WP_REST_Server::CREATABLE,
	            'callback' => array('bhr\Admin\RestApi', 'setGf'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

	        register_rest_route('salesmanago/v1', '/parent', array(
	            'methods'  => \WP_REST_Server::CREATABLE,
	            'callback' => array('bhr\Admin\RestApi', 'getModuleParentState'),
	            'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
	        ));

		    register_rest_route('salesmanago/v1', '/countContacts', array(
			    'methods'  => \WP_REST_Server::CREATABLE,
			    'callback' => array('bhr\Admin\RestApi', 'countContacts'),
			    'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
		    ));

		    register_rest_route('salesmanago/v1', '/exportContacts', array(
			    'methods'  => \WP_REST_Server::CREATABLE,
			    'callback' => array('bhr\Admin\RestApi', 'exportContacts'),
			    'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
		    ));

		    register_rest_route('salesmanago/v1', '/exportOrders', array(
			    'methods'  => \WP_REST_Server::CREATABLE,
			    'callback' => array('bhr\Admin\RestApi', 'exportOrders'),
			    'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
		    ));

		    register_rest_route('salesmanago/v1', '/countOrders', array(
			    'methods'  => \WP_REST_Server::CREATABLE,
			    'callback' => array('bhr\Admin\RestApi', 'countOrders'),
			    'permission_callback' => array('bhr\Admin\RestApi', 'userAuth'),
		    ));



	    } catch (\Exception $e) {
    		error_log( $e->getMessage() );
	    }
    }

    public static function userAuth($request) {
	    self::updateNonce();
        return $request->get_param('auth_token') === sha1($_COOKIE[LOGGED_IN_COOKIE]);
    }

    public static function updateNonce() {
	    wp_localize_script( 'sm_sso', 'wpApiSettings', array(
		    'root' => esc_url_raw( rest_url() ),
		    'nonce' => wp_create_nonce( 'wp_rest' )
	    ) );
    }

    public static function createAccount($request)
    {
        try {
            $params = $request->get_body_params();

            $controller = new Create(UserProvider::settingsAccount(), new CreateModel());
            $response = $controller->createAccount(
                array(
                    'email' => htmlspecialchars($params['username']),
                    'password' => htmlspecialchars($params['password']),
                    'lang' => htmlspecialchars($params['lang']),
                    'name' => htmlspecialchars($params['name']),
                    'phone' => htmlspecialchars($params['phone']),
                    'website' => htmlspecialchars($params['website']),
                    'consentDetails' => $params['consentDetails'],
                    'platform' => 'WordPress'
                ),
                ['0', '1', '2', '3']
            );

            return new \WP_REST_Response($response, 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function login($request)
    {
        try {
            $params = $request->get_body_params();

            $settings = UserProvider::settingsUser();
            $settings = HFM::applyFilters('settings-before-login', $settings);
            $controller = new Login($settings, new LoginModel());

            $response = $controller->loginUser($params);

            return new \WP_REST_Response($response, 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function logout()
    {
        try {
            $controller = new WordpressUserController(new IntegrationModel());
            $response = $controller->logout();

            return new \WP_REST_Response($response, 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function token()
    {
        try {
            $model      = new UserModel();
            $controller = new WordpressIntegrationController(UserProvider::initSettingsUser($model), $model);
            $response   = $controller->getToken();
            $response['properties']['success'] = true;

            return new \WP_REST_Response($response, 200);
        } catch (UserAccessException $e) {
            return new \WP_REST_Response($e->getUserMessage(), 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function getExtensions()
    {
        try {
            $controller = new ModulesController();
            $response = $controller->getModules();

            return new \WP_REST_Response($response, 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function setExtensions($request)
    {
        try {
            $params = $request->get_body_params();

            $controller = new ModulesController();
            $response = $controller->setModules($params);

            return new \WP_REST_Response($response, 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function setWooCommerce($request)
    {
        $params = $request->get_body_params();
        $controller = new WooCommerceController(new WooCommerceModel());

        $response = $controller->setConfig(
            array(
                'wc' => array(
                    'cartUrl' => htmlspecialchars($params['cartUrl']),
                    'tags'    => array(
                        'registration' => htmlspecialchars($params['registration']),
                        'login'        => htmlspecialchars($params['login']),
                        'purchase'     => htmlspecialchars($params['purchase']),
                        'newsletter'   => htmlspecialchars($params['newsletter'])
                    ),
                    'event_config' => array(
                        'type'         => htmlspecialchars($params['event_config']['type']),
                        'hookConfig'   => htmlspecialchars($params['event_config']['hookConfig']),
                        'cronConfig'   => htmlspecialchars($params['event_config']['cronConfig'])
                    )
                )
            )
        );

        return new \WP_REST_Response($response, 200);
    }

    public static function getListUsers()
    {
        try {
            $model = new UserModel();
            $controller = new WordpressIntegrationController(UserProvider::initSettingsUser($model), $model);
            $response = $controller->listUsersByClient();

            return new \WP_REST_Response($response, 200);
        } catch (UserAccessException $e) {
            return new \WP_REST_Response($e->getUserMessage(), 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

	public static function getAccount()
	{
		try {
			$controller = new WordpressUserController(new IntegrationModel());
			$response = $controller->getAccountUserData();

			return new \WP_REST_Response($response, 200);
		} catch (SalesManagoException $e) {
			return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
		}
	}

    public static function setAccount($request)
    {
        try {
        $params = $request->get_body_params();

        $controller = new WordpressUserController(new IntegrationModel());
        $response = $controller->setAccountUserData($params);

        return new \WP_REST_Response($response, 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function getShopSettings()
    {
        try {
            $model = new UserModel();
            $controller = new WordpressIntegrationController(UserProvider::initSettingsUser($model), $model);
            $response = $controller->getUserItems();

            return new \WP_REST_Response($response, 200);
        } catch (UserAccessException $e) {
            return new \WP_REST_Response($e->getUserMessage(), 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function createProduct($request)
    {
        try {
            $params = $request->get_body_params();

            $model = new UserModel();
            $controller = new WordpressIntegrationController(UserProvider::initSettingsUser($model), $model);
            $response = $controller->createProduct($params['name'], $params['properties']);

            return new \WP_REST_Response($response, 200);
        } catch (UserAccessException $e) {
            return new \WP_REST_Response($e->getUserMessage(), 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function setIntegration($request)
    {
        try {
            $params = $request->get_body_params();
            $model = new UserModel();
            $controller = new WordpressIntegrationController(
                UserProvider::initSettingsUser($model),
                $model
            );
            $response = $controller->setUserIntegration(
                array(
                    'properties'  => array(
                        'lang'  => $params['lang'],
                        'color' => array(
                            'main'           => $params['color']['main'],
                            'mainFont'       => $params['color']['mainFont'],
                            'additional'     => $params['color']['additional'],
                            'additionalFont' => $params['color']['additionalFont']
                        )
                    )
                )
            );

            return new \WP_REST_Response($response, 200);
        } catch (UserAccessException $e) {
            return new \WP_REST_Response($e->getUserMessage(), 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function setPicture()
    {
        try {
            $model = new UserModel();
            $controller = new WordpressIntegrationController(
                UserProvider::initSettingsUser($model),
                $model
            );

            $response = $controller->uploadImage($_FILES['attachment']);

            return new \WP_REST_Response($response, 200);
        } catch (UserAccessException $e) {
            return new \WP_REST_Response($e->getUserMessage(), 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function accountType()
    {
        try {
            $model = new UserModel();
            $controller = new WordpressIntegrationController(UserProvider::initSettingsUser($model), $model);
            $response = $controller->getAccountTypeWithContacts();

            return new \WP_REST_Response($response, 200);
        } catch (UserAccessException $e) {
            return new \WP_REST_Response($e->getUserMessage(), 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function countContacts($request)
    {
	    try {
		    $params = $request->get_body_params();

		    $exportControllers = ModulesConfigManager::getInstance()
		                                             ->getExportControllers($params);
		    $counted = (integer)0;

		    foreach ($exportControllers as $ExportController) {
		    	$counted += $ExportController
				    ->checkForAdvancedExport()
				    ->countPlatformContacts();
		    }

		    $response = array(
		    	'success' => true,
				'contacts' => $counted
			    );

		    return new \WP_REST_Response($response, 200);
	    } catch (SalesManagoException $e) {
		    return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
	    }
    }

    /**
     * @param $request - reguest parameters from view
     * @throws \Exception
     * @return \WP_REST_Response
    */
    public static function exportContacts($request)
    {
	    try {
		    $params = $request->get_body_params();

		    $exportControllers = ModulesConfigManager::getInstance()
		                                             ->getExportControllers($params);

		    $totalExportInfo = AbstractExportController::$exportResponse;

		    foreach ($exportControllers as $ExportController) {
			    $packagesInfo = $ExportController
				    ->checkForAdvancedExport()
				    ->exportPlatformContacts();

			    $totalExportInfo['total']       += $packagesInfo['total'];
			    $totalExportInfo['successful']  += $packagesInfo['successful'];
			    $totalExportInfo['lost']        += $packagesInfo['lost'];
			    $totalExportInfo['message']     .= $packagesInfo['message'].', - ';
		    }

		    $response = array(
			    'success' => true,
			    'message' => 'Total exported packages: '.$totalExportInfo['total'].'; '
		    );

		    $response['message'] .= ' Successful exported packages: '.$totalExportInfo['successful'].'; ';
		    $response['message'] .= ' Lost packages: '.$totalExportInfo['lost'].'; ';
		    $response['message'] .= ' Messages: '.$totalExportInfo['message'].'; ';

		    return new \WP_REST_Response($response, 200);

	    } catch (SalesManagoException $e) {
		    return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
	    } catch (\Exception $e) {
		    return new \WP_REST_Response($e->getMessage(), 200);
	    }
    }

	public static function countOrders($request)
	{
		try {
			$params   = $request->get_body_params();
			$counted = (int)0;
			$exportControllers = ModulesConfigManager::getInstance()
			                                         ->getExportControllers($params);

			foreach ($exportControllers as $ExportController) {
				$counted += $ExportController
					->checkForAdvancedExport()
					->countPlatformEvents();
			}

			$response = [
				'success' => 'success',
				'orders' => $counted
				];

			return new \WP_REST_Response($response, 200);
		} catch (SalesManagoException $e) {
			return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
		} catch (\Exception $e) {
			return new \WP_REST_Response($e->getMessage(), 200);
		}
	}

    public static function exportOrders($request)
    {
    	try {
		    $params   = $request->get_body_params();

		    $exportControllers = ModulesConfigManager::getInstance()
		                                             ->getExportControllers($params);
		    $totalExportInfo = AbstractExportController::$exportResponse;

		    foreach ( $exportControllers as $ExportController ) {
			    $packagesInfo = $ExportController
				    ->checkForAdvancedExport()
				    ->exportPlatformExternalEvent( $params );

			    $totalExportInfo['total']       += $packagesInfo['total'];
			    $totalExportInfo['successful']  += $packagesInfo['successful'];
			    $totalExportInfo['lost']        += $packagesInfo['lost'];
			    $totalExportInfo['items']       += $packagesInfo['items'];
			    $totalExportInfo['message']     .= $packagesInfo['message'].', - ';
		    }

		    $response = array(
			    'success' => true,
			    'message' => 'Total exported packages: '.$totalExportInfo['total'].'; '
		    );

		    $response['message'] .= ' Successful exported packages: '.$totalExportInfo['successful'].'; ';
		    $response['message'] .= ' Lost packages: '.$totalExportInfo['lost'].'; ';
		    $response['message'] .= ' Items: '.$totalExportInfo['items'].'; ';
		    $response['message'] .= ' Messages: '.$totalExportInfo['message'].'; ';

		    return new \WP_REST_Response($response, 200);
	    } catch (SalesManagoException $e) {
		    return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
	    } catch (\Exception $e) {
		    return new \WP_REST_Response($e->getMessage(), 200);
	    }
    }


    public static function exportCronOrders()
    {
        try {
            $exportController = new CronExportController();

            $totalExportInfo = AbstractExportController::$exportResponse;

                $packagesInfo = $exportController->exportPlatformExternalEvent();

                $totalExportInfo['total']       += $packagesInfo['total'];
                $totalExportInfo['successful']  += $packagesInfo['successful'];
                $totalExportInfo['lost']        += $packagesInfo['lost'];
                $totalExportInfo['items']       += $packagesInfo['items'];
                $totalExportInfo['message']     .= $packagesInfo['message'].', - ';


            $response = array(
                'success' => true,
                'message' => 'Total exported packages: '.$totalExportInfo['total'].'; '
            );

            $response['message'] .= ' Successful exported packages: '.$totalExportInfo['successful'].'; ';
            $response['message'] .= ' Lost packages: '.$totalExportInfo['lost'].'; ';
            $response['message'] .= ' Items: '.$totalExportInfo['items'].'; ';
            $response['message'] .= ' Messages: '.$totalExportInfo['message'].'; ';

            return new \WP_REST_Response($response, 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        } catch (\Exception $e) {
            return new \WP_REST_Response($e->getMessage(), 200);
        }
    }


    public static function getWordPress()
    {
    	try {
		    $controller = new WordpressController( new WordpressModel() );
		    $response   = $controller->getConfig();

		    return new \WP_REST_Response( $response, 200 );
	    } catch (SalesManagoException $e) {
		    return new \WP_REST_Response( $e->getMessage(), 200 );
	    }
    }

    public static function setWordPress($request)
    {
    	try {
		    $params     = $request->get_body_params();
		    $controller = new WordpressController( new WordpressModel() );

		    $response = $controller->setConfig(
			    array(
				    'wp' => array(
					    'tags' => array(
						    'registration' => htmlspecialchars( $params['registration'] ),
						    'login'        => htmlspecialchars( $params['login'] ),
						    'newsletter'   => htmlspecialchars( $params['newsletter'] )
					    )
				    )
			    )
		    );

		    return new \WP_REST_Response( $response, 200 );
	    } catch (SalesManagoException $e) {
		    return new \WP_REST_Response( $e->getMessage(), 200 );
	    }
    }

    public static function getWooCommerce()
    {
        try {
            $controller = new WooCommerceController(new WooCommerceModel());
            $response = $controller->getConfig();

            return new \WP_REST_Response($response, 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function recoverWooCommerceCart($request)
    {
		WcHooks::recoverCart( $request );
    }

    public static function getNewsletter($request)
    {
        try {
            $newsletterController = new NewsletterController();
            $response = $newsletterController->getConfig($request);
            $response['success'] = true;
            return new \WP_REST_Response($response, 200);
        } catch (UserAccessException $e) {
            return new \WP_REST_Response($e->getUserMessage(), 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function setNewsletter($request)
    {
        try {
            $params = $request->get_body_params();
            $newsletterController = new NewsletterController($params['type']);
            $response['success'] = $newsletterController->setConfig($params);

            return new \WP_REST_Response($response, 200);
        } catch (UserAccessException $e) {
            return new \WP_REST_Response($e->getUserMessage(), 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function getCf7()
    {
        try {
            $model = new UserModel();
            $controller = new Cf7Controller(
                UserProvider::initSettingsUser($model),
                $model,
                new Cf7Model()
            );
            $response = $controller->getConfig();

            return new \WP_REST_Response($response, 200);
        } catch (UserAccessException $e) {
            return new \WP_REST_Response($e->getUserMessage(), 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function setCf7($request)
    {
        try {
            $params = $request->get_body_params();
            $model = new UserModel();
            $controller = new Cf7Controller(
                UserProvider::initSettingsUser($model),
                $model,
                new Cf7Model()
            );
            $response = $controller->setConfig($params);

            return new \WP_REST_Response($response, 200);
        } catch (UserAccessException $e) {
            return new \WP_REST_Response($e->getUserMessage(), 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function getGf()
    {
        try {
            $model = new UserModel();
            $controller = new GfController(
                UserProvider::initSettingsUser($model),
                $model
            );
            $response = $controller->getConfig();

            return new \WP_REST_Response($response, 200);
        } catch (UserAccessException $e) {
            return new \WP_REST_Response($e->getUserMessage(), 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function setGf($request)
    {
        try {
            $params = $request->get_body_params();
            $model = new UserModel();
            $controller = new GfController(
                UserProvider::initSettingsUser($model),
                $model,
                new GfModel()
            );
            $response = $controller->setConfig($params);

            return new \WP_REST_Response($response, 200);
        } catch (UserAccessException $e) {
            return new \WP_REST_Response($e->getUserMessage(), 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function itemAction($request)
    {
        try {
            $params = $request->get_body_params();
            $model = new UserModel();
            $controller = new WordpressIntegrationController(UserProvider::initSettingsUser($model), $model);
            $response = $controller->itemAction(
                array(
                    "type" => htmlspecialchars($params['type']),
                    "item" => array(
                        "id"     => htmlspecialchars($params['id']),
                        "active" => filter_var($params['active'], FILTER_VALIDATE_BOOLEAN)
                    )
                )
            );

            return new \WP_REST_Response($response, 200);
        } catch (UserAccessException $e) {
            return new \WP_REST_Response($e->getUserMessage(), 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function consentFormCode($request)
    {
        try {
            $params = $request->get_body_params();
            $model = new UserModel();
            $controller = new WordpressIntegrationController(UserProvider::initSettingsUser($model), $model);
            $response = $controller->getConsentFormCode($params);

            return new \WP_REST_Response($response, 200);
        } catch (UserAccessException $e) {
            return new \WP_REST_Response($e->getUserMessage(), 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

    public static function getModuleParentState($request)
    {
        try {
            $params = $request->get_body_params();
            $controller = new ModulesController();
            $response = $controller->getParentState($params['extension']);

            return new \WP_REST_Response($response, 200);
        } catch (UserAccessException $e) {
            return new \WP_REST_Response($e->getUserMessage(), 200);
        } catch (SalesManagoException $e) {
            return new \WP_REST_Response($e->getSalesManagoMessage(), 200);
        }
    }

}
