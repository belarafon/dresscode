<?php

namespace SALESmanago\Exception;

use Throwable;


class SalesManagoException extends \Exception
{
    protected $status;

    public function __construct($message = "", $code = 0, $status = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function getSalesManagoMessage()
    {
        $error = array(
            'success' => false,
            'code'    => $this->getCode(),
            'status'  => $this->getStatus(),
            'message' => htmlspecialchars($this->getMessage())
        );

        return $error;
    }
}
