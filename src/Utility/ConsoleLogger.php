<?php

namespace Bnm\Importer\Utility {

    class ConsoleLogger
    {
        function writeLine($message, $data = [])
        {
            echo ">>> {$message}\n";

            if (is_array($data) &&count($data) > 0) {
                print_r($data);
            }
        }
    }
}
