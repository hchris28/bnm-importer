<?php

namespace Bnm\Importer\DataValidation {
    abstract class Validator
    {
        abstract public function is_valid($input);
    }
}
