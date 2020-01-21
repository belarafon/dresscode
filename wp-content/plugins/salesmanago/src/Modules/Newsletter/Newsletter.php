<?php

namespace bhr\Modules\Newsletter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use \SALESmanago\Provider\UserProvider;
use \SALESmanago\Controller\ConnectSalesManagoController;
use \bhr\Model\HooksModel;
use bhr\Helper\Contact\Contact;

class Newsletter {

	const
		MAP_NAME = 'mappedName',
		NEWS_CONT = 'newsletterContent',
		COOKIE_NAME = 'smoptst';

	public $Model;
	public $defaultContactState;
	public $config;
	public $defaultLang;
	public $lang;

	protected $SmOptStatus;

	public $Contact;

	public function __construct()
	{
		$this->Model = new Model();
		$this->defaultContactState = $this->Model->getDefaultContactState();
		$this->config = $this->Model->getNewsletterConfig();
	}

	public function setContact(Contact $Contact)
	{
		$this->Contact = $Contact;
		$this->getContactOptStatusFromSalesmanago();
		return $this;
	}

	public function getType()
	{
		return (isset($this->config['type'])
		        && !empty($this->config['type'])
		) ? $this->config['type']
			: '';
	}

	public function getContactOptStatusFromSalesmanago()
	{
		if (isset($_COOKIE[self::COOKIE_NAME])) {
			$cookie = json_decode(stripslashes($_COOKIE[self::COOKIE_NAME]), true);
		}

		if (isset($cookie) && is_array($cookie)) {
			if (array_key_exists($this->Contact->getEmail(), $cookie)) {
				$this->SmOptStatus = $cookie[$this->Contact->getEmail()];
			}
		} elseif($this->Contact->getEmail()) {
			$response = $this->contactBasic( $this->Contact->getEmail() );
			$this->SmOptStatus = ( $response['success'] )
				? ! $response['contact']['optedOut']
				: false;
		} else {
			$this->SmOptStatus = false;
		}
	}

	public function contactBasic($email)
	{
		$SmConnectController = new ConnectSalesManagoController(
			UserProvider::initSettingsUser(
				new HooksModel()
			)
		);

		return $SmConnectController->getContactBasic( $email );
	}

	public function setCookie($contactOptState)
	{
		$data = json_encode(
			[
				$this->Contact->getEmail() => filter_var($contactOptState, FILTER_VALIDATE_BOOLEAN)
			]
		);

		$period = time() + (3600 * 86400);
		setcookie(self::COOKIE_NAME, $data, $period, '/');
	}

	public function getModel()
	{
		return $this->Model;
	}

	public function setDefaultLang($lang)
	{
		$this->defaultLang = $lang;
		return $this;
	}

	public function setLang($lang)
	{
		$this->lang = $lang;
		return $this;
	}

	public function getLang()
	{
		return $this->lang;
	}

	public function getConfig()
	{
		return $this->Model->getNewsletterConfig();
	}

	/**
	 * @param boolean $optInStatus;
     * @return boolean true if use double opt in else if not;
	*/
	public function checkForDoubleOptIn($optInStatus)
	{
        /*don't send 'useApiDoubleOptIn' if contact exist & is optin*/
        if (isset($this->SmOptStatus) && $this->SmOptStatus) {
            return false;
        }

		$apiDoubleOptIn = $this->Model->getApiDoubleOptIn();

		if ($apiDoubleOptIn && $optInStatus) {
			$this->Contact
				->getOptions()
				->setUseApiDoubleOptIn($apiDoubleOptIn);
			return true;
		}
	}
}
