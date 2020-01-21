<?php

namespace bhr\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bhr\Model\HooksModel;
use bhr\Helper\Contact\Contact;

class Tagger
{
	const
		T_PURCHASE     = 'prc',
		T_REGISTER     = 'rgs',
		T_LOGIN        = 'lgn',
		T_NEWSLETTER   = 'nws',
		T_CUSTOM       = 'cstm',
		T_COOKIE_NAME  = 'smcntctgs',
		T_SESSION_NAME = 'sessionTags';

	private $model;
	private $Contact;
	private $customTag = '';
	private $cookieExpiredTime;

	public function __construct($model)
	{
		$this->model = $model;
		$this->cookieExpiredTime = time() + (3600 * 86400);
	}

	public function setModel($model)
	{
		$this->model = $model;
		return $this;
	}

	public function setContact(Contact &$Contact)
	{
		$this->Contact = $Contact;
		return $this;
	}

	public function getContact()
	{
		if (isset($this->Contact)) {
			return $this->getContact();
		} else {
			throw new \Exception('Tagger - can\'t return not set contact' );
		}
	}

	/**
	 * @param $tagsType = array()
	 * @param $customTag = string for custom solutions
	 *
	 * @return Contact $Contact
	 * @throws \Exception when Contact doesn't set
	 */
	public function setTags($tagsType, $customTag = '')
	{
        try {
            if (!isset($this->Contact)) {
                throw new \Exception("Tagger - can\'t set tags: {$tagsType}; for no contact");
            }

            $this->customTag = $customTag;

            if (is_array($tagsType)) {
                foreach ($tagsType as $type) {
                    $this->switcher($type);
                }
            } else {
                $this->switcher($tagsType);
            }

            $this->setNewsletter();

            return $this->Contact;
        } catch (\Exception $e) {
            error_log(print_r($e->getMessage(), true));
        }
	}

    public function unsetTag($tagsType, $customTag = '')
    {
        try {
            if (!isset($this->Contact)) {
                throw new \Exception("Tagger - can\'t unset tags: {$tagsType}; for no contact");
            }

            $this->customTag = $customTag;

            if (is_array($tagsType)) {
                foreach ($tagsType as $type) {
                    $this->unsetSwitcher($type);
                }
            } else {
                $this->unsetSwitcher($tagsType);
            }

            return $this->Contact;
        } catch (\Exception $e) {
            error_log(print_r($e->getMessage(), true));
        }
    }

	private function switcher($type)
	{
		switch ($type) {
			case self::T_REGISTER:
				$this->setRegister();
				break;
			case self::T_PURCHASE:
				$this->setPurchase();
				break;
			case self::T_NEWSLETTER:
				$this->setNewsletter();
				break;
			case self::T_LOGIN:
				$this->setLogin();
				break;
			case self::T_CUSTOM:
				$this->setCustom();
				break;
			default:
				$this->setNewsletter();
				break;
		}
	}

    private function unsetSwitcher($type)
    {
        switch ($type) {
            case self::T_PURCHASE:
                $this->customUnsetCheck(
                    strtolower($this->model->getTags(HooksModel::T_PURCHASE))
                );
                break;
        }
    }

	private function setPurchase()
	{
		$tag = strtolower($this->model->getTags(HooksModel::T_PURCHASE));
		$this->customCheck($tag);
	}

	private function setRegister()
	{
		$tag = strtolower($this->model->getTags(HooksModel::T_REGISTER));
		$this->customCheckWithCookie($tag, self::T_REGISTER);
	}

	private function setLogin()
	{
		$tag = strtolower($this->model->getTags(HooksModel::T_LOGIN));
		$this->customCheck($tag);
	}

    private function setNewsletter()
    {
        if ($this->Contact->getOptions()->getForceOptIn() == true
            && $this->Contact->getIsSubscribingState()
        ) {
            $tag = strtolower($this->model->getTags(HooksModel::T_NEWS));
            $this->customCheckWithCookie($tag, self::T_NEWSLETTER);
        }
    }

	private function setCustom()
	{
		if (!empty($this->customTag)) {
			$tag = strtolower($this->customTag);
			$this->customCheck($tag);
		}
	}

	private function customCheckWithCookie($tag, $shortTag)
	{
		$tag = strtolower($tag);

		$contactTags = $this->getContactTagsFromWebStorage();

		if ($contactTags) {
			$contactTags = explode(',', $contactTags);

			if (!in_array($shortTag, $contactTags)) {
				$contactTags[] = $shortTag;
				$contactTags = implode(',', $contactTags);
				$this->setContactTagsToSession($contactTags);

				$cTags = $this->Contact
					->getOptions()
					->getTags();

				if (!empty($cTags)
				    && !empty($tag)
				) {
					$cTags .= ','.$tag;
					$this->Contact->getOptions()->setTags($cTags);
				} elseif (!empty($tag)) {
					$this->Contact->getOptions()->setTags($tag);
				}
			}
		} else {
			$this->setContactTagsToSession($shortTag);
			if (!empty($tag)) {
				$this->Contact->getOptions()->setTags($tag);
			}
		}

		if (!empty($_SESSION['sessionTags'])) {
			$this->setContactTagsToCookie($_SESSION['sessionTags']);
		}

		return $this->Contact;
	}

	private function customCheck($tag)
	{
		$tag = strtolower($tag);
		$cTags = $this->Contact->getOptions()->getTags();

		if (!empty($cTags)) {
			if (!in_array($tag, explode(',', $cTags))) {
				$tToSet = $cTags. ',' . $tag;
				$this->Contact
					->getOptions()
					->setTags($tToSet);
			}
		} else {
			$this->Contact
				->getOptions()
				->setTags($tag);
		}
	}

    private function customUnsetCheck($tag)
    {
        $tag = strtolower($tag);
        $contactTags = $this->Contact->getOptions()->getTags();

        if ($contactTags == $tag) {
            $this->Contact->getOptions()->setTags('');
        } elseif (strpos($contactTags, ',') !== false) {
            $tagsArr = explode(',', $contactTags);
            if (is_array($tagsArr)) {
                if (($key = array_search($tag, $tagsArr)) !== false) {
                    unset($tagsArr[$key]);
                }
                $this->Contact->getOptions()->setTags(implode(',', $tagsArr));
            }
        }
    }

	/**
	 * Get tags form SESSION variable or Cookies depends which exist,
	 * for $this->Contact->email
	 *
	 * @return string $tags
	*/
    private function getContactTagsFromWebStorage()
    {
	    $contactTags = $this->getContactTagsFromCookie();

	    $contactTags = !empty($contactTags)
		    ? $contactTags
		    : $this->getContactTagsFromSession();

    	return strtolower($contactTags);
    }

	/**
	 * Get tags from cookie by $this->Contact->email
	 * @return string $tagsFromCookie - string of tags for contact email;
	*/
	private function getContactTagsFromCookie()
	{
        $contactEmail = $this->_getContactEmail();

		$tagsFromCookie = isset($_COOKIE[self::T_COOKIE_NAME])
			? $_COOKIE[self::T_COOKIE_NAME]
			: '';

		if (!empty( $tagsFromCookie )) {
			$tagsFromCookie = json_decode(stripslashes($tagsFromCookie), true);
			$tagsFromCookie = (array_key_exists($contactEmail, $tagsFromCookie))
				? $tagsFromCookie[$contactEmail]
				: '';
		}

		return strtolower($tagsFromCookie);
	}

	/**
	 * @return mixed - array if Cookie exist otherwise false.
	*/
	private function getTagsCookie()
	{
		if (!isset($_COOKIE[self::T_COOKIE_NAME])) {
			return null;
		}
		
		return json_decode(stripslashes($_COOKIE[self::T_COOKIE_NAME]), true);
	}

	/**
	 * Set tags for $this->Contact->email to Cookie from SESSION variable;
	 * @param string $sessionVariableWithAllTags - json from session variable
	 * @return boolean setcookie()
	 */
	private function setContactTagsToCookie($sessionVariableWithAllTags)
	{
		$contactEmail = $this->_getContactEmail();
		$tagsCookie = $this->getTagsCookie();
		$tagsSession = json_decode($sessionVariableWithAllTags, true);

		if ($tagsCookie == null) {
			return setcookie(self::T_COOKIE_NAME, $sessionVariableWithAllTags, $this->cookieExpiredTime, '/');
		}

		if ((array_key_exists($contactEmail, $tagsCookie)
			&& array_key_exists($contactEmail, $tagsSession))

		    || (!array_key_exists($contactEmail, $tagsCookie)
		        && array_key_exists($contactEmail, $tagsSession)
		    )
		) {
			$tagsCookie[$contactEmail] = $tagsSession[$contactEmail];
		}

		return setcookie(self::T_COOKIE_NAME, json_encode($tagsCookie), $this->cookieExpiredTime, '/');
	}

	/**
	 * Get tags from SESSION variable by $this->Contact->email
	 * @return string $sessionTags - string of tags for $this->Contact->email;
	 */
	private function getContactTagsFromSession()
	{
        $cEmail = $this->_getContactEmail();

		$sessionTags = isset($_SESSION['sessionTags'])
			? $_SESSION['sessionTags']
			: '';

		if (!empty( $sessionTags )) {
			$sessionTags = json_decode($sessionTags, true);
			$sessionTags = (array_key_exists($cEmail, $sessionTags))
				? $sessionTags[$cEmail]
				: '';
		}

		return strtolower($sessionTags);
	}

	/**
	 * Set tags for $this->Contact->email to Session variable;
	 * @param string $string - tags for $this->Contact->email
	 * @return string/json
	 */
	private function setContactTagsToSession($string)
	{
	    $cEmail = $this->_getContactEmail();

		if (!isset($_SESSION[self::T_SESSION_NAME])) {
			$_SESSION[self::T_SESSION_NAME] = json_encode(array($cEmail => $string));
			return $_SESSION[self::T_SESSION_NAME];
		}

		$sessionUnJsonVariable = json_decode($_SESSION[self::T_SESSION_NAME], true);

		if (!is_array($sessionUnJsonVariable)) {
			unset($_SESSION[self::T_SESSION_NAME]);
			$this->setContactTagsToSession($string);
		}

		$sessionUnJsonVariable[$cEmail] = $string;
		$_SESSION[self::T_SESSION_NAME] = json_encode($sessionUnJsonVariable);
		return $_SESSION[self::T_SESSION_NAME];
	}

    /**
     * Get hash from contact email to use it in public storage
     */
    protected function _getContactEmail()
    {
        $email = $this->Contact->getEmail();
        return hash("sha256", $email);
    }
}
