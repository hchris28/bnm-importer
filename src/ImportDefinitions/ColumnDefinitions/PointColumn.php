<?php

namespace Bnm\Importer\ImportDefinitions\ColumnDefinitions {

    class PointColumn extends ColumnDefinition {
        public function __construct($import_definition, $name, $input_column, $validation = [])
        {
            parent::__construct(
                $import_definition,
                $name,
                $input_column,
                array_unique(array_merge(['Point'], $validation)),
                \PDO::PARAM_STR,
                ColumnValueType::Point
            );
        }

        public function format_sql_param_value($row) {
            $input = $row[$this->input_column['ordinal']];

            // input is expected to be a comma separated coordinate pair,
            // the following regex explodes and trims.
            list($lat, $lng) = preg_split('/(\s*,*\s*)*,+(\s*,*\s*)*/', $input);

            return "POINT({$lat} {$lng})";
        }
    }
}