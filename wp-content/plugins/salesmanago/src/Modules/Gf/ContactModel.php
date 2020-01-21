<?php

namespace bhr\Modules\Gf;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

use bhr\Model\ContactModelInterface;

use bhr\Helper\Contact\Contact;
use bhr\Helper\Contact\Address;
use bhr\Helper\Contact\Options;
use bhr\Helper\Contact\Consents;
use bhr\Helper\Functions;

use SALESmanago\Exception\SalesManagoException;

class ContactModel implements ContactModelInterface
{
    const
        CONF                 = 'confirmation',
        CONF_DOUBLE          = 'double',
        PROPS                = 'properties',
        PREFIX_FIELD         = 'sm-',
        CONSENT_FIELD        = 'sm-consent-',

        FIELDS               = 'fields',
        NAME_FIELD           = 'name',
        LAST_NAME_FIELD      = 'lastname',
        EMAIL_FIELD          = 'email',
        PHONE_FIELD          = 'phone',
        COMPANY_FIELD        = 'company',
        STREET_ADDRESS_FIELD = 'streetaddress',
        ADDRESS_CD_FIELD     = 'address2',
        ZIPCODE_FIELD        = 'zipcode',
        CITY_FIELD           = 'city',
        COUNTRY_FIELD        = 'country',
        OPT_IN_FIELD         = 'optin',

        FORCE_OPTIN          = 'forceOptIn',

        CONTACT_CONSENTS     = 'consents';

    private $Contact;
    private $Address;
    private $Options;
    private $Consents;

    private $smController;

    private $synchronize;
    private $useDoubleOptIn;
    private $smContactOptInStatus = null;
    private $subscribingNow = false;

    private $entry;
    private $form;
    private $gfConfig;
    private $formConfig;

    protected $settingsModel;

    public function __construct($smController, ModuleConfigurationInterface $model)
    {
        $this->Contact = new Contact();
        $this->Address = new Address();
        $this->Options = new Options();
        $this->Consents = new Consents();

        $this->smController = $smController;
        $this->settingsModel = $model;

        $this->setSynchronize();
        $this->setUseDoubleOptIn();
    }

    /**
     * This method is used for sets additional parameters to ContactModel depends on platform & hook parameters
     * @param  array $array;
     * @return $this
    */
    public function setParameters($array)
    {
        $this->entry = $array['entry'];
        $this->form = $array['form'];
        $this->gfConfig = $array['gfConfig'];
        $this->formConfig = $array['formConfig'];

        return $this;
    }

    /**
     * Get & parse contact to Contact object
     * @return Contact
     * @throws SalesManagoException
    */
    public function get()
    {
        try {
            $contact = $this->getFormData();

            $this->Contact->setName(
                isset($contact[self::NAME_FIELD])
                    ? implode(' ', $contact[self::NAME_FIELD])
                    : ''
            )->setEmail(
                isset($contact[self::EMAIL_FIELD])
                    ? $contact[self::EMAIL_FIELD]
                    : ''
            )->setPhone(
                isset($contact[self::PHONE_FIELD])
                    ? $contact[self::PHONE_FIELD]
                    : ''
            )->setCompany(
                isset($contact[self::COMPANY_FIELD])
                    ? $contact[self::COMPANY_FIELD]
                    : ''
            );

            $this->Address->setStreetAddress(
                isset($contact[self::STREET_ADDRESS_FIELD])
                    ? implode(' ', $contact[self::STREET_ADDRESS_FIELD])
                    : ''
            )->setCountry(
                isset($contact[self::COUNTRY_FIELD]) ? $contact[self::COUNTRY_FIELD] : ''
            )->setCity(
                isset($contact[self::CITY_FIELD]) ? $contact[self::CITY_FIELD] : ''
            )->setZipCode(
                isset($contact[self::ZIPCODE_FIELD]) ? $contact[self::ZIPCODE_FIELD] : ''
            );
            
            $this->Options
                ->setTags(isset($this->formConfig['tags']) ? $this->formConfig['tags'] : '')
                ->setRemoveTags(isset($this->formConfig['removeTags']) ? $this->formConfig['removeTags'] : '');

            $this->Options->setProperties(
                isset($contact[self::PROPS])
                    ? $contact[self::PROPS]
                    : ''
            );

            $this->setConsentsToContact($contact);

            $this->Contact
                ->setAddress($this->Address)
                ->setOptions($this->Options)
                ->setConsents($this->Consents);

            if (isset($contact[self::OPT_IN_FIELD])) {
                $this->setOptInStatuses($contact[self::OPT_IN_FIELD]);
            } else {
                $this->setOptInStatuses();
            }

            return $this->Contact;
        } catch (\Exception $e) {
            throw new SalesManagoException($e->getMessage());
        }
    }

    /**
     * Parse data from from to contact array
     * @return array $contact
     * @throws SalesManagoException
    */
    private function getFormData()
    {
        try {
            $contact = [];
            $noPropName = 'customProperties';
            $noPropNameCount = 0;

            foreach ($this->form[self::FIELDS] as $field) {
                $value = rgar($this->entry, (string)$field->id);

                if ($this->isConsentField($field->adminLabel)) {
                    $consent = $this->getCheckConsentsFormData($field->adminLabel, $value, $field);
                    $contact[self::CONTACT_CONSENTS][$consent['nr']][$consent['key']] = $consent['value'];
                    continue;
                }

                if ($this->isContactField($field->adminLabel)  && !$this->isConsentField($field->adminLabel)) {
                    $key = strtolower(substr($field->adminLabel, 3));

                    switch ($key) {
                        case self::NAME_FIELD:
                            $contact[self::NAME_FIELD][] = $value;
                            break;
                        case self::LAST_NAME_FIELD:
                            $contact[self::NAME_FIELD][] = $value;
                            break;
                        case self::STREET_ADDRESS_FIELD:
                            $contact[self::STREET_ADDRESS_FIELD][] = $value;
                            break;
                        case self::ADDRESS_CD_FIELD:
                            $contact[self::STREET_ADDRESS_FIELD][] = $value;
                            break;
                        case self::OPT_IN_FIELD:
                            $contact[self::OPT_IN_FIELD] = rgar($this->entry, (string)$field->get_entry_inputs()[0]['id']);
                            break;
                        default:
                            $contact[$key] = $value;
                    }

                } else {
                    $inputs = $field->get_entry_inputs();

                    if (!isset($field->adminLabel) || empty($field->adminLabel)) {
                        $noPropNameCount++;
                        $field->adminLabel = $noPropName.$noPropNameCount;
                    }

                    if (is_array($inputs)) {
                        foreach ($inputs as $input) {
                            $value = rgar($this->entry, (string)$input['id']);

                            if ($value != "") {
                                if (isset($contact[self::PROPS][$field->adminLabel])) {
                                    $contact[self::PROPS][$field->adminLabel] .= $value . ",";
                                } else {
                                    $contact[self::PROPS][$field->adminLabel] = $value . ",";
                                }

                                $contact[self::PROPS][$field->adminLabel] = Functions::checkCropProperties($contact[self::PROPS][$field->adminLabel]);
                            } else {
                                continue;
                            }
                        }

                        $contact[self::PROPS][$field->adminLabel] = isset($contact[self::PROPS][$field->adminLabel])
                            ? Functions::checkCropProperties(substr($contact[self::PROPS][$field->adminLabel], 0, -1))
                            : '';

                    } else {
                        $contact[self::PROPS][$field->adminLabel] = Functions::checkCropProperties($value);
                    }
                }
            }

            return $contact;
        } catch (\Exception $e) {
            throw new SalesManagoException($e->getMessage());
        }
    }

    /**
     * @param string $fieldName - if you use multiply consents your filed name must look like sm-consent-$nr-$fieldName
     *                            where $nr is number of your consent;
     * @param mixed $field
     * @param string $filedValue
     * @return array
     */
    private function getCheckConsentsFormData($fieldName, $filedValue, $field = null)
    {
        $name = str_replace(self::CONSENT_FIELD, '', $fieldName);

        if (strpos($name, Consents::CONSENT_ACCEPT) !== false && $field != null) {
            //this is for consent checkbox, it's value is stored in $this->entry
            $filedValue = (empty($filedValue)) ? $this->entry[$field->inputs[0]['id']] : $filedValue;
        }

        if (strpos($name, '-') !== false) {
            $nrKeyArr = explode('-', $name);
            $consent = [
                'nr' => $nrKeyArr[0],
                'key' => $nrKeyArr[1],
                'value' => $filedValue
            ];
        } else {
            $consent = [
                'nr' => 0,
                'key' => $name,
                'value' => $filedValue
            ];
        }

        return $consent;
    }

    /**
     * Check if field is field is an SALESmanago consent filed
     * @param string $fieldName
     * @return boolean
     */
    private function isConsentField($fieldName)
    {
        return strpos($fieldName, self::CONSENT_FIELD) !== false;
    }

    /**
     * @param $contact - $this->getFormData() method return;
     * @return mixed
     */
    private function setConsentsToContact($contact)
    {
        if (!array_key_exists(self::CONTACT_CONSENTS, $contact)) {
            return false;
        }

        foreach ($contact[self::CONTACT_CONSENTS] as $consent) {
            $consentName = array_key_exists(Consents::CONSENT_NAME, $consent)
                ? $consent[Consents::CONSENT_NAME]
                : '';
            $consentAccept = array_key_exists(Consents::CONSENT_ACCEPT, $consent)
                ? $consent[Consents::CONSENT_ACCEPT]
                : '';
            $optOut = array_key_exists(Consents::OPT_OUT, $consent)
                ? $consent[Consents::OPT_OUT]
                : false;

            $this->Consents->set($consentName, $consentAccept, $optOut);
        }
        return $this;
    }

    /**
     * Check & add forceOpt parts to Contact
     * @param boolean $optInField - forceOptIn
     * @return mixed Contact || boolean
     * @throws SalesManagoException
     */
    private function setOptInStatuses($optInField = null)
    {
        try {
            if (!isset($this->Contact)) {
                return false;
            }

            $this->subscribingNow = ($optInField != null && $optInField != '')
                ? $optInField
                : false;

            $this->getSmContactOptInStatus();
            $this->checkAddUseApiDoubleOptIn();

            $optin = null;

            if (isset($this->gfConfig[self::FORCE_OPTIN])
                && $this->gfConfig[self::FORCE_OPTIN] == 'true'
            ) {
                $optin = true;
            } elseif ($this->smContactOptInStatus) {
                $optin = true;
            } else {
                $optin = $this->subscribingNow;
            }

            switch ($optin) {
                case true:
                    $this->setContactOptInState(true);
                    break;
                case false:
                    $this->setContactOptInState(false);
                    break;
                default:
                    $this->setContactOptInState(false);
                    break;
            }

            if ($this->useDoubleOptIn) {
                $this->Contact
                    ->getOptions()
                    ->setUseApiDoubleOptIn($this->useDoubleOptIn);
            }

            return $this->Contact;
        } catch (\Exception $e) {
            throw new SalesManagoException($e->getMessage());
        }
    }

    /**
     * Check if field is field is an SALESmanago field
     * @param string $field
     * @return boolean
    */
    private function isContactField($field)
    {
        return strpos($field, self::PREFIX_FIELD) !== false;
    }

    /**
     * Gets Contact OptIn status form SM & sets it to $this->smContactOptInStatus
     * @return mixed object $this
     * @throws SalesManagoException
    */
    public function getSmContactOptInStatus()
    {
        try {
            if (!isset($this->Contact) && $this->Contact->getEmail()) {
                $this->smContactOptInStatus = null;
            }
            $basic = $this->smController->getContactBasic($this->Contact->getEmail());
            $status = (isset($basic['contact']) && isset($basic['contact']['optedOut']))
                ? !$basic['contact']['optedOut']
                : false;

            $this->smContactOptInStatus = $status;

            return $this;
        } catch (\Exception $e) {
            throw new SalesManagoException($e->getMessage());
        }
    }

    /**
     * Set Synchronize flag if Synchronize enabled
     */
    private function setSynchronize()
    {
        if(!$this->settingsModel->getSynchronizeRule()){
            return false;
        }

        $this->synchronize = true;
    }

    /**
     * Set useApiDoubleOptIn configuration if useApiDoubleOptIn enabled
     */
    private function setUseDoubleOptIn()
    {
        if(!$conf = $this->settingsModel->getDoubleOptInConf()){
            return false;
        }

        $this->useDoubleOptIn = $conf;
    }

    /**
     * Sets $this->Contact opt states depends on param
     * @param boolean $state
     * @return boolean
    */
    private function setContactOptInState($state = false)
    {
        if (!isset($this->Contact)) {
            return false;
        }

        $this->Contact->getOptions()
            ->setForceOptIn($state)
            ->setForceOptOut(!$state)
            ->setForcePhoneOptIn($state)
            ->setForcePhoneOptOut(!$state);

        return true;
    }

    /**
     * Check if $this->useApiDoubleOptIn, next check SM optin status,
     * sets useApiDoubleOptIn if smOptInStatus == false.
    */
    private function checkAddUseApiDoubleOptIn()
    {
        if (!$this->useDoubleOptIn || $this->smContactOptInStatus == true) {
            return false;
        }

        if (!$this->subscribingNow) {
            return false;
        }

        $this->Contact->getOptions()
            ->setUseApiDoubleOptIn(
                [
                  'useApiDoubleOptIn'               => $this->useDoubleOptIn['double'],
                  'apiDoubleOptInEmailTemplateId'   => $this->useDoubleOptIn['template'],
                  'apiDoubleOptInEmailAccountId'    => $this->useDoubleOptIn['email'],
                  'apiDoubleOptInEmailSubject'      => $this->useDoubleOptIn['topic'],
                ]
            );

        return true;
    }
}
