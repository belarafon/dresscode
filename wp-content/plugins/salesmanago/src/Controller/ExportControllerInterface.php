<?php

namespace bhr\Controller;

interface ExportControllerInterface {

	/**
	 * return integer - count of counted contacts;
	*/
	public function countPlatformContacts();

	/**
	 * return integer - count of exported contacts;
	 */
	public function exportPlatformContacts();

	/**
	 * return integer - count of exported events;
	 */
	public function exportPlatformExternalEvent();

	/**
	 * return $this
	*/
	public function checkForAdvancedExport();

}