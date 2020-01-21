<?php

namespace bhr\Modules\Newsletter\States;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bhr\Modules\Newsletter\Newsletter;

class StateMapper extends Newsletter implements State
{
	const M_TYPE = 'mapper';

    public function setConfig($params)
    {
        $data = [
            'type' => self::M_TYPE,
            Newsletter::MAP_NAME => htmlspecialchars($params[Newsletter::MAP_NAME]),
        ];

        return $this->Model
            ->setNewsletterConfig($data);
    }

    public function setFront()
    {
        return (SM_TEST_MODE)
            ? $this->getTestViewItem()
            : false;
    }

    public function setContactOptStates()
    {
	    $state = $this->getSubscriberStateFromRequest();
	    $this->getContactOptStatusFromSalesmanago();

	    $this->Contact->setIsSubscribingState($state);

	    $state = (!$state)
		    ? $this->SmOptStatus
		    : $state;

	    $this->Contact
		    ->getOptions()
		    ->setForceOptIn($state)
		    ->setForceOptOut(!$state)
		    ->setForcePhoneOptIn($state)
		    ->setForcePhoneOptOut(!$state);

	    $this->checkForDoubleOptIn($state);

	    if ($state) {
		    $this->setCookie($state);
	    }

	    return $this->Contact;
    }

    public function getSubscriberStateFromRequest()
    {
        if (isset($_REQUEST[$this->config[Newsletter::MAP_NAME]])) {
            return true;
        }
        return false;
    }

    private function getTestViewItem()
    {
        return "<p class='woocommerce-FormRow form-row test-mapper-checkbox'>
            <label class='woocommerce-form__label woocommerce-form__label-for-checkbox inline test-mapper-checkbox' style='margin-left: 0'>
                <input class='woocommerce-form__input woocommerce-form__input-checkbox test-mapper-checkbox' type='checkbox'
                       name='testMapperCheckboxName' >
                <span class=test-mapper-checkbox-span'>Test Subscribe For mapper Newsletter type</span>
            </label>
        </p>";
    }
}
