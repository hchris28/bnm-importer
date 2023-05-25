<?php

namespace Bnm\Importer {

    use \Bnm\Importer\DataAccess\Connection;
    use \Bnm\Importer\ImportData;
    use \Bnm\Importer\ImportValidationStatus;
    use \Bnm\Importer\ImportValidationResult;
    use \Bnm\Importer\ImportDefinitions\ImportDefinition;
    use Bnm\Importer\ImportDefinitions\ColumnDefinitions\ColumnValueType;
    use \Bnm\Importer\Utility\FileSystem;

    /**
     * Manages the import process.
     */
    class Importer
    {
        /**
         * Constructor
         * 
         * @param array $data The data to import.
         * @param ImportDefinition $import_definition The import definition for the data.
         */
        public function __construct(
            ImportContext $import_context,
            ImportData $import_data,
            ImportDefinition $import_definition
        ) {
            $this->_import_context = $import_context;
            $this->_import_data = $import_data;
            $this->_import_definition = $import_definition;
        }

        private ImportContext $_import_context;
        private ImportData $_import_data;
        private ImportDefinition $_import_definition;

        /**
         * Process the data in preparation for import.
         * 
         * @return ImportValidationResult
         */
        function validate(): ImportValidationResult
        {
            $errors = [];

            foreach ($this->_import_data->file_data as $key => $row) {
                $validation_result = $this->_validateRow($row);
                if ($validation_result !== true) {
                    var_dump($validation_result);
                    $errors[] = "Error on row {$key}: " . implode(' ', $validation_result);
                } else {
                    // we don't need to continue building the sql parameters if there are any errors,
                    // but we do want to continue looking for more errors
                    if (count($errors) > 0) {
                        continue;
                    }
                }
            }

            return count($errors) == 0
                ? new ImportValidationResult(ImportValidationStatus::Ok, [])
                : new ImportValidationResult(ImportValidationStatus::Error, $errors);
        }

        /**
         * Import the data into the database.
         * 
         * Always call prepare() first and examine the result before calling execute().
         * 
         * @return bool
         */
        public function import(): bool
        {
            $this->_import_definition->setImportContext($this->_import_context);

            //sanity check
            if (!$this->_import_definition->readyToImport())
                return false;

            $db_conn = new Connection();
            $db_conn->beginTransaction();

            $stmt_success = $db_conn->executePreparedStatement(
                $this->_generateSql(),
                $this->_generateParamsArray()
            );

            if ($stmt_success)
                $this->_moveImageFiles();

            $db_conn->commit();

            // ImportContext's can't be re-used
            $this->_import_definition->setImportContext(null);

            // TODO: should we include more info in the return value? rows inserted? error messages?
            return $stmt_success;
        }

        /**
         * Move files from the temp directory to the directory defined by the import definition.
         */
        private function _moveImageFiles()
        {
            if ($this->_import_data->temp_image_folder != null) {
                FileSystem::xcopy(
                    $this->_import_data->temp_image_folder,
                    $this->_import_definition->getImagePath()
                );
            }
        }

        /**
         * Generate the sql statement for multiple insert as defined by import definition.
         */
        private function _generateSql(): string
        {
            $col_names = array_map(function ($column) {
                return $column->name;
            }, $this->_import_definition->columns);
            $stmt_cols = "`" . implode('`, `', $col_names) . "`";

            $col_placeholders = array_map(function ($column) {
                if ($column->value_type === ColumnValueType::Point) {
                    return '(ST_PointFromText(?, 4326))';
                }
                return '?';
            }, $this->_import_definition->columns);


            $row_placeholders = '(' . implode(', ', $col_placeholders) . ')';
            $all_row_placeholders = implode(', ', array_fill(0, count($this->_import_data->file_data), $row_placeholders));

            return "INSERT INTO `{$this->_import_definition->table}` ({$stmt_cols}) VALUES {$all_row_placeholders}";
        }

        /**
         * Generate an array of parameters suitable for supplying to the sql prepared statement.
         */
        private function _generateParamsArray(): array
        {
            $param_values = [];
            $param_index = 1;

            foreach ($this->_import_data->file_data as $row) {
                foreach ($this->_import_definition->columns as $column) {
                    $param_values[$param_index++] = $column->format_sql_param_value($row);
                }
            }

            return $param_values;
        }

        /**
         * Validate the data in a data row as defined by the import definition.
         * 
         * @param array $row The data to validate.
         * 
         * @return bool|array True on success, an array of error messages on failure.
         */
        private function _validateRow(array $row): mixed
        {
            $errors = [];

            foreach ($this->_import_definition->getImportColumns() as $column) {
                array_push($errors, ...$column->validate($row));
            }

            return count($errors) > 0 ? $errors : true;
        }
    }
}
