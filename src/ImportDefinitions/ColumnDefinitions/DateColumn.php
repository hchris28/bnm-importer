<?php

namespace Bnm\Importer\ImportDefinitions\ColumnDefinitions {

    class DateColumn extends ColumnDefinition
    {
        public function __construct($import_definition, $name, $input_column, $validation = [])
        {
            parent::__construct(
                $import_definition,
                $name, 
                $input_column,
                array_unique(array_merge(['Date'], $validation)), 
                \PDO::PARAM_STR, 
                ColumnValueType::Date
            );
        }

        public function format_sql_param_value($row) {
            $input = $row[$this->input_column['ordinal']];
            $date = date_parse($input);

            return 
                $date['year'] . 
                "-" . str_pad($date['month'], 2, "0", STR_PAD_LEFT) . 
                "-" . str_pad($date['day'], 2, "0", STR_PAD_LEFT);
        }
    }
}