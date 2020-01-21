<?php

namespace bhr\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class HooksFiltersManager
{
    private static $enabledHooks = true;
    private static $enabledFilters = true;

    public static function disableHooks()
    {
        self::$enabledHooks = false;
    }

    public static function disableFilters()
    {
        self::$enabledFilters = false;
    }

    public static function doAction($tag, $args = '')
    {
        return (self::$enabledHooks)
            ? do_action($tag, $args)
            : null;
    }

    public static function applyFilters($tag, $args)
    {
        return (self::$enabledFilters)
            ? apply_filters($tag, $args)
            : $args;
    }
}
