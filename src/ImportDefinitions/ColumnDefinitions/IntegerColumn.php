<?php

namespace Bnm\Importer\ImportDefinitions\ColumnDefinitions {

    class IntegerColumn extends ColumnDefinition
    {
        public function __construct($import_definition, $name, $input_column, $validation = [])
        {
            parent::__construct(
                $import_definition,
                $name,
                $input_column,
                array_unique(array_merge(['Int'], $validation)),
                \PDO::PARAM_INT,
                ColumnValueType::Int
            );
        }
    }
}
