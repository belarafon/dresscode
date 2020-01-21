<?php

namespace SALESmanago\Services;

use SALESmanago\Entity\Settings;


interface CreateAccountInterface
{
    const METHOD_CREATE_ACCOUNT = "/api/account/registerAppstore",
          METHOD_CONTACT_SUPPORT = "/api/contact/upsertVendorToSupport";

    public function createAccount(Settings $settings, $user, $modulesId);

    public function contactToSupport(Settings $settings, $userDetails);
}
