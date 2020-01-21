<?php

namespace SALESmanago\Exception;


class UserAccessException extends \Exception
{
    public function getUserMessage()
    {
        $error = array(
            'success' => false,
            'message' => htmlspecialchars($this->getMessage())
        );

        return $error;
    }
}
