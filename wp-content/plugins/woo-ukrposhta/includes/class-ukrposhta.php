<?php
/**
 * Main class for WC_Shipping_Ukrposhta.
 *
 * @category WordPress_Plugins
 * @package Ukrposhta
 * @author      MORKVA
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Wordpress_Ukrposhta Class.
 */
class WordpressWoocommerce_UkrposhtaDelivery {

    /**
     * Version
     *
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * Constructor
     */
    public function __construct() {
        $this->define_constants();

        /*add_action( 'init', [ $this, 'setup' ] );
        add_filter( 'woocommerce_get_sections_products', [ $this, 'wcslider_add_section' ] );
        add_filter( 'woocommerce_get_settings_products', [ $this, 'wcslider_all_settings' ], 10, 2 );
        add_filter( 'woocommerce_shipping_methods', [ $this, 'register_ukrposhta_method' ] );*/
    }

    /**
     * Define Plugin Constants
     */
    public function define_constants() {
        define( 'WPSK_BASENAME', plugin_basename( dirname( dirname( __FILE__ ) ) ) );
        define( 'WPSK_VERSION', $this->version );
    }

    /**
     * Setup
     */
    public function setup() {
        if ( get_option( WPSK_BASENAME . '-version' ) === WPSK_VERSION ) {
            return;
        }

        update_option( WPSK_BASENAME . '-version', WPSK_VERSION );
    }

    /**
    * Create the section beneath the products tab
    **/
    public function wcslider_add_section( $sections ) {
    
        $sections['wcslider'] = __( 'УкрПошта', 'text-domain' );
        return $sections;
    
    }

    /**
    * Add settings to the specific section we created before
    */
    public function wcslider_all_settings( $settings, $current_section ) {
        /**
        * Check the current section is what we want
        **/
        if ( $current_section == 'wcslider' ) {
            $settings_slider = array();
            // Add Title to the Settings
            $settings_slider[] = array(
                'name' => __( 'УкрПошта Налаштування', 'text-domain' ),
                'type' => 'title',
                'desc' => __( 'Тут ви можете налаштувати спосіб доставки через УкрПошту.', 'text-domain' ),
                'id' => 'wcslider'
            );

            // Add activate plugin checkbox
            $settings_slider[] = array(
                'name'      =>  __( 'Активувати плагін', 'text-domain' ),
                'desc_tip'  => __( 'Для того щоб працювати з плагіном, активуйте його.', 'text-domain' ),
                'id'        => 'ukrposhta_activate',
                'type'      => 'checkbox',
                'css'       => 'min-width: 300px;',
                'desc'      => __( 'Активувати', 'text-domain' ),
            );

            // Add second text field option
            $settings_slider[] = array(
                'name'     => __( 'УкрПошта', 'text-domain' ),
                'desc_tip' => __( 'Спосіб доставки через УкрПошту.', 'text-domain' ),
                'id'       => 'ukrposhta_title',
                'type'     => 'text',
                'desc'     => __( 'УкрПошта', 'text-domain' ),
            );

            // Add description field
            $settings_slider[] = array(
                'name'     => __( 'Опис', 'text-domain' ),
                'desc_tip' => __( 'Опис на сторінці оформлення замовлення.', 'text-domain' ),
                'id'       => 'ukrposhta_description',
                'type'     => 'textarea',
                'desc'     => __( '', 'text-domain' ),
            );
        
            $settings_slider[] = array( 'type' => 'sectionend', 'id' => 'wcslider' );
            return $settings_slider;
    
        /**
        * If not, return the standard settings
        **/
        } else {
            return $settings;
        }
    }

    /**
     * Registering shupping method of ukrposhta
     *
     * @since 0.1
     */
    public function register_ukrposhta_method( $methods )
    {
        $methods[ 'ukrposhta_method' ] = 'WC_Ukrposhta_Type';
        return $methods;
    }
}
