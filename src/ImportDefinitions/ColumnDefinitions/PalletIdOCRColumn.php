<?php

namespace Bnm\Importer\ImportDefinitions\ColumnDefinitions {

    class PalletIdOCRColumn extends ColumnDefinition
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

        public function format_sql_param_value($row)
        {
            $input = $row[$this->input_column['ordinal']];

            // split_input the input value on whitespaces
            $split_input = preg_split('/\s+/', $input);

            // remove non-numeric characters from each entry
            $pallet_ids = array_map(
                fn ($value) => preg_replace("/[^0-9]/", "", $value),
                $split_input
            );

            return implode(',', $pallet_ids);
        }
    }
}