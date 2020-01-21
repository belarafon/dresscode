<?php

namespace bhr\Modules\WooCommerce;

use bhr\Modules\WooCommerce\CronExportModel;
use bhr\Modules\WooCommerce\PurchaseModel;
use SALESmanago\Entity\Settings;
use SALESmanago\Exception\SalesManagoException;


class CronExportController extends ExportController
{
    public $purchaseModel;
    public $interval;

    public function __construct()
    {
        parent::__construct();
        $this->exportModel = new CronExportModel();
        $this->purchaseModel = new PurchaseModel();
        $this->feederActive = model::isFeederActive();
    }

    /**
     * @throws SalesManagoException
     */

    public function exportPlatformExternalEvent()
    {
        try{
            $this->interval = $this->purchaseModel->config;
            if(empty($this->interval)){
                return false;
            }
            $this->exportModel->setAdvancedForExportContacts($this->interval);

            $packages = self::$exportResponse;
            $page = 1;
            $exportData = true;

            while($exportData){
                $exportData = $this->exportModel->getEvents($page);
                if($exportData != false){
                    $response = $this->exportContactExtEvents($exportData);
                    $packages['total']++;
                    if($response['success']){
                        $packages['successful']++;
                        $packages['items'] += count($exportData);
                    } else {
                        $packages['lost'];
                    }
                    $packages['message'] .= $response['message']."; ";
                }
                $page++;
            }
            return $packages;
        } catch (\Exception $e) {
            throw new SalesManagoException($e->getMessage());
        }
    }
}