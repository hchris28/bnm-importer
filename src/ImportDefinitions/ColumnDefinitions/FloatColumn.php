<?php

namespace Bnm\Importer\ImportDefinitions\ColumnDefinitions {

    class FloatColumn extends ColumnDefinition
    {
        public function __construct($import_definition, $name, $input_column, $validation = [])
        {
            parent::__construct(
                $import_definition,
                $name, 
                $input_column,
                array_unique(array_merge(['Float'], $validation)), 
                \PDO::PARAM_STR, 
                ColumnValueType::Float
            );
        }
    }
}