<?php

namespace plugins\UkrPoshta\classes\base;

use plugins\UkrPoshta\classes\Log;
use wpdb;

/**
 * Class DatabaseSync
 * @package plugins\UkrPoshta\classes\base
 * @property wpdb db
 * @property int updatedAt
 * @property Log log
 * @property array areas
 */
abstract class DatabaseSync extends Base
{

    /**
     * @return array
     */


    /**
     * @return Log
     */
    protected function getLog()
    {
        return UP()->log;
    }

    /**
     * @return wpdb
     */
    protected function getDb()
    {
        return UP()->db;
    }

    /**
     * @return int
     */
    protected function getUpdatedAt()
    {
        return time();
    }

}
