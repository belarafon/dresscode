<?php

namespace bhr\Helper\Contact;

use bhr\Helper\Functions;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Consents
{
    /**
     * @var array
     */
    protected $consents = [];

    const
        CONSENT_DETAILS = 'consentDetails',

        CONSENT_NAME    = 'consentName',
        CONSENT_ACCEPT  = 'consentAccept',
        AGREEMENT_DATE  = 'agreementDate',
        CONSENT_DESCRIPTION_ID = 'consentDescriptionId',

        C_IP            = 'ip',
        OPT_OUT         = 'optOut';

    /**
     * @param string $consentName
     * @param bool $consentAccept
     * @param $agreementDate - 13 characters microtime();
     * @param $ip
     * @param bool $optOut
     * @param null $consentDescriptionId
     * @return $this;
     */
    public function set(
        $consentName,
        $consentAccept = false,
        $optOut = false,
        $agreementDate = null,
        $ip = null,
        $consentDescriptionId = null
    ) {
        $agreementDate = ($agreementDate == null)
            ? intval(round(microtime(true) * 1000))
            : $agreementDate;

        $agreementDate = (strlen($agreementDate) < 13) ? $agreementDate . '000' : $agreementDate;

        $ip = ($ip == null)
            ? Functions::getUserIP()
            : $ip;

        if (!$consentAccept) {
            return $this;
        }

        $this->consents[] = Functions::filterData(
            [
                self::CONSENT_NAME => $consentName,
                self::CONSENT_ACCEPT => boolval($consentAccept),
                self::AGREEMENT_DATE => $agreementDate,
                self::C_IP => $ip,
                self::OPT_OUT => boolval($optOut),
                self::CONSENT_DESCRIPTION_ID => $consentDescriptionId
            ]
        );

        return $this;
    }

    /**
     * @return array
     */
    protected function get()
    {
        return $this->consents;
    }

    /**
     * @return array for sm api
     */
    public function getConsentDetailsArr()
    {
        return [self::CONSENT_DETAILS => $this->get()];
    }
}
