<?php

namespace bhr\Modules\Newsletter\States;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bhr\Modules\Newsletter\Newsletter;

class StateInput extends Newsletter implements State
{
	const
		INP_NAME = 'sm_newsletter',
		I_TYPE = 'newsletter',
		CSS_CLASS = 'sm_newsletter_in';

    public function setConfig($params)
    {
        $data['type'] = self::I_TYPE;

        if (!empty($params[Newsletter::NEWS_CONT])
            && is_array($params[Newsletter::NEWS_CONT])
        ) {
            $newsCont = [];
            foreach ($params[Newsletter::NEWS_CONT] as $langKey => $param) {
                $newsCont[$langKey] = htmlspecialchars($param);
            }

            $data[Newsletter::NEWS_CONT] = $newsCont;
        } else {
            $data[Newsletter::NEWS_CONT] = htmlspecialchars($params[Newsletter::NEWS_CONT]);
        }

        return $this->Model
            ->setNewsletterConfig($data);
    }

    public function setFront()
    {
        if (is_array($this->config[Newsletter::NEWS_CONT])
            && array_key_exists($this->lang, $this->config[Newsletter::NEWS_CONT])
            && !empty($this->config[Newsletter::NEWS_CONT][$this->lang])
        ) {
            return $this->getViewItem($this->config[Newsletter::NEWS_CONT][$this->lang]);
        } elseif (isset($this->config[Newsletter::NEWS_CONT]['default'])
            && !empty($this->config[Newsletter::NEWS_CONT]['default'])
        ) {
            return $this->getViewItem($this->config[Newsletter::NEWS_CONT]['default']);
        }
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
        if (isset($_REQUEST[self::INP_NAME])) {
            return boolval($_REQUEST[self::INP_NAME]);
        }

        return false;
    }

    public function getViewItem($content)
    {
        return "<p class='woocommerce-FormRow form-row {self::CSS_CLASS}'>
            <label class='woocommerce-form__label woocommerce-form__label-for-checkbox inline {self::CSS_CLASS}' style='margin-left: 0'>
                <input class='woocommerce-form__input woocommerce-form__input-checkbox {self::CSS_CLASS}' type='checkbox'
                       name='" . self::INP_NAME  . "'>
                <span class='" . self::CSS_CLASS . "'>{$content}</span>
            </label>
        </p>";
    }
}
