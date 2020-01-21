<?php

namespace bhr\Model;

trait StoreTrait {

	public function getStoreId($storeDomain = null, $settingsPath = null)
	{
		$stmt = $this->db->get_row($this->db->prepare("SELECT option_value FROM {$this->db->options} WHERE option_name = %s LIMIT 1", parent::SETTINGS_STORE_ID), ARRAY_A);

		if ($stmt != null) {
			return $stmt['option_value'];
		}

		if ($stmt == null && $storeDomain == null) {
			return '';
		} elseif ($stmt == null && $storeDomain != null) {
			return $this->setStoreId($storeDomain, $settingsPath);
		}

		return false;
	}

	/**
	 * Generate and set SALESmanago store identificator based on platform home url;
	 * @param string $storeDomain - base Home Url form platform
	 * @param string $settingsPath - custom setting path
	 * @return boolean
	 */
	private function setStoreId($storeDomain = null, $settingsPath = null)
	{
		if ($storeDomain == null) {
			return false;
		}

		$id = md5($storeDomain);
		$settingsPath = ($settingsPath != null) ? $settingsPath : parent::SETTINGS_STORE_ID;
		$this->db->query($this->db->prepare("INSERT INTO {$this->db->options} (option_id, option_name, option_value) VALUES (NULL, %s, %s)", array($settingsPath, $id)));
		return $id;
	}

}
