<?php

namespace Bnm\Importer\ImportDefinitions {

    use \BnM\Importer\ImportDefinitions\ColumnDefinitions\ImageDataColumn;
    use \BnM\Importer\ImportContext;

    /**
     * The base class for an import defintion.
     */
    abstract class ImportDefinition
    {
        /**
         * The name of subfolder where import files are found.
         */
        public string $import_folder;

        /**
         * The name of the table in the database.
         */
        public string $table;

        /**
         * An array of column definitions.
         */
        public array $columns;

        /**
         * An associative array to get lookup values
         * ```
         * [
         *      'name' => string containing a unique name
         *      'values' => a callable that returns an array containing lookup values
         * ]
         * ``` 
         */
        public array $lookups;

        /**
         * The root path to the import_folder where images should be saved. 
         * @see ImportDefinition::getImagePath()
         */
        public ?string $image_folder;

        public function __construct(
            string $import_folder,
            string $table,
            array $columns
        ) {
            $this->import_folder = $import_folder;
            $this->table = $table;
            $this->columns = $columns;
            
            $this->image_folder = null;
            $this->lookups = [];
        }

        /**
         * Get the full path to the import_folder to save imported images to. It is the root path as defined by
         * by the $image_folder property and the import id found in the ImportContext.
         */
        public function getImagePath(): string
        {
            return "{$this->image_folder}/{$this->_import_context->getImportId()}";
        }

        /**
         * The id of the current import.
         */
        private ?ImportContext $_import_context = null;

        public function setImportContext($import_context): void
        {
            $this->_import_context = $import_context;
        }

        public function getImportContext(): ?ImportContext
        {
            return $this->_import_context;
        }

        /**
         * Get an array containing the subset of columns that are imported from the data file.
         * 
         * @return array An array of column definitions sprted by ordinal.
         */
        public function getImportColumns(): array
        {
            $import_columns = array_filter(
                $this->columns,
                fn ($column) => $column->input_column != null
            );

            usort(
                $import_columns,
                fn ($a, $b) => $a->input_column['ordinal'] <=> $b->input_column['ordinal']
            );

            return $import_columns;
        }

        /**
         * Returns true if the class is initialized properly and is ready to be used by the importer.
         */
        public function readyToImport()
        {
            if ($this->_import_context == null)
                return false;

            $has_image_col = false;
            foreach ($this->getImportColumns() as $column) {
                if ($column instanceof ImageDataColumn) {
                    $has_image_col = true;
                    break;
                }
            }

            if ($has_image_col && $this->image_folder == null)
                return false;

            return true;
        }

        private array $_lookup_cache = [];

        /**
         * Get lookup values by name
         * 
         * ```
         * [
         *      'name' => string containing a unique name
         *      'values' => an array containing lookup values
         * ]
         * 
         * @param string $name The name of the lookup list.
         * 
         * @return array
         */
        public function getLookupValues($name) : array
        {
            if (array_key_exists($name, $this->_lookup_cache))
                return $this->_lookup_cache[$name];

            $lookup_data = $this->lookups[$name]();

            $this->_lookup_cache[$name] = $lookup_data;

            return $lookup_data;
        }
    }
}
