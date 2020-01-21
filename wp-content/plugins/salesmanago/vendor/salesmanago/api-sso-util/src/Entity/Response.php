<?php

namespace SALESmanago\Entity;


class Response
{
    const SUCCESS = 'success';

    protected $response = array();

    protected function array_push_assoc($key, $value)
    {
        $this->response[$key] = $value;
    }

    public function addStatus($status)
    {
        $this->array_push_assoc(self::SUCCESS, $status);
        return $this;
    }

    public function addField($key, $value)
    {
        $this->array_push_assoc($key, $value);
        return $this;
    }

    public function addArray($arr)
    {
        $this->response = array_merge($this->response, $arr);
        return $this;
    }

    public function build()
    {
        return $this->response;
    }
}
