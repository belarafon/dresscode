<?php

namespace bhr\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use SALESmanago\Model\CreateInterface;

class CreateModel extends AbstractModel implements CreateInterface
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
}
