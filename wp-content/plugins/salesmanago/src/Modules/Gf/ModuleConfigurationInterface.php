<?php


namespace bhr\Modules\Gf;


interface ModuleConfigurationInterface
{
    public function getDoubleOptInConf();
    public function getSynchronizeRule();
    public function getModuleConfiguration();
}
