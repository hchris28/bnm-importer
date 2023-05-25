<?php

namespace Bnm\Importer {

    /**
     * Indicates the result of import validation.
     */
    class ImportValidationResult
    {
        /**
         * The status of the result. One of the ImportValidationStatus constants. 
         */
        public int $status;

        /**
         * An array of error messages.
         */
        public array $errors;

        /**
         * Constructor
         * 
         * @param int $status The status of the result. One of the ImportValidationStatus constants. 
         * @param array $errors An array of error messages. An empty array if there are no errors.
         */
        public function __construct(int $status, array $errors = [])
        {
            $this->status = $status;
            $this->errors = $errors;
        }

        /**
         * Return all error messages as a single new-line delimited string.
         * 
         * @return string
         */
        public function getErrorString() {
            return implode("\n", $this->errors);
        }
    }
}
