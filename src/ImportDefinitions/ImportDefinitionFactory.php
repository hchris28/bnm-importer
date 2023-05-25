<?php

namespace Bnm\Importer\ImportDefinitions {
    use \Bnm\Importer\Utility\ConsoleLogger as Logger;
    use \BnM\Importer\ImportDefinitions\ImportDefinition;

    /**
     * Create instances of import definitions.
     */
    class ImportDefinitionFactory
    {
        /***
         * Get an import definition by name
         */
        static function getImportDefinitionByLocation(string $import_folder) : ?ImportDefinition
        {
            $logger = new Logger();
            $classnames = self::_getImportDefinitionClassnames();

            foreach ($classnames as $classname) {
                $logger->writeLine("Trying {$classname}");
                $import_definition = self::_getImportDefinitionClass($classname);

                if ($import_folder !== $import_definition->import_folder) {
                    $logger->writeLine('Folder mismatch');
                    continue;
                }
                
                // if get here we have found the right import definition
                return $import_definition;
            }

            // if we get here no import definition was found
            return null;
        }

        /**
         * Find an import definition that matches the provided data.
         * 
         * An import definition must have the same number of columns and 
         * have the same column names in the same order to match the data.
         * 
         * @param array $headers An array of column names from the data source. 
         * 
         * @return ImportDefinition
         */
        static function getImportDefinitionFromHeaders(array $headers) : ?ImportDefinition
        {
            $logger = new Logger();
            $classnames = self::_getImportDefinitionClassnames();

            foreach ($classnames as $classname) {
                $logger->writeLine("Trying {$classname}");
                $import_definition = self::_getImportDefinitionClass($classname);
                $import_columns = $import_definition->getImportColumns();

                if (count($import_columns) !== count($headers)) {
                    $logger->writeLine('Column count doesn\'t match');
                    continue;
                }

                foreach ($import_columns as $column) {
                    if ($column->input_column['name'] != $headers[$column->input_column['ordinal']]) {
                        $logger->writeLine('Input names don\'t match');
                        break;
                    }
                }

                // if get here we have found the right import definition
                return $import_definition;
            }

            // if we get here no import definition was found
            $logger->writeLine("No import definition found.");
            return null;
        }

        /**
         * Get a list of imp[ort definition class names found in the application.
         * 
         * Import defintions are found in \BnM\Importer\ImportDefinitions\.
         * 
         * @return array An array of strings suitable for instantiation. 
         */
        static private function _getImportDefinitionClassnames() : array
        {
            $classnames = [];
            $dir = __DIR__;

            foreach (new \DirectoryIterator($dir) as $item) {
                if (!$item->isFile())
                    continue;

                $candidateClassname = $item->getBasename('.php');
                if (preg_match('/^[a-zA-Z]+ImportDefinition$/', $candidateClassname)) {
                    $classnames[] = $candidateClassname;
                }
            }

            return $classnames;
        }

        /**
         * Instantiate an import definition from a class name.
         * 
         * @param string $classname The name of the class to instantiate.
         * @return ImportDefinition
         */
        static private function _getImportDefinitionClass(string $classname) : ImportDefinition {
            $fq_classname = '\\Bnm\\Importer\\ImportDefinitions\\' . $classname;
            $importDefinition = new $fq_classname();

            return $importDefinition;
        }
    }
}
