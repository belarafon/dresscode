<?php

namespace SALESmanago\Services;

use SALESmanago\Entity\Settings;
use SALESmanago\Exception\SalesManagoException;


class ConnectSalesManagoService extends AbstractClient implements ApiMethodInterface
{
    const EVENT_TYPE_CART = "CART",
        EVENT_TYPE_PURCHASE = "PURCHASE",
        EVENT_TYPE_CANCELLATION = "CANCELLATION",
        EVENT_TYPE_RETURN = "RETURN";

    use EventTypeTrait;

    private $contactBasic;

    public function __construct(Settings $settings)
    {
        $this->setClient($settings);
    }

    /**
     * @param Settings $settings
     * @param string $userEmail
     * @return array
     * @throws SalesManagoException
     */
    public function getContactByEmail(Settings $settings, $userEmail)
    {
        $data = array_merge(
            $this->__getDefaultApiData($settings),
            array(
                'email' => array($userEmail),
            )
        );

        $response = $this->request(self::METHOD_POST, self::METHOD_STATUS, $data);

        if (is_array($response)
            && array_key_exists('success', $response)
            && count($response['contacts']) === 1
        ) {
            $user = array_pop($response['contacts']);
            return array(
                "success" => true,
                "contactId" => $user['contactId'],
            );
        } else {
            return $this->contactUpsert(
                $settings,
                array(
                    "email" => $userEmail,
                ),
                array(
                    "forceOptIn" => false,
                    "forceOptOut" => true,
                )
            );
        }
    }

    /**
     * @param Settings $settings
     * @param string $userEmail
     * @return array $contactBasic
     */
    public function getContactBasicByEmail(Settings $settings, $userEmail)
    {
        $data = array_merge(
            $this->__getDefaultApiData($settings),
            array(
                'email' => array($userEmail),
            )
        );

        $response = $this->request(self::METHOD_POST, self::METHOD_STATUS, $data);

        if (is_array($response)
            && array_key_exists('success', $response)
            && count($response['contacts']) === 1
        ) {
            $user = array_pop($response['contacts']);
            $this->contactBasic = array(
                "success" => true,
                "contact" => $user,
            );
            return $this->contactBasic;
        } else {
            $this->contactBasic = array(
                "success" => false,
            );
            return $this->contactBasic;
        }
    }

    /**
     * @param Settings $settings
     * @param string $contactId
     * @return array
     * @throws SalesManagoException
     */
    public function getContactById(Settings $settings, $contactId)
    {
        $data = array_merge($this->__getDefaultApiData($settings), array(
            'id' => array($contactId),
        ));

        $response = $this->request(self::METHOD_POST, self::METHOD_STATUS_BY_ID, $data);
        return $this->validateResponse($response);
    }

    public function checkForceOptions($options)
    {
        if (isset($options['force']) && $options['force'] == true) {
            return false;
        }

        return !(isset($options['forceOptIn']) && $options['forceOptIn'] == true) ||
            (isset($options['forceOptOut']) && $options['forceOptOut'] == true);
    }

    /**
     * @param string $email
     * @param array $options
     * @param Settings $settings
     */
    public function synchronizeFromSales(Settings $settings, $email, &$options)
    {
        $options['synchronizeFromSales'] = false;

        if (isset($options['synchronizeRule'])
            && $options['synchronizeRule']
            && !$options['forceOptIn']
        ) {
            if (!isset($this->contactBasic)) {
                $this->getContactBasicByEmail($settings, $email);
            }

            if (is_array($this->contactBasic)
                && array_key_exists('success', $this->contactBasic)
                && isset($this->contactBasic['contact'])
                && !(strtotime($options['createdOn']) <= time() - 900)
            ) {
                if ($this->contactBasic['contact']['optedOut'] == false) {
                    $options['forceOptIn'] = true;
                    $options['forceOptOut'] = false;
                    $options['forcePhoneOptIn'] = true;
                    $options['forcePhoneOptOut'] = false;
                    $options['synchronizeFromSales'] = true;
                }
            }
        }
    }

    /**
     * use before method synchronizeFromSales
     * @param Settings $settings
     * @param string $email
     * @param array $options
     * @return boolean
     */
    public function checkApiDoubleOptIn(Settings $settings, $email, &$options)
    {
        if (isset($options['synchronizeFromSales'])
            && $options['synchronizeFromSales']
        ) {
            if (!isset($options['useApiDoubleOptIn'])) {
                return true;
            }

            if (!$options['useApiDoubleOptIn']) {
                return true;
            }

            if (!isset($this->contactBasic)) {
                $this->getContactBasicByEmail($settings, $email);
            }

            if ($this->contactBasic['success']) {
                $contact = $this->contactBasic['contact'];
            } else {
                return true;
            }

            if ($contact['optedOut'] == false
                && $options['forceOptIn'] == true
                && $options['forceOptOut'] == false
            ) {
                if (isset($options['useApiDoubleOptIn'])) {
                    unset($options['useApiDoubleOptIn']);
                }

                if (isset($options['apiDoubleOptInEmailTemplateId'])) {
                    unset($options['apiDoubleOptInEmailTemplateId']);
                }

                if (isset($options['apiDoubleOptInEmailAccountId'])) {
                    unset($options['apiDoubleOptInEmailAccountId']);
                }

                if (isset($options['apiDoubleOptInEmailSubject'])) {
                    unset($options['apiDoubleOptInEmailSubject']);
                }
            }
        }

        if (
            isset($options['useApiDoubleOptIn'])
            && $options['useApiDoubleOptIn']
        ) {
            $options['forceOptIn'] = (isset($contact) && isset($contact['optedOut'])) ? !$contact['optedOut'] : false;
            $options['forceOptOut'] = (isset($contact) && isset($contact['optedOut'])) ? $contact['optedOut'] : true;

            if (isset($options['forcePhoneOptIn'])
                && $options['forcePhoneOptIn']
            ) {
                $options['forcePhoneOptIn'] = false;
                $options['forcePhoneOptOut'] = true;
            }
        }
    }

    /**
     * @param Settings $settings
     * @param array $user
     * @param array $options
     * @param array $properties
     * @return array
     * @throws SalesManagoException
     */
    public function contactUpsert(Settings $settings, $user, $options = array(), $properties = array())
    {
        $data = $this->__getDefaultApiData($settings);


        $this->getContactBasicByEmail($settings, $user['email']);
        $this->checkApiDoubleOptIn($settings, $user['email'], $options);
        $this->synchronizeFromSales($settings, $user['email'], $options);

        $data = array_merge($data, array('contact' => $this->__getContactData($user)));

        $tag = array(
            'tags' => array(),
            'removeTags' => array(),
        );

        if ($settings->count($settings->getTags()) > 0) {
            $tag['tags'] = $settings->getTags();
        }

        if ($settings->count($settings->getRemoveTags()) > 0) {
            $tag['removeTags'] = $settings->getRemoveTags();
        }

        if (!empty($options)) {
            foreach ($options as $key => $value) {
                if ($key == 'tags') {
                    $tag['tags'] = array_merge($tag['tags'], $this->__prepareTags($value));
                } elseif ($key == 'removeTags') {
                    $tag['removeTags'] = array_merge($tag['removeTags'], $this->__prepareTags($value));
                } elseif ($key == 'force') {
                    continue;
                } else {
                    $data[$key] = $value;
                }
            }
        }

        if (!empty($tag['tags'])) {
            $data['tags'] = $tag['tags'];
        }

        if (!empty($tag['removeTags'])) {
            $data['removeTags'] = $tag['removeTags'];
        }

        if (!empty($properties)) {
            foreach ($properties as $key => $value) {
                $data['properties'][$key] = $value;
            }
        }

        unset($data['createdOn'], $data['synchronizeRule'], $data['synchronizeFromSales']);
        $response = $this->request(self::METHOD_POST, self::METHOD_UPSERT, $this->filterData($data));
        $response['synchronizeFromSales'] = isset($options['synchronizeFromSales'])
            ? $options['synchronizeFromSales']
            : false;
        return $this->validateCustomResponse($response, array(array_key_exists('contactId', $response)));
    }

    /**
     * @param Settings $settings
     * @param array $user
     * @param array $options
     * @return array
     * @throws SalesManagoException
     */
    public function contactSubscriber(Settings $settings, $user, $options)
    {
        if (isset($options['tags'])) {
            $options['tags'] = $this->__prepareTags($options['tags']);
        }

        if (isset($options['removeTags'])) {
            $options['removeTags'] = $this->__prepareTags($options['removeTags']);
        }

        $data = array_merge(
            $this->__getDefaultApiData($settings),
            array('contact' => $this->__getContactData($user)),
            $options
        );

        $response = $this->request(self::METHOD_POST, self::METHOD_UPSERT, $this->filterData($data));
        return $this->validateCustomResponse($response, array(array_key_exists('contactId', $response)));
    }

    /**
     * @param Settings $settings
     * @param string $userEmail
     * @return array
     * @throws SalesManagoException
     */
    public function contactDelete(Settings $settings, $userEmail = '')
    {
        $data = array_merge($this->__getDefaultApiData($settings), array(
            'email' => $userEmail,
        ));

        $response = $this->request(self::METHOD_POST, self::METHOD_DELETE, $data);
        return $this->validateResponse($response);
    }

    /**
     * @param Settings $settings
     * @param array $user
     * @return array
     * @throws SalesManagoException
     */
    public function contactAddNote(Settings $settings, $user)
    {
        $data = array_merge($this->__getDefaultApiData($settings), array(
            'priv' => $user['priv'],
            'note' => $user['note'],
        ));

        if (!empty($user['email'])) {
            $data['email'] = $user['email'];
        } elseif (!empty($user['contactId'])) {
            $data['contactId'] = $user['contactId'];
        }

        $response = $this->request(self::METHOD_POST, self::METHOD_ADD_NOTE, $data);
        return $this->validateResponse($response);
    }

    /**
     * @param Settings $settings
     * @param string $type
     * @param array $product
     * @param array $user
     * @param string $eventId
     * @return array
     * @throws SalesManagoException
     */
    public function contactExtEvent(Settings $settings, $type, $product, $user, $eventId)
    {
        $data = array_merge($this->__getDefaultApiData($settings), array(
            'contactEvent' => $this->__getContactEventData($product, $type, $eventId),
        ));

        if (!empty($user['email'])) {
            $data['email'] = $user['email'];
        } else {
            $data['contactId'] = $user['contactId'];
        }

        if (!empty($eventId)) {
            $method = self::METHOD_UPDATE_EXT_EVENT_V2;
        } else {
            $method = self::METHOD_ADD_EXT_EVENT_V2;
        }

        $response = $this->request(self::METHOD_POST, $method, $this->filterData($data));
        return $this->validateCustomResponse($response, array(array_key_exists('eventId', $response)));
    }

    protected function __getContactData($user)
    {
        if (!empty($user)) {
            $contact = array();
            foreach ($user as $key => $value) {
                if (in_array($key, array("streetAddress", "zipCode", "city", "country"))) {
                    $contact["address"][$key] = $value;
                } else {
                    $contact[$key] = $value;
                }
            }
            return $contact;
        }
    }

    protected function __getContactEventData($product, $type, $eventId)
    {
        $contactEvent = array(
            'date' => 1000 * time(),
            'contactExtEventType' => $type,
        );

        $contactEvent = array_merge(
            $contactEvent,
            $product
        );

        if (!empty($eventId)) {
            $contactEvent['eventId'] = $eventId;
        }

        return $contactEvent;
    }

    protected function __prepareTags($tags)
    {
        $tags = explode(',', $tags);
        foreach ($tags as $key => $tag) {
            $tags[$key] = strtoupper(trim($tag));
        }
        return $tags;
    }
}
