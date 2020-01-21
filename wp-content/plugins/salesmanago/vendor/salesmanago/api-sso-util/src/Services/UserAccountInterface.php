<?php

namespace SALESmanago\Services;

use SALESmanago\Entity\Settings;


interface UserAccountInterface
{
    const METHOD_CREATE_LIVE_CHAT = "/api/wm/createLiveChat",
          METHOD_CREATE_BASIC_POPUP = "/api/wm/createBasicPopup",
          METHOD_CREATE_WEB_PUSH_CONSENT_FORM = "/api/wm/createWebPushConsentForm",
          METHOD_CONSENT_FORM_CODE = "/api/wm/consentFormCode",
          METHOD_ITEM_ACTION = "/api/wm/itemAction",

          METHOD_ADD_SUBSCRIBE_PRODUCTS = "/api/appstore/subscribeProducts",
          METHOD_ACCOUNT_ITEMS = "/api/account/items",

          REDIRECT_APP = "/api/authorization/authorize",
          REFRESH_TOKEN = "/api/authorization/refreshToken",

          METHOD_BATCH_UPSERT = "/api/contact/batchupsert",
          METHOD_BATCH_ADD_EXT_EVENT = "/api/contact/batchAddContactExtEvent",

          METHOD_ACCOUNT_TYPE = "/api/account/accountTypeWithContacts",
          METHOD_UPLOAD_IMAGE = "/api/wm/uploadImage.json",
          METHOD_LIST_USERS = "/api/user/listByClient";

    public function exportContacts(Settings $settings, $upsertDetails);

    public function exportContactExtEvents(Settings $settings, $events);

    public function getAccountTypeWithContacts($userData);

    public function itemAction(Settings $settings, $userData);

    public function consentFormCode($userData);

    public function uploadImage(Settings $settings, $image);

    public function listUsersByClient(Settings $settings);
}
