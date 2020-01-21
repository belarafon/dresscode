<?php

namespace bhr\Modules\Cf7;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bhr\Model\AbstractModel;
use SALESmanago\Exception\UserAccessException;
use SALESmanago\Model\SettingsInterface;

class Model extends AbstractModel implements SettingsInterface
{
    const
        EXT_NAME = 'cf7',
        WP_PLUGIN = 'contact-form-7/wp-contact-form-7.php';


    public static function getDefaultConfig()
    {
        return [
            self::EXT_NAME => [
                'options'      => [],
                'properties'   => [],
                'confirmation' => [
                    'double'   => '',
                    'template' => '',
                    'email'    => '',
                    'topic'    => '',
                ],
                'forms'        => [],
                'form'         => [],
                'users'        => [],
                'forceOptIn'   => ''
            ]
        ];
    }

    public static function isFeederActive()
    {
        $active = in_array(
            self::WP_PLUGIN,
            apply_filters(
                'active_plugins',
                get_option('active_plugins')
            )
        );

        if (get_site_option('active_sitewide_plugins')) {
            $active = (!$active)
                ? array_key_exists(
                    self::WP_PLUGIN,
                    get_site_option('active_sitewide_plugins')
                )
                : $active;
        }

        return $active;
    }

    public function getConfig()
    {
        $userData = $this->getUserData();
        $userData['extensions'][self::EXT_NAME]['forms'] = $this->getCf7FormsData();
        $config = $userData['extensions'][self::EXT_NAME];

        ($config['forceOptIn'] == "false" || empty($config['forceOptIn']))
            ? $config['forceOptIn'] = false
            : $config['forceOptIn'] = true;
        ($config['confirmation']['double'] == "false" || empty($config['confirmation']['double']))
            ? $config['confirmation']['double'] = false
            : $config['confirmation']['double'] = true;

        return $config;
    }

    public function setConfig($extProperties)
    {
        if (!isset($extProperties['form'])) {
            $extProperties['form'] = array();
        }

        $value = $this
            ->getValue()
            ->addUpdatedAtTime()
            ->updateExtension(self::EXT_NAME, $extProperties)
            ->buildValue();

        $status = $this->setUserData($value);

        return $status;
    }

    private function updateExtension($key, $value)
    {
        $this->userValue['extensions'][$key] = $value;

        return $this;
    }

    /**
     * @param $userProperties
     * @throws UserAccessException
     * @return mixed
     */
    public function getUserSettings($userProperties)
    {
        $stmt = $this->db->get_row($this->db->prepare("SELECT option_value FROM {$this->db->options} WHERE option_name = %s LIMIT 1", self::SETTINGS), ARRAY_A);
        if ($stmt == null) {
            throw new UserAccessException('User not exist');
        }

        return $this->decodeValue($stmt['option_value']);
    }

    private function getCf7FormsData()
    {
        $args = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);
        $formsData = array();
        if ($data = get_posts($args)) {
            foreach ($data as $key) {
                $formsData[] = ["id" => $key->ID, 'title' => $key->post_title];
            }
        } else {
            $formsData[] = esc_html__('No Contact Form found', 'text-domanin');
        }

        return $formsData;
    }
}
