<?php

namespace Bnm\Importer\ImportDefinitions\ColumnDefinitions {
    use Bnm\Importer\Utility\GuidGenerator;

    class AutoGuidColumn extends ColumnDefinition
    {
        public function __construct($import_definition, $name)
        {
            parent::__construct(
                $import_definition,
                $name, 
                null,
                [], 
                \PDO::PARAM_STR, 
                ColumnValueType::Guid
            );
        }

        public function format_sql_param_value($row) {
            // AutoGuid columns do not take any input value.
            return GuidGenerator::new_guid();
        }
    }
}