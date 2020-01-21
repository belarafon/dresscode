<?php

namespace bhr\Helper\Contact;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Options
{
    const
        BIRTHDAY    = 'birthday',

        TAGS        = 'tags',
        PROPERTIES  = 'properties',
        R_TAGS      = 'removeTags',

        F_OPT_IN    = 'forceOptIn',
        F_OPT_OUT   = 'forceOptOut',

        F_P_OPT_INT = 'forcePhoneOptIn',
        F_P_OPT_OUT = 'forcePhoneOptOut',
        N_EMAIL     = 'newEmail',

        U_API_D_OPT_IN        = 'useApiDoubleOptIn',
	    D_OPT_IN_TEMPLATE_ID  = 'apiDoubleOptInEmailTemplateId',
        D_OPT_IN_EMAIL_ACC_ID = 'apiDoubleOptInEmailAccountId',
        D_OPT_IN_EMAIL_SUBJ   = 'apiDoubleOptInEmailSubject';

    private $birthday;
    private $forceOptIn;
    private $forceOptOut;
    private $forcePhoneOptIn;
    private $forcePhoneOptOut;

    private $properties;

    private $tags;
    private $removeTags;

    private $newEmail;

    /**
     * array
    */
    private $customOptions;

    private $useApiDoubleOptIn = array(
    	self::U_API_D_OPT_IN        => false,
	    self::D_OPT_IN_TEMPLATE_ID  => '',
	    self::D_OPT_IN_EMAIL_ACC_ID => '',
	    self::D_OPT_IN_EMAIL_SUBJ   => ''
    );

    public function __construct(
        $tags = array(),
        $removeTags = array(),
        $birthday = '',
        $forceOptIn = '',
        $forceOptOut = '',
        $forcePhoneOptIn = '',
        $forcePhoneOptOut = '',
        $properties = array(),
        $newEmail = ''
    ) {
        $this->tags             = $tags;
        $this->removeTags       = $removeTags;
        $this->birthday         = $birthday;
        $this->forceOptIn       = $forceOptIn;
        $this->forceOptOut      = $forceOptOut;
        $this->forcePhoneOptIn  = $forcePhoneOptIn;
        $this->forcePhoneOptOut = $forcePhoneOptOut;
        $this->properties       = $properties;
        $this->newEmail         = $newEmail;
    }

    public function setTags($param)
    {
        $this->tags = $param;
        return $this;
    }

    public function getTags()
    {
    	return $this->tags;
    }

    public function setRemoveTags($param)
    {
        $this->removeTags = $param;
        return $this;
    }

    public function getRemoveTags()
    {
        return $this->removeTags;
    }

    public function setBirthday($param)
    {
        $this->birthday = $param;
        return $this;
    }

    public function setForceOptIn($param)
    {
        $this->forceOptIn = $param;
        return $this;
    }

	public function getForceOptIn()
	{
		return $this->forceOptIn;
	}

    public function setForceOptOut($param)
    {
        $this->forceOptOut = $param;
        return $this;
    }

    public function setForcePhoneOptIn($param)
    {
        $this->forcePhoneOptIn = $param;
        return $this;
    }

    public function setForcePhoneOptOut($param)
    {
        $this->forcePhoneOptOut = $param;
        return $this;
    }

    public function setProperties($param)
    {
        $this->properties = $param;
        return $this;
    }

    public function setNewEmail($param)
    {
        $this->newEmail = $param;
        return $this;
    }

	/**
	 * @param array - array(
	 *                      'useApiDoubleOptIn' => true/false,
	 *                      'apiDoubleOptInEmailTemplateId' => '',
	 *                      'apiDoubleOptInEmailAccountId' => '',
	 *                      'apiDoubleOptInEmailSubject' => ''
	 *                )
	 * @return $this;
	 */
    public function setUseApiDoubleOptIn($params)
    {
		if(empty($params)
		   || !isset($params[self::U_API_D_OPT_IN])
		   || !$params['useApiDoubleOptIn']
		) {
			return $this;
		}

		$this->useApiDoubleOptIn[self::U_API_D_OPT_IN] = $params[self::U_API_D_OPT_IN];

		if (isset($params[self::D_OPT_IN_TEMPLATE_ID])) {
			$this->useApiDoubleOptIn[self::D_OPT_IN_TEMPLATE_ID] = $params[self::D_OPT_IN_TEMPLATE_ID];
		}

		if (isset($params[self::D_OPT_IN_EMAIL_ACC_ID])) {
			$this->useApiDoubleOptIn[self::D_OPT_IN_EMAIL_ACC_ID] = $params[self::D_OPT_IN_EMAIL_ACC_ID];
		}

		if (isset($params[self::D_OPT_IN_EMAIL_SUBJ])) {
			$this->useApiDoubleOptIn[self::D_OPT_IN_EMAIL_SUBJ] = $params[self::D_OPT_IN_EMAIL_SUBJ];
		}

		return $this;
    }

    /**
     * Sets custom options
     * @param array
     * @return $this
    */
    public function setCustomOptions($params) {

        if (isset($this->customOptions) && is_array($this->customOptions)) {
            $this->customOptions = array_merge($this->customOptions, $params);
            return $this;
        }

        $this->customOptions = $params;
        return $this;
    }

	/**
	 * @return array
	 */
	public function getUseApiDoubleOptIn() {
		return $this->useApiDoubleOptIn;
	}

    public function get()
    {
        $this->setOptStatusesBaseOnForceOptIn();
        $options = array(
            self::TAGS        => $this->setStrFromArr($this->tags, ','),
            self::R_TAGS      => $this->setStrFromArr($this->removeTags, ','),
            self::N_EMAIL     => $this->newEmail,
            self::PROPERTIES  => $this->properties,
            self::F_OPT_IN    => $this->forceOptIn,
            self::F_OPT_OUT   => $this->forceOptOut,
            self::F_P_OPT_INT => $this->forcePhoneOptIn,
            self::F_P_OPT_OUT => $this->forcePhoneOptOut,
            self::BIRTHDAY    => $this->birthday
        );

        if (isset($this->customOptions)) {
            $options = array_merge($this->customOptions, $options);
        }

        if ($this->useApiDoubleOptIn[self::U_API_D_OPT_IN]) {
	        $options = array_merge($this->useApiDoubleOptIn, $options);
        }

        return $this->filterData($options);
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

    protected function setOptStatusesBaseOnForceOptIn()
    {
        if (is_bool($this->forceOptIn)
            && $this->forceOptOut != ''
        ) {
            $this->forceOptOut = !$this->forceOptIn;
            $this->forcePhoneOptIn = $this->forceOptIn;
            $this->forcePhoneOptOut = !$this->forceOptIn;
        }
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
