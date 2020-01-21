<?php

namespace SALESmanago\Services;

use SALESmanago\Exception\SalesManagoException;


trait EventTypeTrait
{
    protected $eventType = array(
        "CART",
        "PURCHASE",
        "VISIT",
        "PHONE_CALL",
        "OTHER",
        "RESERVATION",
        "CANCELLED",
        "ACTIVATION",
        "MEETING",
        "OFFER",
        "DOWNLOAD",
        "LOGIN",
        "TRANSACTION",
        "CANCELLATION",
        "RETURN"
    );

    public function checkAccessEventType($name)
    {
        $name = strtoupper($name);
        if (!in_array($name, $this->eventType)) {
            return new SalesManagoException('cannot find external event', 13);
        }
        return $name;
    }
}
