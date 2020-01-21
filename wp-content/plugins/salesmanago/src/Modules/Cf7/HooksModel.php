<?php


namespace bhr\Modules\Cf7;

use bhr\Modules\Gf\ModuleConfigurationInterface;

class HooksModel extends Model implements ModuleConfigurationInterface
{

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

    public function getModuleConfiguration()
    {
        return $this->getConfig();
    }

    public function getSynchronizeRule()
    {
        return true;
    }

    /**
     * Find & return configuration of current contact form
     * @param string $formTitle;
     * @return mixed array if form config exist || otherwise - false;
    */
    public function getCurrentFormConfig($formTitle)
    {
        $cf7Config = $this->getConfig();

        foreach ($cf7Config['form'] as $formConfig) {
            if ($formConfig['name'] == $formTitle) {
                return $formConfig;
            }
        }

        return false;
    }
}
