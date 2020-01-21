<?php

namespace SALESmanago\Exception;

use Throwable;


class AccountActiveException extends \Exception
{
    protected $redirect;

    public function __construct($message = "", $code = 0, $redirect = "", Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->redirect = $redirect;
    }

    /**
     * @return string
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    public function getExceptionMessage()
    {
        $error = array(
            'success' => false,
            'code'    => $this->getCode(),
            'redirect' => $this->getRedirect(),
            'message' => htmlspecialchars($this->getMessage())
        );

        return $error;
    }
}
