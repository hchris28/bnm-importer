<?php

namespace Bnm\Importer\DataValidation {
    class DateValidator extends Validator
    {
        public function is_valid($input)
        {
            $date = date_parse($input);
            return $date['error_count'] == 0
                && checkdate($date['month'], $date['day'], $date['year']) !== false;
        }
    }
}
