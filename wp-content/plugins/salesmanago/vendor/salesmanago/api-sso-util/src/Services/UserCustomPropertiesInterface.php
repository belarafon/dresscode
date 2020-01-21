<?php

namespace SALESmanago\Services;

use SALESmanago\Entity\Settings;


interface UserCustomPropertiesInterface
{
    const METHOD_GET_INTEGRATION_PROPERTIES = '/api/account/integration/properties',
          METHOD_SET_INTEGRATION_PROPERTIES = '/api/account/integration/setProperties';

    public function getUserCustomProperties(Settings $settings);

    public function setUserCustomProperties(Settings $settings, $properties);
}
