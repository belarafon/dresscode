<?php

namespace bhr\Modules\Wordpress;

use \bhr\Controller\ExportControllerInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use \bhr\Controller\AbstractExportController;

class ExportController extends AbstractExportController implements ExportControllerInterface {

	public function setAdditionalData()
	{
		$this->exportModel = new ExportModel();
		$this->feederActive = Model::isFeederActive();
	}
}