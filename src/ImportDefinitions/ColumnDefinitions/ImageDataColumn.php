<?php

namespace Bnm\Importer\ImportDefinitions\ColumnDefinitions {
    use \Bnm\Importer\ImportContext;

    class ImageDataColumn extends ColumnDefinition
    {
        public function __construct($import_definition, $name, $input_column, $validation = [])
        {
            parent::__construct(
                $import_definition,
                $name,
                $input_column,
                $validation,
                \PDO::PARAM_STR,
                ColumnValueType::ImageData
            );
        }

        public function format_sql_param_value(mixed $row) {
            $input = $row[$this->input_column['ordinal']];
            
            if (empty($input))
                return null;

            // ImageDataColumn's expect that the input value will be a path to a file on disk
            $path_info = pathinfo($input);
            
            return "{$this->import_definition->getImagePath()}/{$path_info['basename']}";
        }
    }
}
