<?php

namespace plugins\UkrPoshta\classes\base;

/**
 * Class Base
 * @package plugins\UkrPoshta\classes\base
 */
class Base
{

    /**
     * @return string
     */
    public static function getClass()
    {
        return get_called_class();
    }

    /**
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        $method = 'get' . ucwords($property);
        $this->$property = $this->$method();
        return $this->$property;
    }
}
