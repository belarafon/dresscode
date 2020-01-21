<?php

namespace SALESmanago\Services;

use SALESmanago\Entity\Settings;


interface LoginAccountInterface
{
    const METHOD_LOGIN_AUTHORIZE = "/api/authorization/token",
          METHOD_ACCOUNT_INTEGRATION = "/api/account/integration";

    public function accountAuthorize($user);

    public function accountIntegrationSettings(Settings $settings);
}
