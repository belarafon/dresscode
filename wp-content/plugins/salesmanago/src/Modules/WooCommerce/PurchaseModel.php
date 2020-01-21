<?php


namespace bhr\Modules\WooCommerce;
use bhr\Modules\WooCommerce\Model;
use SALESmanago\Exception\Exception;
use SALESmanago\Exception\SalesManagoException;
use bhr\Helper\Functions;


class PurchaseModel extends Model
{
    public $config;
    public $type;

    const
        EXPORT_HOOKS = 'hooks',
        EXPORT_OTHER = 'other',
        EXPORT_CRON = 'cron',
        PURCHASE_CALLBACK = 'Purchase',
        RECOVER_ORDER_CALLBACK = 'recoverOrderFromId',
        SAVE_ORDER_ID_CALLBACK = 'saveOrderId';

    public function __construct()
    {
        parent::__construct();
        $this->getConfig();
    }

    public static $hookArr = [
        'woocommerce_order_status_changed' => [
            'hookName'      => 'woocommerce_order_status_changed',
            'hookType'      => 'action',
            'orderPlace'    => '4'
        ],
        'woocommerce_checkout_order_processed' => [
            'hookName'      => 'woocommerce_checkout_order_processed',
            'hookType'      => 'action',
            'orderPlace'    => '1'
        ],
        'woocommerce_checkout_update_order_meta' => [
            'hookName'      => 'woocommerce_checkout_update_order_meta',
            'hookType'      => 'action',
            'orderPlace'    => '1'
        ],
        'woocommerce_pre_payment_complete' => [
            'hookName'      => 'woocommerce_pre_payment_complete',
            'hookType'      => 'action',
            'orderPlace'    => '1'
        ],
        'woocommerce_payment_complete' => [
            'hookName'      => 'woocommerce_payment_complete',
            'hookType'      => 'action',
            'orderPlace'    => '1'
        ],
        'other' => [
            'saveIdHook'     => 'woocommerce_order_status_changed',
            'hookName'       => 'woocommerce_thankyou',
            'orderPlaceHook' => '1',
            'orderPlaceSave' => '4'
        ]
    ];

    public function getConfig(){
        $userData = $this->getUserData();
        if(isset($userData['extensions']['wc']['event_config'])){
            $exportType = $userData['extensions']['wc']['event_config']['type'];
            $hookDetail = $userData['extensions']['wc']['event_config']['hookConfig'];
            $cronDetail = isset($userData['extensions']['wc']['event_config']['cronConfig']) ? $cronDetail = $userData['extensions']['wc']['event_config']['cronConfig'] :  '';

            switch ($exportType){
                case self::EXPORT_HOOKS:
                    $this->type = self::EXPORT_HOOKS;
                    $this->config = self::$hookArr[$hookDetail]['hookName'];
                    return $this->config;
                    break;
                case self::EXPORT_OTHER:
                    $this->type = self::EXPORT_OTHER;
                    $this->config = $exportType = $userData['extensions']['wc']['event_config']['type'];
                    return $this->config;
                    break;
                case self::EXPORT_CRON:
                    $this->type = self::EXPORT_CRON;
                    $this->config = $cronDetail;
                    return $this->config;
                    break;
            }
            return false;
        } else {
            return true;
        }
    }

    public function exec(Hooks $object)
    {
        try{
            if($this->type == self::EXPORT_CRON){
                return true;
            }

            if($this->type == self::EXPORT_HOOKS){
                $hookType = self::$hookArr[$this->config]['hookType'];
                $hookName = self::$hookArr[$this->config]['hookName'];
                $callback = self::PURCHASE_CALLBACK;
                $orderPlace = self::$hookArr[$this->config]['orderPlace'];

                if($hookType = 'action'){
                    add_action($hookName, array($object, $callback), 10, $orderPlace);
                } else {
                    add_filter($hookName, array($object, $callback), 10, $orderPlace);
                }
            } elseif ($this->type == self::EXPORT_OTHER){
                $saveIdHook = self::$hookArr[$this->config]['saveIdHook'];
                $hookName = self::$hookArr[$this->config]['hookName'];

                $purchaseCallback = self::RECOVER_ORDER_CALLBACK;
                $saveIdCallback = self::SAVE_ORDER_ID_CALLBACK;

                $orderPlaceHook = self::$hookArr[$this->config]['orderPlaceHook'];
                $orderPlaceSave = self::$hookArr[$this->config]['orderPlaceSave'];

                add_action($hookName, array($object, $purchaseCallback), 10, $orderPlaceHook);
                add_action($saveIdHook, array($object, $saveIdCallback), 10, $orderPlaceSave);
            }

            return true;
        } catch (\Exception $e){
            error_log(print_r($e->getMessage(), true));
        }
    }

}