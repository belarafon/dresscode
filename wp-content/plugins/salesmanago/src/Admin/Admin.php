<?php

namespace bhr\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Admin
{
    public $plugin_dir;
    public $plugin_url;

    public function __construct($plugin_dir, $plugin_url)
    {
        $this->plugin_dir = $plugin_dir;
        $this->plugin_url = $plugin_url;

        new PluginHooks();
        new RestApi();

        $this->initHooks();
    }

    public function initHooks()
    {
        add_action('admin_menu', array($this, 'registerAdminDashboardPage'));
        add_action('admin_footer', array($this, 'registerConfig'));
        add_action('admin_enqueue_scripts', array($this, 'registerAssets'));
        add_action('admin_bar_menu', array($this, 'toolbarLink'), 999);
    }

    public function toolbarLink($wp_admin_bar)
    {
        $args = array(
            'id'    => 'salesmanago',
            'title' => 'SALESmanago',
            'href'  => "admin.php?page=sm",
            'meta'  => array(
                'class' => 'salesmanago-toolbar-link',
                'title' => 'SALESmanago Marketing Automation'
            )
        );
        $wp_admin_bar->add_node($args);
    }

    public function registerAdminDashboardPage()
    {
        add_menu_page(
            'SALESmanago',
            'SALESmanago',
            'manage_options',
            'sm',
            array($this, 'dashboardHomePage'),
            $this->plugin_url . '../assets/img/icon.png',
            10
        );

        do_action('sm-register-admin-menu-after', $this);
    }

    public function dashboardHomePage()
    {
        echo '<div id="bhr-app"></div>';
    }

    public function currentLang()
    {
        $lang       = strtoupper(substr(get_locale(), 0, 2));
        $accessLang = array('EN', 'PL');

        if (in_array($lang, $accessLang)) {
            return $lang;
        }

        return $accessLang[0];
    }

    public function ssoConfig()
    {
    	$lang = $this->currentLang();
		$lang = ($lang != 'EN' && $lang != 'PL') ? 'EN' : $lang;

		$basicAuth = isset($_COOKIE['smviewaut']) ? $_COOKIE['smviewaut'] : '';

        $bhrSSO = array(
            "baseURL"    => get_rest_url(null, '/', 'json') . 'salesmanago/v1/',
            "platform"   => "Wordpress",
            "clientID"   => '',
            "shopOrigin" => get_home_url(),
            "lang"       => $lang,
	        "version"    => '2.6.0',
	        "auth"       => $basicAuth,
            "enable"     => array(
                "synchronizeRule" => false,
                "callback"        => false,
	            "apiDoubleOptIn"  => true,
                "eventConfig"     => true
            ),
	        "params" => array(
		        "sm_request" => true
	        )
        );

        if(!empty($_COOKIE[LOGGED_IN_COOKIE])) {
            $bhrSSO['params'] = array(
                "auth_token" => sha1($_COOKIE[LOGGED_IN_COOKIE]),
	            "sm_request" => true
            );
        }

        return json_encode($bhrSSO);
    }

    public function registerConfig()
    {
        echo "<script type=text/javascript>sessionStorage.setItem('bhrSSO', JSON.stringify({$this->ssoConfig()}));</script>";
    }

    public function registerAssets($hook)
    {
        wp_register_style(
            'sm_sso_style',
            $this->plugin_url . '../assets/css/sm_sso_wp.css',
            false
        );

        wp_enqueue_media();
        wp_enqueue_style('sm_sso_style');

        if ($hook != 'toplevel_page_sm') {
            return;
        }

        wp_register_script(
            'sm_sso_vendor',
            $this->plugin_url . '../assets/js/chunk-vendors.3610e7cd.js',
            array(),
            rand(0, 100000000),
            true
        );

        wp_register_script(
            'sm_sso',
            $this->plugin_url . '../assets/js/wordpress.3776c734.js',
            array('sm_sso_vendor'),
            rand(0, 100000000),
            true
        );

        wp_enqueue_script('sm_sso_vendor');
        wp_enqueue_script('sm_sso');

	    wp_localize_script( 'sm_sso', 'wpApiSettings', array(
		    'root' => esc_url_raw( rest_url() ),
		    'nonce' => wp_create_nonce( 'wp_rest' )
	    ) );
    }
}
