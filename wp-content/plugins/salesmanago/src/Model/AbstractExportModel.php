<?php

namespace bhr\Model;

abstract class AbstractExportModel extends UserModel {

	const
        DEFAULT_PACKAGE_LIMIT = 50,
        DEFAULT_INTERVAL = 3600;

	public $packageLimit   = self::DEFAULT_PACKAGE_LIMIT;
	public $advancedExport = false;
	public $dateRange;
	public $tags;
	public $contactOptStatus = '';

	/**
	 * @param string
	 * @return $this;
	*/
	public function setContactOptStatus($param = '')
	{
		$this->contactOptStatus = $param;
		return $this;
	}

	public function setPackageLimit($param)
	{
		$this->packageLimit = $param;
		return $this;
	}

	public function getPackageLimit()
	{
		return $this->packageLimit;
	}

	public function setAdvancedExport($param)
	{
		if (isset($param['advanced'])) {
			$this->setAdvancedForExportContacts($param);
		} else {
			$this->setAdvancedForCountContacts($param);
		}
	}

	/**
	 * @param array $param - request parameters
	 */
	public function setAdvancedForCountContacts($param)
	{
		$this->advancedExport = $param['active'];

		if ($this->advancedExport
		    && !empty($param['tag'])
		) {
			$this->tags = strpos($param['tag'], ',')
				? explode(',', $param['tag'])
				: [$param['tag']];
		}

		if ($this->advancedExport
		    && !empty($param['calendar']['from'])
		    && !empty($param['calendar']['to'])
		) {
			$this->dateRange = [
				'from' => $param['calendar']['from']. ' ' . '00:00:00',
				'to'   => $param['calendar']['to'] . ' ' . '23:59:59'
			];
		}
	}

	/**
	 * @param array $param - request parameters
	 */
	public function setAdvancedForExportContacts($param)
	{
		$this->advancedExport = $param['advanced']['active'];

		if ($this->advancedExport
		    && !empty($param['advanced']['tag'])
		) {
			$this->tags = strpos($param['advanced']['tag'], ',')
				? explode(',', $param['advanced']['tag'])
				: [$param['advanced']['tag']];
		}

		if ($this->advancedExport
		    && !empty($param['advanced']['calendar']['from'])
		    && !empty($param['advanced']['calendar']['to'])
		) {
			$this->dateRange = [
				'from' => $param['advanced']['calendar']['from']. ' ' . '00:00:00',
				'to'   => $param['advanced']['calendar']['to'] . ' ' . '23:59:59'
			];
		}
	}

	public function getCustomers($page = '0')
	{
		return $this->translateCustomer();
	}

	public function translateCustomer($user = array())
	{
		return $user;
	}

	public function countCustomers()
	{
		return (integer)0;
	}
}