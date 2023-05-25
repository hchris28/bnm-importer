<?php

namespace Bnm\Importer\DataValidation {
    class PointValidator extends Validator
    {
        public function is_valid($input)
        {
            if (!isset($input))
                return false;

            list($lat, $lng) = preg_split('/(\s*,*\s*)*,+(\s*,*\s*)*/', $input);

            return filter_var($lat, FILTER_VALIDATE_FLOAT) !== false
                && filter_var($lng, FILTER_VALIDATE_FLOAT) !== false;
        
        }
    }
}