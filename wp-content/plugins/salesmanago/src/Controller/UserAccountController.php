<?php

namespace bhr\Controller;

use SALESmanago\Controller\UserAccountController as User;
use SALESmanago\Entity\Settings;
use bhr\Modules\WooCommerce\WooCommerceInterface;

class UserAccountController extends User
{
    public function __construct(Settings $settings, WooCommerceInterface $model)
    {
        parent::__construct($settings, $model);
        $this->model = $model;
    }

    public function userIntegration($userProperties)
    {
        $this->model->setCustomProperties($userProperties['properties']);
        $expPackSize = 50;

        if (in_array(
            'woocommerce/woocommerce.php',
            apply_filters(
                'active_plugins',
                get_option('active_plugins')
            )
        )) {
            if ($userProperties['contacts'] == true) {
                $countExpData = $this->model->countCustomers();
                $exportContactStatus = isset($userProperties['exportWithStatus'])
                    ? $userProperties['exportWithStatus']
                    : '';


                if ($countExpData > $expPackSize) {
                    $loops = ceil($countExpData / $expPackSize);

                    for ($i = 0; $i <= $loops; $i++) {
                        $exportData = $this->model->getCustomersForBatchUpsert(
                            $i,
                            $expPackSize,
                            $exportContactStatus
                        );

                        if (!empty($exportData)) {
                            $this->exportContacts($exportData);
                        }
                    }
                } else {
                    $exportData = $this->model->getCustomersForBatchUpsert(
                        '',
                        '-1',
                        $exportContactStatus
                    );

                    if (!empty($exportData)) {
                        $this->exportContacts($exportData);
                    }
                }
            }

            $exportData = null;

            if ($userProperties['transaction'] == true) {
                $page = 0;
                $exportData = true;
                while ($exportData) {
                    $exportData = $this->model->getEventsForBatchUpsert($page, $expPackSize);
                    if ($exportData != false) {
                        $this->exportContactExtEvents($exportData);
                        $page++;
                    }
                }
                $page = 0;
            }
        }

        return $this->setUserCustomProperties($userProperties['properties']);
    }
}