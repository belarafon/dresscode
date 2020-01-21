<?php

namespace bhr\Modules\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use SALESmanago\Exception\Exception;
use SALESmanago\Exception\SalesManagoException;
use SALESmanago\Provider\UserProvider;
use SALESmanago\Services\ConnectSalesManagoService;
use SALESmanago\Controller\ConnectSalesManagoController;

use bhr\Helper\Crypto;
use bhr\Helper\HooksFiltersManager as HFM;

use bhr\Modules\Newsletter\Context;
use bhr\Modules\Newsletter\Newsletter;

use bhr\Helper\Functions;

use bhr\Helper\Tagger;

class Hooks
{
    const SM_LAST_ORDER = 'smLastOrder';
    const SM_PRE_ORDER = 'smprepurchase';

    public $model;
    public $controller;
    public $ContactModel;
    public $product;
    public $PurchaseModel;
    private $NewsletterContext;
    private $Tagger;

    /**
     * available statuses: refunded, cancelled, completed, processing, pending, on-hold
     **/
    protected $processingOrders = array(
        'on-hold',
        'wc-on-hold',
        'processing',
        'pending'
    );

    /**
     * WooCommerceHooks constructor.
     * @throws SalesManagoException
     * @throws \SALESmanago\Exception\UserAccessException
     */
    public function __construct()
    {
        $this->model        = new HooksModel();
        $this->controller   = new ConnectSalesManagoController(UserProvider::initSettingsUser(new HooksModel()));
        $this->ContactModel = new ContactModel();
        $this->product      = new ProductsModel($this->model);
        $this->PurchaseModel = new PurchaseModel();
        $this->Tagger       = new Tagger($this->model);

	    $Context = new Context(new Newsletter());
	    $this->NewsletterContext = $Context->getContext();

        $this->initHooks();
    }

    public function initHooks()
    {
        $this->PurchaseModel->exec($this);

        /**
         * Prepurchase method is deprecated since it caused a lot of problems
         */
        /*add_action('woocommerce_before_checkout_process', array($this, 'prePurchase'), 1);*/

        /*add_action('woocommerce_order_status_changed', array($this, 'purchase'), 10, 4);*/

        add_action('woocommerce_order_status_cancelled', array($this, 'cancelledOrder'), 10, 1);
        add_action( 'woocommerce_order_status_refunded', array($this,'returnOrder'), 10, 1);

        add_action('woocommerce_checkout_update_user_meta', array($this, 'createUser'));
        add_action('woocommerce_customer_save_address', array($this, 'createUser'));
        add_action('profile_update', array($this, 'createUser'));

        add_action('user_register', array($this, 'registerUser'));
        add_action('wp_login', array($this, 'loginUser'));

        /*CART hooks*/
        add_action('woocommerce_add_to_cart', array($this, 'addToCart'));
        add_action('woocommerce_update_cart_action_cart_updated', array($this, 'addToCart'));
        add_action('woocommerce_remove_cart_item', array($this, 'addToCart'));

        add_action('sm_wc_child_action_register', function () {
            HFM::doAction('sm_wc_after_init_hooks', $this);
        });

        HFM::doAction('sm_wc_after_init_hooks', $this);
    }

    public function createUser($user_id)
    {
    	try {
		    $Contact = $this->ContactModel->get( $user_id );

		    if ( empty($Contact) || !$Contact ) {
			    $Contact = $this->ContactModel->getPurchaseNoAccountFromPost( $_POST );
		    }

		    $Contact = $this->NewsletterContext
			    ->setContact($Contact)
			    ->setContactOptStates();

		    $Contact = $this->Tagger
			    ->setContact($Contact)
			    ->setTags(Tagger::T_NEWSLETTER);

            if ($synchronizeRule = $this->model->getSynchronizeRule()) {
                $Contact->getOptions()->setCustomOptions($synchronizeRule);
            }

		    $contact = $Contact->getDataOptions();

		    $response = $this->controller->contactUpsert( $contact['data'], $contact['options'] );

		    if ( ! empty( $response['contactId'] ) ) {
			    $this->controller->createCookie(
				    ConnectSalesManagoController::COOKIES_CLIENT,
				    $response['contactId']
			    );
		    }
	    } catch (\Exception $e) {
		    error_log(print_r($e->getMessage(), true));
	    }
    }

    public function loginUser($user_login)
    {
    	try {
		    if ( ! $this->isCustomer( $user_login ) ) {
			    return true;
		    }

		    $Contact = $this->ContactModel->get( '', 'login', $user_login );

		    $Contact = $this->NewsletterContext
			    ->setContact($Contact)
			    ->setContactOptStates();

		    $Contact = $this->Tagger
			    ->setContact($Contact)
			    ->setTags(Tagger::T_LOGIN);

			$contact = $Contact->getDataOptions();

		    $response = $this->controller->contactUpsert(
		    	$contact['data'],
			    $contact['options']
		    );

		    if ( ! empty( $response['contactId'] ) ) {
			    $this->controller->createCookie(
				    ConnectSalesManagoController::COOKIES_CLIENT,
				    $response['contactId']
			    );
		    }
	    } catch (\Exception $e) {
		    error_log(print_r($e->getMessage(), true));
	    }
    }

	/**
	 * Parse woocommerce user to SM contact, send contact to SM
	 * Set smclient for contact
	 * @param $user_id - registered user id
	 */
    public function registerUser($user_id)
    {
    	try {
		    $Contact = $this->ContactModel->get( $user_id );

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

		    if ( ! empty( $response['contactId'] ) ) {
			    $this->controller->createCookie(
				    ConnectSalesManagoController::COOKIES_CLIENT,
				    $response['contactId']
			    );
		    }
	    } catch (\Exception $e) {
		    error_log(print_r($e->getMessage(), true));
	    }
    }

    /**
     * Parse & send order data as SM external event Purchase
     *
     * @param mixed $order  WC order or wc_order_id
     * @return bool         When no orders exist or get order with no processed statuses
     * @log Exception       log to wp logs an \Exception
    */
    public function purchase($order)
    {
    	try {
		    global $woocommerce;

		    HFM::doAction( 'sm_action_before_purchase', $order );

		    if ( empty( $order ) ) {
			    return true;
		    }

		    $processingOrderWithStatuses = HFM::applyFilters( 'sm_processing_purchase_order_statuses', $this->processingOrders);

		    $order = is_int( $order )
			    ? wc_get_order( $order )
			    : $order;

		    if (!in_array( $order->get_status(), $processingOrderWithStatuses)) {
			    return true;
		    }

		    $prePurchaseID = $this->prePurchaseGetID();

		    $Contact = $this->purchaseUpsert( $order );
		    $products = $this->product->getProductsFromOrder( $order, $woocommerce );

		    if ( ! $this->doublePurchaseCheck( $this->generateOrderId( $order->get_order_key() ) ) ) {
			    return false;
		    }

		    $processingOrderWithStatuses = HFM::applyFilters(
			    'sm_filter_processed_order_statuses_before_send_purchase_statuses_check',
			    $processingOrderWithStatuses
		    );

		    $products = HFM::applyFilters( 'sm_filter_products_before_send_purchase', $products );

		    if ( in_array( $order->get_status(), $processingOrderWithStatuses ) ) {
			    HFM::doAction( 'sm_action_before_send_purchase', [ $Contact, $products ] );
			    $prePurchaseID    = HFM::applyFilters( 'sm_filter_event_id_before_send_purchase', $prePurchaseID );

			    $contact = $Contact->getDataOptions();

			    $extEventResponse = $this->controller->contactExtEvent(
				    ConnectSalesManagoService::EVENT_TYPE_PURCHASE,
				    $products,
				    $contact['data'],
				    $prePurchaseID
			    );

			    if ( ! empty( $response['contactId'] ) ) {
				    $this->controller->createCookie(
					    ConnectSalesManagoController::COOKIES_CLIENT,
					    $response['contactId']
				    );
			    }
		    }

		    /*Don't remove cookie with pre-order id*/
		    $this->controller->deleteCookie( self::SM_PRE_ORDER );
		    $this->controller->deleteCookie( ConnectSalesManagoController::COOKIES_EXT_EVENT );

		    $this->controller->createCookie( self::SM_LAST_ORDER, $order->get_order_key() );

		    $param = isset( $extEventResponse ) ? $extEventResponse : null;
		    HFM::doAction( 'sm_action_after_purchase', $param );
	    } catch (\Exception $e) {
		    error_log(print_r($e->getMessage(), true));
	    }
    }

    /**
     * Parse WC cart to SM external event CART by smclient
     * Set smevent cookie
    */
    public function addToCart()
    {
    	try {
		    global $woocommerce;

		    $prePurchaseID = $this->prePurchaseGetID();
		    if ( ! empty( $prePurchaseID ) ) {
			    /*Remove cookie with pre-order id*/
			    $this->controller->deleteCookie( self::SM_PRE_ORDER );
		    }

		    $userData = isset( $_COOKIE['smclient'] )
			    ? array( 'contactId' => $_COOKIE['smclient'] )
			    : false;

		    if (!$userData && isset($_SESSION['smclient'])) {
			    $userData = array('contactId' => $_SESSION['smclient']);
		    }

			if (!$userData && $this->ContactModel->get(get_current_user_id())) {
				$Contact = $this->ContactModel->get(get_current_user_id());
				$userData = $Contact->getDataOptions();
				$userData = $userData['data'];
			}

		    if ( empty( $userData ) && ! isset( $userData['contactId'] ) ) {
			    return true;
		    }

		    //check smevent in session
		    $smevent = isset( $_COOKIE['smevent'] ) ? $_COOKIE['smevent'] : '';
		    if ( ! $smevent ) {
			    $smevent = isset( $_SESSION['smevent'] ) ? $_SESSION['smevent'] : '';
		    }

		    $products = $this->product->getProductFromCart( $woocommerce );

		    if ( $products ) {
			    $response = $this->controller->contactExtEvent(
				    ConnectSalesManagoService::EVENT_TYPE_CART,
				    $products,
				    $userData,
				    $smevent
			    );

			    if ( ! empty( $response['eventId'] ) ) {
				    $this->controller->createCookie(
					    ConnectSalesManagoController::COOKIES_EXT_EVENT,
					    $response['eventId']
				    );
				    $_SESSION['smevent'] = $response['eventId'];

				    return true;
			    }
		    }
	    } catch (\Exception $e) {
		    error_log(print_r($e->getMessage(), true));
	    } catch (SalesManagoException $e) {
            error_log(print_r($e->getMessage(), true));
        }
    }

    /**
     * @param int $orderId
     * @return boolean
     * @throws \Exception;
     * @throws \SalesManagoException;
    */
    public function cancelledOrder($orderId)
    {
        return $this->_eventFromOrder($orderId, ConnectSalesManagoService::EVENT_TYPE_CANCELLATION);
    }

    /**
     * @param int $orderId
     * @return boolean
     * @throws \Exception;
     * @throws \SalesManagoException;
     */
    public function returnOrder($orderId)
    {
        return $this->_eventFromOrder($orderId, ConnectSalesManagoService::EVENT_TYPE_RETURN);
    }

	/**
	 * This method is used in Admin/RestApi and recover cart from link
	 * @param $request - GET data
	 */
	public static function recoverCart($request)
	{
		try {
			$recoverCartData = Crypto::decrypt( $request->get_param( 'cart' ) );
			$model           = new HooksModel();
			$url             = $model->getWooCommerceCartUrl();

			/*rest is fire up with admin classes this is needed for set fronted WC ->*/
			wc()->frontend_includes();
			wc()->session = new \WC_Session_Handler();
			wc()->session->init();
            wc()->customer = new \WC_Customer( get_current_user_id(), true );
			wc()->cart = new \WC_Cart();/*<-*/

			if ( $recoverCartData ) {
				wc()->cart->empty_cart();

				foreach ( $recoverCartData as $product ) {
					$productId   = isset( $product['product_id'] ) ? (int) $product['product_id'] : 0;
					$quantity    = isset( $product['quantity'] ) ? (int) $product['quantity'] : 1;
					$variationId = isset( $product['variation_id'] ) ? (int) $product['variation_id'] : 0;
					$variation   = isset( $product['variation'] ) ? $product['variation'] : array();

					wc()->cart->add_to_cart( $productId, $quantity, $variationId, $variation, [ 'cart_recover' => true ] );
				}
				wc()->cart->calculate_totals();

				wp_redirect($url);
				exit();
			}
		} catch (\Exception $e) {
			error_log(print_r($e->getMessage(), true));
		}
	}

    protected function purchaseUpsert($order)
    {
    	try {
		    $Contact = $this->ContactModel->getPurchaseNoAccount( $order->get_id() );

			    $Contact = $this->NewsletterContext
				    ->setContact($Contact)
				    ->setContactOptStates();

			    $Contact = $this->Tagger
				    ->setContact($Contact)
				    ->setTags(Tagger::T_PURCHASE);

			    $contact = $Contact->getDataOptions();
			    $response = $this->controller->contactUpsert( $contact['data'], $contact['options'] );

			    if ( ! empty( $response['contactId'] ) ) {
				    $this->controller->createCookie(
					    ConnectSalesManagoController::COOKIES_CLIENT,
					    $response['contactId']
				    );
				    $Contact->setContactId($response['contactId']);
			    }
		    return $Contact;
	    } catch (\Exception $e) {
		    error_log(print_r($e->getMessage(), true));
	    }
    }

    protected function generateOrderId($products)
    {
    	try {
		    $id = null;
		    if ( isset( $products['products'] ) ) {
			    $id = str_replace( ',', '', $products['products'] );
			    $id .= str_replace( '/', '', $products['detail3'] );
		    } elseif ( ! empty( $products ) ) {
			    $wcOrderId = $products;
			    $id        = $wcOrderId;
		    }

		    return $id;
	    } catch (\Exception $e) {
		    error_log(print_r($e->getMessage(), true));
	    }
    }

    protected function doublePurchaseCheck($orderKey)
    {
    	try {
		    $pass = true;
		    if ( isset( $_COOKIE[ self::SM_LAST_ORDER ] )
		         && $_COOKIE[ self::SM_LAST_ORDER ] == $orderKey ) {
			    $pass = false;
		    }

		    if ( isset( $_SESSION[ self::SM_LAST_ORDER ] )
		         && $_SESSION[ self::SM_LAST_ORDER ] == $orderKey ) {
			    $pass = false;
		    }

		    return $pass;
	    } catch (\Exception $e) {
		    error_log(print_r($e->getMessage(), true));
	    }
    }

    protected function prePurchaseGetID()
    {
        $id = '';
        $id = isset($_COOKIE[self::SM_PRE_ORDER])
            ? $_COOKIE[self::SM_PRE_ORDER]
            : $id;
        $id = (empty($id) && isset($_SESSION[self::SM_PRE_ORDER]))
            ? $_SESSION[self::SM_PRE_ORDER]
            : $id;
        return $id;
    }

    protected function _eventFromOrder($orderId, $eventType)
    {
        try {
            $Contact = $this->ContactModel->getPurchaseNoAccount($orderId);
            $userData = $Contact->getDataOptions();
            $userData = $userData['data'];

            if ( empty( $userData ) && !isset( $userData['contactId'] ) ) {
                return true;
            }

            $order = wc_get_order($orderId);
            $products = $this->product->getProductsFromOrder($order);

            if ( $products ) {
                $this->controller->contactExtEvent(
                    $eventType,
                    $products,
                    $userData,
                    ''
                );
            }
        } catch (\Exception $e) {
            error_log(print_r($e->getMessage(), true));
        } catch (SalesManagoException $e) {
            error_log(print_r($e->getMessage(), true));
        }
    }

    /**
     * @param $userIdentify - string name \\ email \\ login
     * @return bool
     * */
    protected function isCustomer($userIdentify)
    {
        $user = (!get_user_by('email', $userIdentify))
            ? get_user_by('login', $userIdentify)
            : get_user_by('email', $userIdentify);

        if ($user) {
            $user = $user->get_role_caps();
            if (!isset($user['customer'])
                || $user['customer'] == null) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \WC_Order $order
     */
    public function saveOrderId($order)
    {
        $order = is_int( $order )
            ? wc_get_order( $order )
            : $order;

        $orderId = Crypto::encrypt($order->get_id());

        Functions::createCookie('smTempOrderId', $orderId);
    }

    public function recoverOrderFromId()
    {
        $encryptedOrderId = isset($_COOKIE['smTempOrderId']) ? $_COOKIE['smTempOrderId'] : null;
        if(empty($encryptedOrderId)){
            return false;
        }
        $decryptedOrderId = Crypto::decrypt($encryptedOrderId);

        $this->purchase($decryptedOrderId);

        Functions::deleteCookie('smTempOrderId');
    }


}
