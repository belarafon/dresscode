<?php

use plugins\UkrPoshta\classes\base\Base;
use plugins\UkrPoshta\classes\base\Options;

define('U_POSHTA_SHIPPING_PLUGIN_DIR', trailingslashit(dirname(__FILE__)));
define('U_POSHTA_SHIPPING_PLUGIN_URL', trailingslashit(plugin_dir_url(__FILE__)));
define('U_POSHTA_SHIPPING_TEMPLATES_DIR', trailingslashit(U_POSHTA_SHIPPING_PLUGIN_DIR . 'templates'));
define('U_POSHTA_SHIPPING_CLASSES_DIR', trailingslashit(U_POSHTA_SHIPPING_PLUGIN_DIR . 'classes'));
define('U_POSHTA_DOMAIN', untrailingslashit(basename(dirname(__FILE__))));
define('U_POSHTA_SHIPPING_METHOD', 'u_poshta_shipping_method');


spl_autoload_register('registerUkrPoshtaAutoload');

function registerUkrPoshtaAutoload($class)
{
    // project-specific namespace prefix
    $prefix = 'plugins\\UkrPoshta\\';
    // base directory for the namespace prefix
    $base_dir = U_POSHTA_SHIPPING_PLUGIN_DIR;
    // does the class use the namespace prefix?
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);
    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    // if the file exists, require it
    if (file_exists($file)) {
        /** @noinspection PhpIncludeInspection */
        require_once $file;
    }
}


/**
 * Class UkrPoshta
 *
 * @property wpdb db
 * @property UkrPoshtaApi api
 * @property Options options
 * @property Log log
 * @property string pluginVersion
 */
class UkrPoshta extends Base
{
    const LOCALE_RU = 'ru_RU';

    /**
     * Register main plugin hooks
     */
    public function init()
    {
        register_activation_hook(__FILE__, array($this, 'activatePlugin'));
        register_deactivation_hook(__FILE__, array($this, 'deactivatePlugin'));

        if ($this->isWoocommerce()) {
            //general plugin actions
            add_action('plugins_loaded', array($this, 'checkDatabaseVersion'));
            add_action('plugins_loaded', array($this, 'loadPluginDomain'));
            add_action('wp_enqueue_scripts', array($this, 'scripts'));
            add_action('wp_enqueue_scripts', array($this, 'styles'));
            add_action('admin_enqueue_scripts', array($this, 'adminScripts'));
            add_action('admin_enqueue_scripts', array($this, 'adminStyles'));

            //register new shipping method
            add_action('woocommerce_shipping_init', array($this, 'initUkrPoshtaShippingMethod'));
            add_filter('woocommerce_shipping_methods', array($this, 'addUkrPoshtaShippingMethod'));

            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'pluginActionLinks'));


        }
    }

    /**
     * @return bool
     */
    public function isWoocommerce()
    {
        return in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
    }

    /**
     * @return bool
     */
    public function isCheckout()
    {
        return Checkout::instance()->isCheckout;
    }

    /**
     * This method can be used safely only after woocommerce_after_calculate_totals hook
     * when $_SERVER['REQUEST_METHOD'] == 'GET'
     *
     * @return bool
     */
    public function isUP()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $sessionMethods = WC()->session->chosen_shipping_methods;

        $chosenMethods = array();
        if ($this->isPost() && ($postMethods = (array)ArrayHelper::getValue($_POST, 'shipping_method', array()))) {
            $chosenMethods = $postMethods;
        } elseif (isset($sessionMethods) && count($sessionMethods) > 0) {
            $chosenMethods = $sessionMethods;
        }
        return in_array(U_POSHTA_SHIPPING_METHOD, $chosenMethods);
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * @return bool
     */
    public function isGet()
    {
        return !$this->isPost();
    }

    /**
     * Enqueue all required scripts
     */
    public function scripts()
    {

    }

    /**
     * Enqueue all required styles
     */
    public function styles()
    {

    }

    /**
     * Enqueue all required styles for admin panel
     */
    public function adminStyles()
    {

    }

    /**
     * Enqueue all required scripts for admin panel
     */
    public function adminScripts()
    {

    }

    /**
     * @param string $handle
     */
    public function localizeHelper($handle)
    {
        wp_localize_script($handle, 'UkrPoshtaHelper', [
            'ajaxUrl' => admin_url('admin-ajax.php', 'relative'),
            'chooseAnOptionText' => __('Choose an option', U_POSHTA_DOMAIN),
        ]);
    }

    /**
     * @param string $template
     * @param string $templateName
     * @param string $templatePath
     * @return string
     */
    public function locateTemplate($template, $templateName, $templatePath)
    {
        global $woocommerce;
        $_template = $template;
        if (!$templatePath)
            $templatePath = $woocommerce->template_url;

        $pluginPath = U_POSHTA_SHIPPING_TEMPLATES_DIR . 'woocommerce/';

        // Look within passed path within the theme - this is priority
        $template = locate_template(array(
            $templatePath . $templateName,
            $templateName
        ));

        if (!$template && file_exists($pluginPath . $templateName)) {
            $template = $pluginPath . $templateName;
        }

        return $template ?: $_template;
    }

    /**
     * @param array $methods
     * @return array
     */
    public function addUkrPoshtaShippingMethod($methods)
    {
        $methods[] = 'WC_UkrPoshta_Shipping_Method';
        return $methods;
    }

    /**
     * Init UkrPoshta shipping method class
     */
    public function initUkrPoshtaShippingMethod()
    {
        if (!class_exists('WC_UkrPoshta_Shipping_Method')) {
            /** @noinspection PhpIncludeInspection */
            require_once U_POSHTA_SHIPPING_PLUGIN_DIR . 'classes/WC_UkrPoshta_Shipping_Method.php';
        }
    }

    /**
     * Activation hook handler
     */
    public function activatePlugin()
    {
        Database::instance()->upgrade();
        DatabaseSync::instance()->synchroniseLocations();
    }

    /**
     * Deactivation hook handler
     */
    public function deactivatePlugin()
    {
        Database::instance()->downgrade();
        Options::instance()->clearOptions();
    }

    public function checkDatabaseVersion()
    {
        if (version_compare($this->pluginVersion, get_site_option('nova_poshta_db_version'), '>')) {
            Database::instance()->upgrade();
            DatabaseSync::instance()->synchroniseLocations();
            update_site_option('nova_poshta_db_version', $this->pluginVersion);
        }
    }

    /**
     * Register translations directory
     * Register text domain
     */
    public function loadPluginDomain()
    {
        $path = sprintf('./%s/i18n', U_POSHTA_DOMAIN);
        load_plugin_textdomain(U_POSHTA_DOMAIN, false, $path);
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->options->isDebug();
    }

    /**
     * @param array $links
     * @return array
     */
    public function pluginActionLinks($links)
    {
        $href = admin_url('admin.php?page=wc-settings&tab=shipping&section=' . U_POSHTA_SHIPPING_METHOD);
        $settingsLink = sprintf('<a href="' . $href . '" title="%s">%s</a>', esc_attr(__('View Plugin Settings', U_POSHTA_DOMAIN)), __('Settings', U_POSHTA_DOMAIN));
        array_unshift($links, $settingsLink);
        return $links;
    }

    /**
     * @return Options
     */
    protected function getOptions()
    {
        return Options::instance();
    }

    /**
     * @return Log
     */
    protected function getLog()
    {
        return null;
    }

    /**
     * @return wpdb
     */
    protected function getDb()
    {
        global $wpdb;
        return $wpdb;
    }

    /**
     * @return UkrPoshtaApi
     */
    protected function getApi()
    {
        return UkrPoshtaApi::instance();
    }

    /**
     * @return string
     */
    protected function getPluginVersion()
    {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        $pluginData = get_plugin_data(__FILE__);
        return $pluginData['Version'];
    }

    /**
     * @var UkrPoshta
     */
    private static $_instance;

    /**
     * @return UkrPoshta
     */
    public static function instance()
    {
        if (static::$_instance == null) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

    /**
     * UkrPoshta constructor.
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

UkrPoshta::instance()->init();


/**
 * @return UkrPoshta
 */
function UP()
{
    return UkrPoshta::instance();
}
