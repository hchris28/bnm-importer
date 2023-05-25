<?php

namespace Bnm\Importer\ImportDefinitions\ColumnDefinitions {

    class TextColumn extends ColumnDefinition
    {
        public function __construct($import_definition, $name, $input_column, $validation = [])
        {
            parent::__construct(
                $import_definition,
                $name,
                $input_column,
                $validation,
                \PDO::PARAM_STR,
                ColumnValueType::String
            );
        }
    }
}
