<?php
namespace plugins\UkrPoshta\classes\base;

use plugins\UkrPoshta\classes\Area;

/**
 * Class OptionsHelper
 * @package plugins\UkrPoshta\classes\base
 */
class OptionsHelper
{
    /**
     * @param Area[] $locations
     * @param bool $enableEmpty
     * @return array
     */
    public static function getList($locations, $enableEmpty = true)
    {
        $result = array();
        if ($enableEmpty) {
            $result[''] = __('Choose an option', U_POSHTA_DOMAIN);
        }
        foreach ($locations as $location) {
            $result[$location->ref] = $location->description;
        }
        return $result;
    }
}
