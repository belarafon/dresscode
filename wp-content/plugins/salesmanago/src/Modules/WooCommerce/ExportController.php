<?php

namespace bhr\Modules\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use \bhr\Controller\AbstractExportController;
use \bhr\Controller\ExportControllerInterface;
use \SALESmanago\Exception\SalesManagoException;

class ExportController extends AbstractExportController implements ExportControllerInterface {

	public function setAdditionalData()
	{
		$this->exportModel = new ExportModel();
		$this->feederActive = Model::isFeederActive();
	}

	/**
	 * @throws SalesManagoException
	*/
	public function exportPlatformExternalEvent() {
		try {
			if ( $this->requestParams['transaction'] != true ) {
				return self::$exportResponse;
			}

			$packages = self::$exportResponse;

			$page       = 1;
			$exportData = true;

			while ( $exportData ) {
				$exportData = $this->exportModel->getEvents($page);
				if ( $exportData != false ) {
					$response = $this->exportContactExtEvents($exportData);
					$packages['total']++;
					if ($response['success']) {
						$packages['successful']++;
						$packages['items'] += count($exportData);
					} else {
						$packages['lost']++;
					}

					$packages['message'] .= $response['message']."; ";
				}
				$page ++;
			}

			return $packages;
		} catch (\Exception $e) {
			throw new SalesManagoException($e->getMessage());
		}
	}

	/**
	 * @throws SalesManagoException;
	*/
	public function countPlatformEvents()
	{
		try {
			return $this->exportModel->countOrders();
		} catch (\Exception $e) {
			throw new SalesManagoException($e->getMessage());
		}
	}
}
