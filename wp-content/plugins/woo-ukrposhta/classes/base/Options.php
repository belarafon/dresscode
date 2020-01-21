<?php

namespace plugins\UkrPoshta\classes\base;

/**
 * Class Options
 * @package plugins\UkrPoshta\classes\base
 *
 * @property int locationsLastUpdateDate
 * @property array shippingMethodSettings
 * @property string senderArea
 * @property string senderCity
 * @property string senderWarehouse
 * @property string apiKey
 * @property bool useFixedPriceOnDelivery
 * @property float fixedPrice
 * @property bool pluginRated
 *
 */
class Options extends Base
{
    const DEBUG = 'debug';
    const USE_FIXED_PRICE_ON_DELIVERY = 'use_fixed_price_on_delivery';
    const FIXED_PRICE = 'fixed_price';
    const OPTION_CASH_ON_DELIVERY = 'on_delivery';
    const OPTION_FIXED_PRICE = 'fixed_price'; 

    /**
     * @return void
     */
    public function ajaxPluginRate()
    {
        UP()->log->info('Plugin marked as rated');
        $this->setOption(self::OPTION_PLUGIN_RATED, 1);
        $result = array(
            'result' => true,
            'message' => __('Thank you :)', U_POSHTA_DOMAIN)
        );
        echo json_encode($result);
        exit;
    }

    /**
     * @return bool
     */
    protected function getUseFixedPriceOnDelivery()
    {
        return filter_var($this->shippingMethodSettings[self::USE_FIXED_PRICE_ON_DELIVERY], FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return float
     */
    protected function getFixedPrice()
    {
        return $this->useFixedPriceOnDelivery ? (float)$this->shippingMethodSettings[self::FIXED_PRICE] : null;
    }

    /**
     * @return int
     */
    public function getLocationsLastUpdateDate()
    {
        return $this->getOption('locations_last_update_date') ?: 0;
    }

    /**
     * @param int $value
     */
    public function setLocationsLastUpdateDate($value)
    {
        $this->setOption('locations_last_update_date', $value);
        $this->locationsLastUpdateDate = $value;
    }

    /**
     * @return array
     */
    protected function getShippingMethodSettings()
    {
        return get_site_option('woocommerce_U_POSHTA_SHIPPING_METHOD_settings');
    }



    /**
     * @return bool
     */
    public function isDebug()
    {
        return filter_var(ArrayHelper::getValue($this->shippingMethodSettings, self::DEBUG), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Delete all plugin specific options from options table
     * @return void
     */
    public function clearOptions()
    {
        $table = UP()->db->options;
        $query = "DELETE FROM `$table` WHERE option_name LIKE CONCAT ('_nova_poshta_', '%')";
        UP()->db->query($query);
    }

    /**
     * @param $optionName
     * @return mixed
     */
    private function getOption($optionName)
    {
        $key = "_nova_poshta_" . $optionName;
        return get_option($key);
    }

    /**
     * @param string $optionName
     * @param mixed $optionValue
     */
    private function setOption($optionName, $optionValue)
    {
        $key = "_nova_poshta_" . $optionName;
        update_option($key, $optionValue);
    }

    /**
     * @var Options
     */
    private static $_instance;

    /**
     * @return Options
     */
    public static function instance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Options constructor.
     *
     * @access private
     */
    private function __construct()
    {
    }

    /**
     * @access private
     */
    private function __clone()
    {
    }

}
