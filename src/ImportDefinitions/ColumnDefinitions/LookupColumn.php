<?php

namespace Bnm\Importer\ImportDefinitions\ColumnDefinitions {

    class LookupColumn extends ColumnDefinition
    {
        public function __construct($import_definition, $name, $input_column, $validation = [])
        {
            parent::__construct(
                $import_definition,
                $name,
                $input_column,
                $validation,
                \PDO::PARAM_INT,
                ColumnValueType::Int
            );
        }

        public function format_sql_param_value($row)
        {
            $input = $row[$this->input_column['ordinal']];

            $lookup_data = $this->import_definition->getLookupValues($this->name);

            if (!in_array($input, $lookup_data)) {
                throw new \Exception("Invalid lookup value [{$input}] for column {$this->name}.");
            }

            return array_search($input, $lookup_data);
        }

        public function validate($row) : array
        {
            $input = $row[$this->input_column['ordinal']];

            $lookup_data = $this->import_definition->getLookupValues($this->name);
            
            if (!in_array($input, $lookup_data)) {
                return ["Invalid lookup value [{$input}] for column {$this->name}."];
            }

            return [];
        }
    }
}
