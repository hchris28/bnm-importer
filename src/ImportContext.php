<?php

namespace Bnm\Importer {
    use \Bnm\Importer\Utility\GuidGenerator;
    use \DateTime;

    /**
     * Represents information about the context of an import.
     */
    class ImportContext
    {
        public function __construct()
        {
            $this->_import_id = GuidGenerator::new_guid();
            $this->_import_time = new DateTime('now');
        }
        
        /**
         * A unique id to identify this import.
         */
        private string $_import_id;

        /**
         * The date the import was initiated.
         */
        private DateTime $_import_time; 

        /**
         * Get the unique id for the import context.
         */
        public function getImportId() : string
        {
            return $this->_import_id;
        }

        /**
         * Ger the time the import was initiated.
         */
        public function getImportTime() : DateTime
        {
            return $this->_import_time;
        }
    }
}
