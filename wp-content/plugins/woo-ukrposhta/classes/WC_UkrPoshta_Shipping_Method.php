<?php

use plugins\UkrPoshta\classes\base\ArrayHelper;
use plugins\UkrPoshta\classes\base\Options;
use plugins\UkrPoshta\classes\Checkout;
use plugins\UkrPoshta\classes\Customer;

/**
 * Class WC_UkrPoshta_Shipping_Method
 */
class WC_UkrPoshta_Shipping_Method extends WC_Shipping_Method
{
    /**
     * Constructor for your shipping class
     *
     * @access public
     * @param int $instance_id
     */
    public function __construct($instance_id = 0)
    {
        parent::__construct($instance_id);
        $this->id = U_POSHTA_SHIPPING_METHOD;
        $this->method_title = __('Укрпошта', U_POSHTA_DOMAIN);
        $this->method_description = $this->getDescription();

        $this->init();

        // Get setting values
        $this->title = $this->settings['title'];
        $this->enabled = $this->settings['enabled'];
    }

    /**
     * Init your settings
     *
     * @access public
     * @return void
     */
    function init()
    {
        $this->init_form_fields();
        $this->init_settings();
        // Save settings in admin if you have any defined
        add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
    }

    public function test($packages)
    {

        return $packages;
    }

    /**
     * Initialise Gateway Settings Form Fields
     */
    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Активувати/Деактивувати', U_POSHTA_DOMAIN),
                'label' => __('Активувати метод доставки UkrPoshta', U_POSHTA_DOMAIN),
                'type' => 'checkbox',
                'description' => '',
                'default' => 'no'
            ),
            'title' => array(
                'title' => __('Укрпошта', U_POSHTA_DOMAIN),
                'type' => 'text',
                'description' => __('Властивість керує заголовком, який бачить користувач під час перевірки.', U_POSHTA_DOMAIN),
                'default' => __('Укрпошта', U_POSHTA_DOMAIN)
            ),
            Options::USE_FIXED_PRICE_ON_DELIVERY => [
                'title' => __('Встановіть фіксовану ціну за доставку.', U_POSHTA_DOMAIN),
                'label' => __('Якщо буде позначено цей пункт, додаткова фіксована ціна буде встановлена ​​для доставки.', U_POSHTA_DOMAIN),
                'type' => 'checkbox',
                'default' => 'no',
                'description' => '',
            ],
            Options::FIXED_PRICE => [
                'title' => __('Фіксована ціна', U_POSHTA_DOMAIN),
                'type' => 'text',
                'description' => __('Фіксована ціна доставки.', U_POSHTA_DOMAIN),
                'default' => 0.00
            ],
            'bb' => array(
                'title' => __(' ', U_POSHTA_DOMAIN),
                'type' => 'hidden',
                'description' => __('решта настройок доступні за <a  href="admin.php?page=morkvaup_plugin">посиланням</a>', U_POSHTA_DOMAIN),
                'default' => __('Укрпошта', U_POSHTA_DOMAIN)
            ),



        );
    }

    /**
     * calculate_shipping function.
     *
     * @access public
     *
     * @param array $package
     */
    public function calculate_shipping($package = array())
    {
        $rate = array(
            'id' => $this->id,
            'label' => $this->title,
            'cost' => 0,
            'calc_tax' => 'per_item'
        );




        if (UP()->options->useFixedPriceOnDelivery) {
            $rate['cost'] = UP()->options->fixedPrice;
        }

        //  elseif ($cityRecipient) {
        //     $citySender = UP()->options->senderCity;
        //     $serviceType = 'WarehouseWarehouse';
        //     /** @noinspection PhpUndefinedFieldInspection */
        //     $cartWeight = max(1, WC()->cart->cart_contents_weight);
        //     /** @noinspection PhpUndefinedFieldInspection */
        //     $cartTotal = max(1, WC()->cart->cart_contents_total);
        //     try {
        //         $result = UP()->api->getDocumentPrice($citySender, $cityRecipient, $serviceType, $cartWeight, $cartTotal);
        //         $cost = array_shift($result);
        //         $rate['cost'] = ArrayHelper::getValue($cost, 'Cost', 0);
        //     } catch (Exception $e) {
        //         UP()->log->error($e->getMessage());
        //     }
        // }
        // Register the rate
        $rate = apply_filters('woo_shipping_for_nova_poshta_before_add_rate', $rate, '');
        $this->add_rate($rate);
    }

    /**
     * Is this method available?
     * @param array $package
     * @return bool
     */
    public function is_available($package)
    {
        return $this->is_enabled();
    }

    /**
     * @return string
     */
    private function getDescription()
    {
        $href = "https://wordpress.org/plugins/woo-ukrposhta/";
        $link = '<a href="' . $href . '" target="_blank" class="np-rating-link">&#9733;&#9733;&#9733;&#9733;&#9733;</a>';

        $descriptions = array();
        $descriptions[] = '';
            $descriptions[] = sprintf(__("Якщо вам подобається наша робота, залиште оцінку на сайті %s !", U_POSHTA_DOMAIN), $link);

        return implode($descriptions, '<br>');
    }
}
