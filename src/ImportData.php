<?php

namespace Bnm\Importer {

    class ImportData
    {
        public function __construct(
            array $file_data, 
            array $file_headers,
            ?string $temp_image_folder)
        {
            $this->file_data = $file_data;
            $this->file_headers = $file_headers;
            $this->temp_image_folder = $temp_image_folder;
        }
        
        public array $file_data;
        public array $file_headers;
        public ?string $temp_image_folder;

        public function getFieldCount()
        {
            return count($this->file_data[0]);
        }
    }
}
