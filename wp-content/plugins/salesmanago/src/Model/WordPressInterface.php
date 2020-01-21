<?php

namespace bhr\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use SALESmanago\Model\IntegrationInterface;

interface WordPressInterface extends IntegrationInterface
{
    public function getExtensions();

    public function setExtensions($userProperties);
}
