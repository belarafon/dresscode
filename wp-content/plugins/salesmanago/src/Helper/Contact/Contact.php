<?php

namespace bhr\Helper\Contact;

use SALESmanago\Exception\Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Contact
{
    const
        ASYNC   = 'async',
        CONTACT = 'contact',
        EMAIL   = 'email',
        C_ID    = 'contactId',
        FAX     = 'fax',
        NAME    = 'name',
        PHONE   = 'phone',
        COMPANY = 'company',
        STATE   = 'state',
        ADDRESS = 'address',
        EXT_ID  = 'externalId';

    private $async = true;

    private $email;
    private $contactId;
    private $fax;
    private $name;
    private $phone;
    private $company;
    private $state;
    private $externalId;

    private $Address;
    private $Options;
    private $Consents;

    /**
     * This means that contact is subscribing now
     * during scrip is executing now;
     * */
    private $isSubscribing = false;

    public $contact = array();

    public function __construct(
        $email = '',
        $contactId = '',
        $name = array(),
        $phone = '',
        $fax = '',
        $company = '',
        $state = '',
        $externalId = ''
    ) {
        $this->email      = $email;
        $this->contactId  = $contactId;

        $this->name       = $name;
        $this->phone      = $phone;
        $this->fax        = $fax;
        $this->company    = $company;
        $this->state      = $state;
        $this->externalId = $externalId;
    }

    /**
     * Sets boolean $this->isSubscribing state of contact subscribing at that moment
     *
     * @param boolean
     * @return $this
     * */
	public function setIsSubscribingState($bool)
	{
		$this->isSubscribing = boolval($bool);
		return $this;
	}

	/**
	 * Sets subscriber actual subscribing flag,
	 * $this->isSubscribing - if contact subscribing at that moment;
	 *
	 * @return bool $this->isSubscribing
	*/
    public function getIsSubscribingState()
    {
		return $this->isSubscribing;
    }

    public function setEmail($param)
    {
        $this->email = $param;
        return $this;
    }

    public function getEmail()
    {
    	if (isset($this->email) && !empty($this->email)) {
    		return $this->email;
	    } else {
		    throw new \Exception('Can\'t remove empty Contact email');
	    }

    }

    public function setContactId($param)
    {
        $this->contactId = $param;
        return $this;
    }

    public function getContactId()
    {
    	return $this->contactId;
    }

    public function setName($param)
    {
        $this->name = $param;
        return $this;
    }

    public function setFax($param)
    {
        $this->fax = $param;
        return $this;
    }

    public function setPhone($param)
    {
        $this->phone = $param;
        return $this;
    }

    public function setCompany($param)
    {
        $this->company = $param;
        return $this;
    }

    public function setState($param)
    {
        $this->state = $param;
        return $this;
    }

    public function setExtId($param)
    {
        $this->externalId = $param;
        return $this;
    }

    public function setAddress(Address $ContactAddress)
    {
        $this->Address = $ContactAddress;
        return $this;
    }

    public function getAddress()
    {
    	return isset($this->Address)
		    ? $this->Address
		    : $this->Address = new Address();
    }

    public function setOptions(Options $Options)
    {
        $this->Options = $Options;
        return $this;
    }

    public function getOptions()
    {
    	return isset($this->Options)
		    ? $this->Options
		    : $this->Options = new Options();
    }

    /**
     * @param Consents $Consents
     * @return $this
     */
    public function setConsents(Consents $Consents)
    {
        $this->Consents = $Consents;
        return $this;
    }

    /**
     * @return Consents
     */
    public function getConsents()
    {
        return isset($this->Consents)
            ? $this->Consents
            : $this->Consents = new Consents();
    }

    public function setAsync($bool)
    {
	    $this->async = filter_var($bool, FILTER_VALIDATE_BOOLEAN);
    }

    public function get()
    {
        if (empty($this->email) && empty($this->contactId)) {
            throw new \Exception('Contact identificators wasn\'t specified');
        }

        $contact = array(
        	self::ASYNC => $this->async,
            self::CONTACT =>
            array(
                self::FAX  => $this->fax,
                self::NAME => $this->setStrFromArr($this->name),
                self::PHONE   => $this->phone,
                self::COMPANY => $this->company,
                self::STATE   => $this->state,
                self::EXT_ID  => $this->externalId
                )
        );

	    if (!empty($this->email)) {
		    $contact[self::CONTACT] = array_merge(
			    $contact[self::CONTACT],
			    array(self::EMAIL => $this->email)
		    );
	    } else {
		    $contact[self::CONTACT] = array_merge(
                $contact[self::CONTACT],
                array(self::C_ID => $this->contactId)
            );
	    }

        if (!empty($this->Address)) {
            $contact[self::CONTACT] = array_merge(
                $contact[self::CONTACT],
                $this->Address->get()
            );
        }

        if (isset($this->Options)) {
            $contact = array_merge(
                $contact,
                $this->Options->get()
            );
        }

        if (isset($this->Consents)) {
            $contact = array_merge(
                $contact,
                $this->Consents->getConsentDetailsArr()
            );
        }

        return $this->filterData($contact);
    }

    /**
     * @throws \Exception;
     * @return array $contact without async parameter;
    */
	public function getForExport()
	{
		$contact = $this->get();
		unset($contact[self::ASYNC]);

		/*fix SM service problem in batchUpsert in v2.4.12*/
		if (isset($contact[Options::TAGS])
		    && !is_array($contact[Options::TAGS])
		) {
			$contact[Options::TAGS] = explode(',', $contact[Options::TAGS]);
		}

		return $contact;
	}

    public function getDataOptions()
    {
        $data = array(
            'data' => array(),
            'options' => array()
        );

        $contact = $this->get();
        $data['data'] = $contact[self::CONTACT];
        unset($contact[self::CONTACT]);
        $data['options'] = $contact;

        return $data;
    }

    public function getForInstantUpsert()
    {
        if (empty($this->email)) {
            throw new \Exception('Contact identificators wasn\'t specified');
        }

        $contact = array(
            self::EMAIL => $this->email,
            self::NAME  => $this->setNameFromArray(),
        );

        if (isset($this->Options)) {
            $options = $this->Options->get();
            $contact = array_merge($contact, $options);
        }

        return $contact;
    }

    protected function setStrFromArr($param, $glue = ' ')
    {
        if (!is_array($param)) {
            return $param;
        }

        $param = $this->filterArr($param);
        if (!empty($param)) {
            return implode($glue, $param);
        }

        return '';
    }

    protected function filterArr($array)
    {
        return array_filter(
            $array,
            function ($value) {
                return !empty($value);
            }
        );
    }

    final protected function filterData($data)
    {
        $data = array_map(function ($var) {
            return is_array($var) ? $this->filterData($var) : $var;
        }, $data);
        $data = array_filter($data, function ($value) {
            return !empty($value) || $value === false;
        });
        return $data;
    }
}
