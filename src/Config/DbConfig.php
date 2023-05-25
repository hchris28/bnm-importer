<?php

namespace Bnm\Importer\Config {

    class DbConfig
    {
        public function __construct()
        {
            $this->env = parse_ini_file(__DIR__ . '.env');
            $this->host = $this->env["DB_HOST"];
            $this->name = $this->env["DB_NAME"];
            $this->user = $this->env["DB_USER"];
            $this->pass = $this->env["DB_PASS"];
            $this->charset = 'utf8mb4';
        }

        private array $env;

        public $host;
        public $name;
        public $user;
        public $pass;
        public $charset;
    }
}