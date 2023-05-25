<?php

namespace Bnm\Importer\ImportDefinitions\ColumnDefinitions {

    class BooleanColumn extends ColumnDefinition
    {
        public function __construct($import_definition, $name, $input_column, $validation = [])
        {
            parent::__construct(
                $import_definition,
                $name, 
                $input_column,
                array_unique(array_merge(['Bool'], $validation)), 
                \PDO::PARAM_BOOL, 
                ColumnValueType::Boolean
            );
        }
    }
}