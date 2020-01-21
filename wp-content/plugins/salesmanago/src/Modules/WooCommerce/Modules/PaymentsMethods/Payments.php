<?php

namespace bhr\Modules\WooCommerce\Modules\PaymentsMethods;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use \bhr\Helper\SubModulesInterface;
use \SALESmanago\Controller\ConnectSalesManagoController as CSMC;
use \SALESmanago\Services\ConnectSalesManagoService as CSMS;

use \bhr\Modules\WooCommerce\Hooks as WcHooks;
use \bhr\Helper\Tagger as SmTagger;
use \bhr\Helper\HooksFiltersManager as HFM;

/**
 * This class is a Sub Module to WooCommerce and change send events data for $acceptedPayments;
 *
*/
class Payments implements SubModulesInterface
{
    const
        ORDER_T_PENDING    = 'pending',
        E_TYPE_TRANSACTION = 'TRANSACTION',
        P_METHOD           = 'payment_method';

    private static $WcHooks;
    private static $SmTagger;
    private static $PaymentMethod;
    private static $acceptedPayments = [
    	'paypal',
	    'wc_gateway_gestpay'
    ];
    private static $acceptedPaymentStatusForPurchase = [
        'Completed',
        'In-Progress',
        'Processed',
	    'processing',
	    'completed' /*for GestPay*/
    ];
    public static $recoveredCartUrl = '';

    public function __construct()
    {
      $this->initHooks();
    }

    public function initHooks()
    {
        add_action("activated_plugin", array($this, "loadThisAfterMaster"), 2);
        add_action("deactivated_plugin", array($this, "loadThisAfterMaster"), 2);

        add_action('sm_action_before_pre_purchase', array($this, 'setPaymentMethod'), 1, 1);

        add_action('woocommerce_payment_complete', array($this, 'wcPaymentComplete'), 1, 2);
        add_action('valid-paypal-standard-ipn-request', array($this, 'wcPaymentComplete'), 2, 2);
        add_action('gestpay_after_order_completed', array($this, 'wcPaymentCompleteGuestPay'), 1, 3);

        add_filter('sm_filter_event_type_before_send_pre_purchase', array($this, 'changeSmWcPrePurchaseEventType'), 1, 2);
        add_filter('sm_filter_event_id_before_send_pre_purchase', array($this, 'setCartIdToPrePurchase'), 1, 3);
        add_filter('sm_filter_event_id_before_send_purchase', array($this, 'resetPrePurchaseIdInPurchaseExtEv'), 1, 3);

        add_action('sm_action_contact_after_set_contact_tags_pre_purchase', array($this, 'setSmTagger'), 1, 4);
        add_filter('sm_filter_contact_before_send_pre_purchase_upsert', array($this, 'changeSmWcContactStatusBeforePrePurchaseUpsert'), 1, 5);

        add_action('sm_action_before_send_pre_purchase', array($this, 'getRecoveredCartUrl'), 1, 5);
        add_filter('sm_filter_products_before_send_pre_purchase', array($this, 'filterProductsInPrePurchase'), 1, 5);

        add_action('sm_wc_after_init_hooks', array($this, 'setWcHooksObject'), 1, 2);

        if (!isset(self::$WcHooks)) {
            HFM::doAction('sm_wc_child_action_register');
        }
    }

    public function loadThisAfterMaster()
    {
        $wpPathToThisFile = preg_replace('/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR . "/$2", __FILE__);
        $thisPlugin       = plugin_basename(trim($wpPathToThisFile));
        $activePlugins    = get_option('active_plugins');
        $thisPluginKey    = array_search($thisPlugin, $activePlugins);

        if (in_array($thisPlugin, $activePlugins) && end($activePlugins) != $thisPlugin) {
            array_splice($activePlugins, $thisPluginKey, 1);
            array_push($activePlugins, $thisPlugin);
            update_option('active_plugins', $activePlugins);
        }
    }

    public function setWcHooksObject(WcHooks $WcHooks)
    {
        self::$WcHooks = $WcHooks;
    }

    public function changeSmWcPrePurchaseEventType($params)
    {
        if (!self::checkPaymentMethod()) {
            return $params;
        }
        return CSMS::EVENT_TYPE_CART;
    }

    public function changeSmWcContactStatusBeforePrePurchaseUpsert($Contact)
    {
        if (!self::checkPaymentMethod()) {
            return $Contact;
        }

        if (isset(self::$SmTagger)) {
	        self::$SmTagger
                ->setContact($Contact)
                ->unsetTag(SmTagger::T_PURCHASE, $Contact);
        }

        return $Contact;
    }

    /**
     * WooCommerce PayPal payment complete.
     * Run when PayPal send callback to WooCommerce and payment complete
     *
     * Filter variables for WooCommerce Hooks->purchase() and send PURCHASE to SM
     *
     * @param array $param - payment details
     * @return boolean
     **/
    public function wcPaymentComplete($param)
    {
        $orders = wc_get_orders(
            array('billing_email' => "{$param['payer_email']}",
                'limit' => 1,
                'orderby' => 'date',
                'order' => 'DESC')
        );

        $lastContactOrder = $orders[0];

        if (!empty($lastContactOrder)) {
            self::setPaymentMethod($lastContactOrder->get_payment_method());

            if (!self::checkPaymentMethod()
                || !in_array($param['payment_status'], self::$acceptedPaymentStatusForPurchase)
                || !in_array($lastContactOrder->get_status(), self::$acceptedPaymentStatusForPurchase)
            ) {
                return true;
            }

            add_filter(
                'sm_filter_processed_order_statuses_before_send_purchase_statuses_check',
                function ($processingOrderWithStatuses) {
                    $processingOrderWithStatuses[] = self::ORDER_T_PENDING;
                    return $processingOrderWithStatuses;
                },
                1,
                1
            );

            add_filter('sm_filter_event_id_before_send_purchase', function () {
                return '';
            });
            self::$WcHooks->purchase($lastContactOrder->get_id());
        }
        return true;
    }

	/**
	 * WooCommerce GestPay payment complete.
	 * Run when PayPal send callback to WooCommerce and payment complete
	 *
	 * Filter variables for WooCommerce Hooks->purchase() and send PURCHASE to SM
	 *
	 * @param array $order - payment details
	 * @return boolean
	 **/
    public function wcPaymentCompleteGuestPay($order = null)
    {
        if (!empty($order)) {
            self::setPaymentMethod($order->get_payment_method());

            if (!self::checkPaymentMethod()
                || !in_array($order->get_status(), self::$acceptedPaymentStatusForPurchase)
            ) {
                return true;
            }

            add_filter(
                'sm_filter_processed_order_statuses_before_send_purchase_statuses_check',
                function ($processingOrderWithStatuses) {
                    $processingOrderWithStatuses[] = self::ORDER_T_PENDING;
                    return $processingOrderWithStatuses;
                },
                1,
                1
            );

            add_filter('sm_filter_event_id_before_send_purchase', function () {
                return '';
            });

            self::$WcHooks->purchase($order->get_id());
        }
        return true;
    }

    public function setSmTagger($TaggerContactArr)
    {
        self::$SmTagger = $TaggerContactArr[0];
    }

    public function setPaymentMethod($param)
    {
        if (isset($_POST[self::P_METHOD])) {
            self::$PaymentMethod = $_POST[self::P_METHOD];
        } elseif (isset($param) && !empty($param)) {
            self::$PaymentMethod = $param;
        }
    }

    public static function checkPaymentMethod($paymentMethod = null)
    {
        if (isset(self::$PaymentMethod)) {
            return in_array(self::$PaymentMethod, self::$acceptedPayments);
        }

        if ($paymentMethod != null) {
            return in_array(self::$PaymentMethod, self::$acceptedPayments);
        }

        return false;
    }

    public function setCartIdToPrePurchase($eventId)
    {
        if (!self::checkPaymentMethod()) {
            return $eventId;
        }

        if (isset($_COOKIE[CSMC::COOKIES_EXT_EVENT])
            && !empty($_COOKIE[CSMC::COOKIES_EXT_EVENT])
        ) {
            return $_COOKIE[CSMC::COOKIES_EXT_EVENT];
        }
    }

    public function resetPrePurchaseIdInPurchaseExtEv($prePurchaseId)
    {
        if (!self::checkPaymentMethod()) {
            return $prePurchaseId;
        }

        if (isset($_COOKIE[CSMC::COOKIES_EXT_EVENT])
            && !empty($_COOKIE[CSMC::COOKIES_EXT_EVENT])
        ) {
            return $_COOKIE[CSMC::COOKIES_EXT_EVENT];
        }
    }

    public function filterProductsInPrePurchase($params)
    {
        if (!self::checkPaymentMethod()) {
            return $params;
        }

        $params['detail2'] = self::$recoveredCartUrl;
        self::$recoveredCartUrl = '';
        return $params;
    }

    public function getRecoveredCartUrl($params)
    {
        if (!self::checkPaymentMethod()) {
            return false;
        }

        if (!isset($params['detail2'])) {
            return false;
        }

        if (empty($params['detail2'])) {
            return false;
        }

        self::$recoveredCartUrl = $params['detail2'];
        return true;
    }
}
