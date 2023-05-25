<?php

namespace Bnm\Importer\DataAccess {
    use \PDO;
    use \PDOException;
    use \Bnm\Importer\Config\DbConfig;

    class Connection
    {
        public function __construct()
        {
            $this->_pdo = $this->initConnection();
        }

        private PDO $_pdo;

        /**
         * Get a connection to the destination database.
         */
        private function initConnection() : PDO
        {
            $dbConfig = new DbConfig();
            $dsn = "mysql:host=" . $dbConfig->host . ";dbname=" . $dbConfig->name . ";charset=" . $dbConfig->charset;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                $pdo = new PDO($dsn, $dbConfig->user, $dbConfig->pass, $options);
            } catch (PDOException $e) {
                throw new PDOException($e->getMessage(), (int)$e->getCode());
            }

            return $pdo;
        }

        public function beginTransaction()
        {
            $this->_pdo->beginTransaction();
        }

        public function commit()
        {
            $this->_pdo->commit();
        }
        
        public function rollback()
        {
            $this->_pdo->rollback();
        }

        public function executePreparedStatement(string $sql, array $params = null)
        {
            $stmt = $this->_pdo->prepare($sql);

            if ($params != null)
            {
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
            }
            
            return $stmt->execute();
        }

        public function fetchAll(string $sql, array $params = null)
        {
            $stmt = $this->_pdo->prepare($sql);

            if ($params != null)
            {
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
            }
            
            $stmt->execute();

            return $stmt->fetchAll();
        }

        /**
         * Return the first 2 columns in the resulting data as an array using the 
         * the first column as the key and the second column as the value. Column
         * names are ignored. Subsequent duplicate values will override previously
         * set values.
         * 
         * @param string $sql The sql statement to prepare and execute.
         * @param array $params If supplied the values in this array will be bound to the sql statement.
         */
        public function fetchLookup(string $sql, array $params = null)
        {
            $stmt = $this->_pdo->prepare($sql);

            if ($params != null)
            {
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
            }
            
            $stmt->execute();
            $data = $stmt->fetchAll(\PDO::FETCH_NUM);

            $lookup_data = array();
            array_walk(
                $data,
                function ($value) use (&$lookup_data) {
                    $lookup_data += [$value[0] => $value[1]];
                }
            );

            return $lookup_data;
        }
    }
}