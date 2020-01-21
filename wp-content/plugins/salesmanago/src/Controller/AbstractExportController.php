<?php

namespace bhr\Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use \SALESmanago\Controller\UserAccountController;
use SALESmanago\Exception\SalesManagoException;
use \SALESmanago\Provider\UserProvider;
use \bhr\Model\UserModel;

abstract class AbstractExportController extends UserAccountController {

	public $model;
	public $exportModel;
	public $userProperties;
	public $feederActive;
	public $requestParams;

	public static $exportResponse = [
			'total' => 0,
		    'items' => 0,
			'successful' => 0,
			'lost' => 0,
			'message' => '',
	];

	public function __construct($params = null) {
		$this->requestParams = $params;
		$this->setAdditionalData();
		$this->model = new UserModel();
		$settings = UserProvider::initSettingsUser( $this->model );
		parent::__construct( $settings, $this->model );
	}

	/**
	 * @exportModel - ExportModel
	 * @feederActive - Model::isFeederActive()
	 */
	public function setAdditionalData() {
		$this->exportModel = null;
		$this->feederActive = false;
	}

	/**
	 * @throws SalesManagoException
	 * @return $this
	*/
	public function checkForAdvancedExport() {
		try {
			if ( isset( $this->requestParams['advanced'] )/*this is for export method*/
			     && isset( $this->requestParams['advanced']['active'] )
			     && isset( $this->requestParams['advanced']['calendar'] )
			) {
				$active   = $this->requestParams['advanced']['active'];
				$calendar = $this->requestParams['advanced']['calendar'];
			} elseif/*this is for count method*/ (
				isset( $this->requestParams['active'] )
				&& isset( $this->requestParams['calendar'] )
			) {
				$active   = $this->requestParams['active'];
				$calendar = $this->requestParams['calendar'];
			}

			if ( ! empty( $active )
			     && ! empty( $calendar )
			) {
				$this->exportModel->setAdvancedExport( $this->requestParams );
			}

			return $this;
		} catch (\Exception $e) {
			throw new SalesManagoException($e->getMessage());
		}
	}

	/**
	 * @return integer - counted contacts
	*/
	public function countPlatformContacts() {
		if ( $this->requestParams['active'] ) {
 			$contacts = $this->exportModel
				->countCustomers();

			return $contacts;
		} else {
			return (integer)0;
		}
	}

	/**
	 * @throws SalesManagoException
	 * @return array export packages inf
	*/
	public function exportPlatformContacts()
	{
		try {
			if ( $this->requestParams['contacts'] != true ) {
				return self::$exportResponse;
			}

			$packages = self::$exportResponse;

			$exportContactStatus = isset($this->requestParams['exportWithStatus'])
				? $this->requestParams['exportWithStatus']
				: '';

			$this->exportModel->setContactOptStatus( $exportContactStatus );

			$countExpData = $this->exportModel->countCustomers();

			if ( $countExpData > $this->exportModel->getPackageLimit() ) {
				$loops = ceil( $countExpData / $this->exportModel->getPackageLimit() );

				$packages['total'] = $loops-1;

				for ( $i = 0; $i <= $loops; $i ++ ) {
					$exportData = $this->exportModel->getCustomers( $i );

					if ( ! empty( $exportData ) ) {
						$response = $this->exportContacts( $exportData );


						if($response['success']) {
							$packages['successful']++;
						} elseif (!$response['success']) {
							$packages['lost']++;
							$packages['message'] .= $response['message']."; ";
						}
					}
				}
			} else {
				$this->exportModel->setPackageLimit( '-1' );
				$exportData = $this->exportModel->getCustomers( '' );

				$packages['total'] = 1;

				if ( ! empty( $exportData ) ) {
					$response = $this->exportContacts( $exportData );

					if ( ! $response['success'] ) {
						$packages['lost'] = 1;
						$packages['message'] = $response['message']."; ";
					} else {
						$packages['success'] = 1;
					}
				}
			}
			return $packages;
		} catch (\Exception $e) {
			throw new SalesManagoException($e->getMessage());
		}
	}

	/**
	 * @retrurn integer
	*/
	public function countPlatformEvents()
	{
		return (integer)0;
	}

	/**
	 * @return array - info of export
	*/
	public function exportPlatformExternalEvent()
	{
		return self::$exportResponse;
	}

	/**
	 * @param object $exportModel
	 * @throws SalesManagoException
	 * @return $this
	*/
	public function setExportModel( $exportModel = null ) {
		try {
			if ( $exportModel != null ) {
				$this->exportModel = $exportModel;
			} else {
				$this->exportModel = new ExportModel();
			}

			$this->exportModel->setPackageLimit( 50 );

			return $this;
		} catch (\Exception $e) {
			throw new SalesManagoException($e->getMessage());
		}
	}
}
