<?php

namespace bhr\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bhr\Modules\Wordpress\Model as WpModel;
use bhr\Modules\WooCommerce\Model as WcModel;
use bhr\Modules\Newsletter\Model as NewsModel;
use bhr\Modules\Cf7\Model as Cf7Model;
use bhr\Modules\Gf\Model as GfModel;

use bhr\Controller\ExportControllerInterface;

use SALESmanago\Exception\SalesManagoException;

class ModulesConfigManager
{
	const
		SM_WP_NAME_SP = 'Wordpress',
		SM_WC_NAME_SP = 'WooCommerce',
		SM_NEWS_NAME_SP = 'Newsletter',
		SM_CF7_NAME_SP  = 'Cf7',
		SM_GF_NAME_SP = 'Gf';

    public static $instance;

    /**
     * Here add NameSpaces as keys
     * You can use key 'deactivate'
     * second ('leadOf') get value of this key as module name
     * and deactivate that module in case of both modules are active;
     */
    private $availableModules = [
        self::SM_WP_NAME_SP => [
            'state' => 1,
            'shortName' => WpModel::EXT_NAME
        ],
        self::SM_WC_NAME_SP => [
            'state'      => 1,
            'shortName'  => WcModel::EXT_NAME,
            'leadOf'     => [self::SM_WP_NAME_SP],
	        'subModules' => [
	        	'\bhr\Modules\WooCommerce\Modules\PaymentsMethods\Payments' => 1
	        ]
        ],
        self::SM_NEWS_NAME_SP => [
            'state' => 1,
            'shortName' => NewsModel::EXT_NAME
        ],
        self::SM_CF7_NAME_SP => [
            'state' => 1,
            'shortName' => Cf7Model::EXT_NAME
        ],
        self::SM_GF_NAME_SP => [
            'state' => 1,
            'shortName' => GfModel::EXT_NAME
        ]
    ];

    private $enabledModules = [];

    private $defaultConfig = [];
    private $defaultActive = [];
    private $activePluginsHooksClasses = [];
    protected static $HooksModel;

    /**
     * @param array $arrayOfModulesActiveStates
     * @throws SalesManagoException
     * @return self
    */
    public static function getInstance($arrayOfModulesActiveStates = null)
    {
    	try {
	        if (self::$instance == null) {
	            self::$instance = new ModulesConfigManager();
	        }

	        self::$instance->setEnabledModules($arrayOfModulesActiveStates);

	        return self::$instance;
	    } catch (\Exception $e) {
    		throw new SalesManagoException( $e->getMessage() );
	    }
    }

    public function getDefaultConfig()
    {
        return $this->defaultConfig;
    }

    public function getDefaultActive()
    {
        return $this->defaultActive;
    }

    public function getHooks()
    {
    	/*
    	if (is_admin()) {
    		return true;
	    }
    	*/

    	if (isset($_REQUEST['sm_request'])
            && $_REQUEST['sm_request']
        ) {
            return true;
        }

        $enabledHooks = [];

        $this->activePluginsHooksClasses = $this->getEnabledModulesHooksClasses();

        if (/*!is_admin() && */$this->activePluginsHooksClasses) {
            foreach ($this->activePluginsHooksClasses as $hooksClass) {
                $enabledHooks[] = new $hooksClass();
            }
            return $enabledHooks;
        } else {
            return null;
        }
    }

    public function getActivesByParents()
    {
        $config = [];
        foreach ($this->availableModules as $moduleName => $state) {
            $modelName = "bhr\\Modules\\{$moduleName}\\Model";

            if ($state['state']
                && method_exists($modelName, 'isFeederActive')
                && $modelName::isFeederActive()
            ) {
                $config[$modelName::EXT_NAME] = filter_var($modelName::isFeederActive(), FILTER_VALIDATE_BOOLEAN);
            } elseif ($state['state'] && !method_exists($modelName, 'isFeederActive')) {
                $config[$modelName::EXT_NAME] = filter_var($state['state'], FILTER_VALIDATE_BOOLEAN);
            }
        }

        return $config;
    }

    public function getEnabledModules()
    {
    	if (!isset($this->enabledModules)
	        || empty($this->enabledModules)
	    ) {
		    return null;
	    }

    	return $this->enabledModules;
    }

    public function isEnabled($const)
    {
	    $enabledModules = $this->getEnabledModules();

		if ($enabledModules == null) {
			return false;
		}

		if (array_key_exists($const, $enabledModules)) {
			return true;
		}

		return false;
    }

    /**
     * @param array $params - request parameters
     * @throws SalesManagoException;
     * @return array $exportControllers - array of active export controllers objects
    */
    public function getExportControllers($params = null)
    {
    	try {
		    $exportControllers = [];
	        $controllers = $this->getEnabledModulesExportControllers();

	        if (empty($controllers)) {
			    return $exportControllers;
		    }

			foreach ($controllers as $controller) {

					$module = $controller['module'];
					$Controller = new $controller['controller']($params);

					if ($Controller instanceof ExportControllerInterface
						&& array_key_exists($module, $this->enabledModules)
					) {
						$exportControllers[] = $Controller;
					}
			}
	        return $exportControllers;
	    } catch (\Exception $e) {
			throw new SalesManagoException($e->getMessage());
	    }
    }

	private function __construct()
	{
		$this->defaultActive = [
			'active' => [
				/*NewsModel::EXT_NAME - must be always on, for WooCommerce & WordPress modules
				 in case of prepare opt states*/
				NewsModel::EXT_NAME => true,
				Cf7Model::EXT_NAME  => false,
				GfModel::EXT_NAME   => false,
				WcModel::EXT_NAME   => false,
				WpModel::EXT_NAME   => false
			]
		];

		$this->defaultConfig = $this->setModulesDefaultConfig();
	}

    private function setModulesDefaultConfig()
    {
        $config = [];
        foreach ($this->availableModules as $moduleName => $state) {
            if ($state) {
                $modelName = "bhr\\Modules\\{$moduleName}\\Model";
                $config = array_merge($config, $modelName::getDefaultConfig());
            }
        }

        return $config;
    }

    private function getEnabledModulesHooksClasses()
    {
        $modulesHooksClasses = [];

        if (empty($this->enabledModules)) {
            return false;
        }

        foreach ($this->enabledModules as $moduleName => $moduleConf) {
            $moduleHooksClass = "bhr\\Modules\\{$moduleName}\\Hooks";
            $moduleModelClass = "bhr\\Modules\\{$moduleName}\\Model";

            if (method_exists($moduleModelClass, 'isFeederActive')
                && filter_var($moduleModelClass::isFeederActive(), FILTER_VALIDATE_BOOLEAN)) {
                $modulesHooksClasses[] = $moduleHooksClass;
	            $modulesHooksClasses = array_merge($modulesHooksClasses, $this->getEnabledSubModulesClasses($moduleConf));
            }
        }

        return $modulesHooksClasses;
    }

    /**
     * @param array $moduleConf - module configuration array;
     * @return array - classes of sub modules;
    */
    private function getEnabledSubModulesClasses($moduleConf)
    {
	    $subModulesHooksClasses = [];

    	if (!isset($moduleConf['subModules'])) {
			return $subModulesHooksClasses;
	    }

	    foreach ($moduleConf['subModules'] as $subModuleName => $enabled) {
		    if ($enabled
		        && array_key_exists( 'bhr\Helper\SubModulesInterface', class_implements($subModuleName))) {
			    $subModulesHooksClasses[] = $subModuleName;
		    }
	    }

		return $subModulesHooksClasses;
    }

    /**
     * @throws SalesManagoException
    **/
	private function getEnabledModulesExportControllers()
	{
		try {
			$modulesExportControllers = [];

			if ( empty( $this->enabledModules ) ) {
				return false;
			}

			foreach ( $this->enabledModules as $moduleName => $moduleConf ) {
				$moduleExportController = "\\bhr\\Modules\\{$moduleName}\\ExportController";
				$moduleModelClass       = "\\bhr\\Modules\\{$moduleName}\\Model";

				if ( class_exists( $moduleExportController )
				     && method_exists( $moduleModelClass, 'isFeederActive' )
				     && filter_var( $moduleModelClass::isFeederActive(), FILTER_VALIDATE_BOOLEAN )
				) {
					$modulesExportControllers[] = [
						'controller' => $moduleExportController,
						'module' => $moduleName
					];
				}
			}

			return $modulesExportControllers;
		} catch (\Exception $e) {
			throw new SalesManagoException($e->getMessage());
		}
	}

    private function setEnabledModules($arrayOfModulesActiveStates)
    {
        $modulesToUnset = [];

        if ($arrayOfModulesActiveStates == null) {
            $arrayOfModulesActiveStates = $this->defaultActive['active'];
        }

        foreach ($this->availableModules as $moduleName => $moduleConf) {
            if (!$moduleConf['state']) {
                continue;
            }
            if (!array_key_exists($moduleConf['shortName'], $arrayOfModulesActiveStates)) {
                continue;
            }
            if (!$arrayOfModulesActiveStates[$moduleConf['shortName']]) {
                continue;
            }

            if (isset($moduleConf['leadOf'])) {
                $modulesToUnset = array_merge($modulesToUnset, $moduleConf['leadOf']);
            }

            $this->enabledModules[$moduleName] = $moduleConf;
        }

        if (!empty($modulesToUnset)) {
            foreach ($modulesToUnset as $module) {
                if (array_key_exists($module, $this->enabledModules)) {
                    unset($this->enabledModules[$module]);
                }
            }
        }
    }
}
