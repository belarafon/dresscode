<?php

namespace bhr\Modules\Newsletter\States;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

interface State
{
    public function setConfig($params);
    public function getConfig();

    public function setFront();
    public function setContactOptStates();

    public function getSubscriberStateFromRequest();
}
