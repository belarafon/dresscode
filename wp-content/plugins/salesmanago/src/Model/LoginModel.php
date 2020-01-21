<?php

namespace bhr\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use SALESmanago\Model\LoginInterface;

class LoginModel extends AbstractModel implements LoginInterface
{
	use StoreTrait;

    public function insert($settings)
    {
        $value = $this
            ->prepareValue($settings, true)
            ->addExpiresTokeTime()
            ->buildValue();

        $this->db->query($this->db->prepare("INSERT INTO {$this->db->options} (option_id, option_name, option_value) VALUES (NULL, %s, %s)", array(self::SETTINGS, $value)));
	    $this->setStoreId();
    }

    public function checkUser($settings)
    {
        $stmt = $this->db->get_row($this->db->prepare("SELECT option_id as id FROM {$this->db->options} WHERE option_name = %s LIMIT 1", self::SETTINGS), ARRAY_A);

        return isset($stmt['id']) ? $stmt['id'] : $stmt;
    }

    public function update($id, $settings)
    {
        $value = $this
            ->prepareValue($settings, true)
            ->addExpiresTokeTime()
            ->buildValue();

        $this->db->query($this->db->prepare("UPDATE {$this->db->options} SET option_value = %s WHERE option_name = %s and option_id = %d", array($value, self::SETTINGS, $id)));
    }

    public function updateProperties($settings, $properties)
    {
        $value = $this
            ->getValue()
            ->addUpdatedAtTime()
            ->extendValue(array('properties' => $properties))
            ->buildValue();

        $this->setUserData($value);
    }
}
