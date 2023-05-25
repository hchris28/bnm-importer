<?php

namespace Bnm\Importer\DataAccess {

    use \Bnm\Importer\DataAccess\Connection;

    class Query
    {
        public static function fetchAll($sql, $params = null) : array
        {
            $db_conn = new Connection();
            
            return $db_conn->fetchAll($sql, $params);
        }

        public static function fetchLookup($sql, $params = null) : array
        {
            $db_conn = new Connection();
            
            return $db_conn->fetchLookup($sql, $params);
        }
    }
}
