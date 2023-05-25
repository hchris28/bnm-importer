<?php

namespace Bnm\Importer\DataValidation {
    class DatetimeValidator extends Validator
    {
        public function is_valid($input)
        {
            try {
                $date = new \DateTime($input);
            } catch (\Exception $e) {
                return false;
            }

            return true;
        }
    }
}
