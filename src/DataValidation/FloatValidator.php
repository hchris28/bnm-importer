<?php

namespace Bnm\Importer\DataValidation {
    class FloatValidator extends Validator
    {
        public function is_valid($input)
        {
            return isset($input)
                && filter_var($input, FILTER_VALIDATE_FLOAT) !== false;
        }
    }
}
