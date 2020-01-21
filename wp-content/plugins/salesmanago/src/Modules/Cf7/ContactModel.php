<?php

namespace bhr\Modules\Cf7;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

use bhr\Helper\Contact\Address;
use bhr\Helper\Contact\Contact;
use bhr\Helper\Contact\Options;

use bhr\Model\ContactModelInterface;
use bhr\Modules\Gf\ModuleConfigurationInterface;
use SALESmanago\Exception\SalesManagoException;

class ContactModel implements ContactModelInterface
{
    const
        FORCE_OPTIN = 'forceOptIn';

    private $Contact;
    private $Address;
    private $Options;

    private $smController;
    private $settingsModel;
    private $synchronize;
    private $useDoubleOptIn;
    private $currentFormConf;

    private $smContactOptInStatus = null;
    private $subscribingNow = false;

    private $formData;
    private $config;

    public function __construct($smController, ModuleConfigurationInterface $model)
    {
        $this->Contact = new Contact();
        $this->Address = new Address();
        $this->Options = new Options();
        $this->smController = $smController;

        $this->settingsModel = $model;

        $this->setSynchronize();
        $this->setUseDoubleOptIn();
    }

    public function setParameters($array)
    {
        $this->formData = $array['formData'];
        $this->currentFormConf = $array['currentFormConf'];
        $this->config = $array['config'];

        return $this;
    }

    /**
     * @return Contact $Contact
     * @throws SalesManagoException
    */
    public function get()
    {
        $this->parseContact();
        return $this->Contact;
    }

    /**
     * @return mixed Contact $Contact || boolean
     * @throws SalesManagoException
     */
    private function parseContact()
    {
        try {
            if (!isset($this->formData['sm-email'])
                || empty($this->formData['sm-email'])
            ) {
                return false;
            }

            if (!$this->currentFormConf) {
                return false;
            }

            $this->Contact->setEmail($this->formData['sm-email'])
                ->setName(isset($this->formData['sm-name']) ? $this->formData['sm-name'] : '')
                ->setPhone(isset($this->formData['sm-phone']) ? $this->formData['sm-phone'] : '')
                ->setFax(isset($this->formData['sm-fax']) ? $this->formData['sm-fax'] : '')
                ->setCompany(isset($this->formData['sm-company']) ? $this->formData['sm-company'] : '');

            $this->Address->setCity(isset($this->formData['sm-city']) ? $this->formData['sm-city'] : '')
                ->setCountry(isset($this->formData['sm-country']) ? $this->formData['sm-country'] : '')
                ->setStreetAddress(isset($this->formData['sm-address']) ? $this->formData['sm-address'] : '')
                ->setZipCode(isset($this->formData['sm-postcode']) ? $this->formData['sm-postcode'] : '');

            $this->Contact->setAddress($this->Address);

            $this->Options->setProperties($this->getProperties());

            $this->Options
                ->setTags(
                    isset($this->currentFormConf['tags'])
                        ? $this->currentFormConf['tags']
                        : ''
                )->setRemoveTags(
                    isset($this->currentFormConf['removeTags'])
                        ? $this->currentFormConf['removeTags']
                        : '');

            $this->Contact->setOptions($this->Options);

            $this->setOptInStatuses();

            return $this->Contact;
        } catch (\Exception $e) {
            throw new SalesManagoException($e->getMessage());
        }
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
     * Check & add forceOpt parts to Contact
     * @return mixed Contact || boolean
     * @throws SalesManagoException
     */
    private function setOptInStatuses()
    {
        try {
            if (!isset($this->Contact)) {
                return false;
            }

            if (
                isset($this->formData['sm-optin'])
                && is_array($this->formData['sm-optin'])
                && implode($this->formData['sm-optin']) != ''
            ) {
                $this->subscribingNow = true;
            } else {
                $this->subscribingNow = false;
            }

            $this->getSmContactOptInStatus();
            $this->checkAddUseApiDoubleOptIn();

            $optin = null;

            if (isset($this->config['forceOptIn'])
                && $this->config['forceOptIn'] == "true"
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
                $this->Contact->getOptions()
                    ->setUseApiDoubleOptIn($this->useDoubleOptIn);
            }

            return $this->Contact;
        } catch (\Exception $e) {
            throw new SalesManagoException($e->getMessage());
        }
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

    private function getProperties()
    {
        return array_merge(
            $this->prepareDetailMap(),
            $this->prepareRadioMap()
        );
    }

    private function prepareDetailMap()
    {
        $properties = [];
        foreach ($this->config['properties'] as $key => $propertyName) {
            $customInput = (isset($this->formData[$key]) ? $this->formData[$key] : '');
            $customInput = (strlen($customInput) > 255) ? substr($customInput, 0, 255) : $customInput;

            if ($propertyName != '' && $customInput != '') {
                $properties[$propertyName] = $customInput;
            }
        }
        return $properties;
    }

    private function prepareRadioMap()
    {
        $properties = [];
        foreach ($this->config['options'] as $key => $optionName) {
            $customInput = (isset($this->formData[$key]) ? $this->formData[$key] : '');

            $customInput = is_array($customInput) ? implode(' ', $customInput) : $customInput;
            $customInput = (strlen($customInput) > 255) ? substr($customInput, 0, 255) : $customInput;

            if ($optionName != '' && $customInput != '') {
                $properties[$optionName] = is_array($customInput) ? implode(' ', $customInput) : $customInput;
            }
        }
        return $properties;
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
        if (!$conf = $this->settingsModel->getDoubleOptInConf()) {
            return false;
        }

        $this->useDoubleOptIn = $conf;
    }

}
