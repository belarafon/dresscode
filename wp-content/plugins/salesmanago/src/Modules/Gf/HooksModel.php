<?php


namespace bhr\Modules\Gf;

use bhr\Model\HooksModel as GlobalHooksModel;
use \RGFormsModel;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class HooksModel extends GlobalHooksModel implements ModuleConfigurationInterface
{
    const
        EXT_NAME       = 'gf',
        CONTACT_PREFIX = 'sm-',
        FORCE_OPTIN    = 'forceOptIn',
        CONF           = 'confirmation',
        CONF_DOUBLE    = 'double',
        OPTIN_FIELD    = 'optin',
        PROPERTIES_ARR = 'properties',
        CONTACT_ARR    = 'contact',
        OPTIONS_ARR    = 'options',
        LASTNAME_FIELD = 'lastname',
        ADDRESS2_FIELD = 'address2',
        WP_PLUGIN      = 'gravityforms/gravityforms.php';

    public function getConfig()
    {
        $userData = $this->getUserData();
        $userData['extensions'][self::EXT_NAME]['forms'] = $this->getGfFormsData();
        $config = $userData['extensions'][self::EXT_NAME];

        ($config['forceOptIn'] == "false" || empty($config['forceOptIn']))
            ? $config['forceOptIn'] = false
            : $config['forceOptIn'] = true;
        ($config['confirmation']['double'] == "false" || empty($config['confirmation']['double']))
            ? $config['confirmation']['double'] = false
            : $config['confirmation']['double'] = true;

        return $config;
    }

    public function getGfFormsData()
    {
        $formsData = array();
        if ($formsList = RGFormsModel::get_forms()) {
            foreach ($formsList as $form) {
                $formsData[] = ['id' => $form->id, 'title' => $form->title];
            }
        }

        return $formsData;
    }

    public function getDoubleOptInConf()
    {
        $config = $this->getModuleConfiguration();

        return (
            isset($config['confirmation'])
            && isset($config['confirmation']['double'])
            && $config['confirmation']['double']
        )
            ? $config['confirmation']
            : false ;
    }

    public function getSynchronizeRule()
    {
        return true;
    }

    public function getModuleConfiguration()
    {
        return $this->getConfig();
    }
}
