<?php

namespace bhr\Modules\Newsletter\States;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bhr\Modules\Newsletter\Newsletter;

class StateNoActive extends Newsletter implements State
{
	public function getConfig()
	{
		return $this->Model->getNewsletterConfig();
	}

	public function setContactOptStates()
	{
		$this->getContactOptStatusFromSalesmanago();

		$this->Contact
			->getOptions()
			->setForceOptIn($this->SmOptStatus)
			->setForceOptOut(!$this->SmOptStatus)
			->setForcePhoneOptIn($this->SmOptStatus)
			->setForcePhoneOptOut(!$this->SmOptStatus);

		if ($this->SmOptStatus) {
			$this->setCookie($this->SmOptStatus);
		}

		return $this->Contact;
	}

    public function getSubscriberStateFromRequest()
    {
        return false;
    }

	public function setFront()
	{
		return false;
	}

	public function setConfig($params)
	{
		return false;
	}
}