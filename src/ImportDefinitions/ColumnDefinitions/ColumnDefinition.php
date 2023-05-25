<?php

namespace Bnm\Importer\ImportDefinitions\ColumnDefinitions {
    use \Bnm\Importer\ImportDefinitions\ImportDefinition;
    use \Bnm\Importer\DataValidation\Validator;

    /**
     * Reprsents a column in a target database, and generally (but not always) a mapping to a field in an import dataset. 
     */
    abstract class ColumnDefinition
    {
        /**
         * @param ImportDefinition $import_definition The associated import definition for the column.
         * @param string $name The name of the column in the destination database.
         * @param array $input_column An array containing positional information about the field ['ordinal' => 1, 'name' => 'column anme'].
         * @param array $validation An array of callable objects containing validcation rules.
         * @param int $param_type The parameter type for the destination database, one of the \PDO::PARAM_* values.
         * @param int $value_type A value indicating more specifics about the data in the column, one of the ColumnValueType constants.
         */
        public function __construct(
            ImportDefinition $import_definition,
            string $name, 
            ?array $input_column = null, 
            array $validation = [], 
            int $param_type = \PDO::PARAM_STR, 
            int $value_type = ColumnValueType::String)
        {
            $this->import_definition = $import_definition;
            $this->name = $name;
            $this->input_column = $input_column;
            $this->validation = $validation;
            $this->param_type = $param_type;
            $this->value_type = $value_type;
        }

        /**
         * The associated import definition for the column.
         */
        public ImportDefinition $import_definition;

        /**
         * The name of the column in the destination database.
         */
        public string $name;

        /**
         * An array containing positional information about the field ['ordinal' => 1, 'name' => 'column anme']
         */
        public ?array $input_column;

        /**
         * An array of callable objects containing validation rules.
         */
        public array $validation;

        /**
         * The parameter type for the destination database, one of the \PDO::PARAM_* values.
         */
        public string $param_type;

        /**
         * A value indicating more specifics about the data in the column, one of the ColumnValueType constants.
         */
        public int $value_type;

        /**
         * Process the value of the input to prepare it for sql insert. This method takes the entire row data
         * as input so we can access other field data if necessary. The current field input value can be accessed 
         * with ```php
         * $this->input_column['ordinal']
         * ```
         * 
         * @param array $row An array containing the current row data.
         */
        public function format_sql_param_value(array $row) {

            return $row[$this->input_column['ordinal']];
        }

        /**
         * Apply validation rules to the given data.
         * 
         * @param array $row The data to validate.
         */
        public function validate($row) : array
        {
            $column_errors = [];

            foreach ($this->validation as $rule) {
                $validator = $this->_getValidatorClass($rule);
                $input_value = $row[$this->input_column['ordinal']];
                if (!$validator->is_valid($input_value)) {
                    $column_errors[] = "{$this->name} failed validation rule `{$rule}` with value `{$input_value}`.";
                }
            }

            return $column_errors;
        }

        /**
         * Get an instance of a validator for a given class name.
         * 
         * Valdiator classes are found in \Bnm\Importer\DataValidation\.
         * 
         * @param string $classname The name of the class of the validator.
         * 
         * @return Validator An instance of a validator class.
         */
        private function _getValidatorClass(string $classname): Validator
        {
            $fq_classname = '\\Bnm\\Importer\\DataValidation\\' . $classname . 'Validator';
            $validator = new $fq_classname();

            return $validator;
        }
    }
}
