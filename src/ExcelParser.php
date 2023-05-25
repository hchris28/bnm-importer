<?php

namespace Bnm\Importer {

    use \PhpOffice\PhpSpreadsheet\IOFactory;
    use \PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
    use \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
    use \PhpOffice\PhpSpreadsheet\Cell\Coordinate;
    use \Bnm\Importer\Utility\FileSystem;
    use \Bnm\Importer\Utility\ConsoleLogger as Logger;

    /**
     * Read Excel files from disk and extract data and images.
     */
    class ExcelParser
    {
        /**
         * @param string $filename The path to the file on disk.
         */
        public function __construct(string $filename)
        {
            $this->filename = $filename;
            $this->_parse_id = uniqid('XLP');
        }

        /**
         * The path to the file on disk to parse.
         */
        public string $filename;

        /**
         * A mostly unique identifier for the related parsing operation. This is also used
         * as part of a directory path for parsed images.
         */
        private string $_parse_id;

        /**
         * Parse the Excel file and return the data.
         * 
         * @return ImportData An object containing a data array and a headers array.
         */
        function parse(?bool $has_headers = false): ImportData
        {
            $logger = new Logger();

            $spreadsheet = IOFactory::load($this->filename);
            $import_worksheet = $spreadsheet->getSheet(0);
            $file_data = $import_worksheet->toArray();

            // if the file does not have headers we will return an array with 
            $file_headers = $has_headers == true
                ? array_shift($file_data)
                : array_fill(0, count($file_data[0]), null);

            $drawings = $import_worksheet->getDrawingCollection();
            if (count($drawings) == 0) {
                return new ImportData($file_data, $file_headers, null);
            }

            $this->_createTempImageFolder();

            foreach ($drawings as $drawing) {

                $coordinate_string = $drawing->getCoordinates();
                $coordinates = Coordinate::indexesFromString($coordinate_string);

                // translate the row and col index to 0 based 
                $col_index = $coordinates[0] - 1; // $drawing->getCoordinates() return values are 1-based,
                $row_index = $coordinates[1] - 1;
                if ($has_headers)
                    $row_index -= 1;

                $temp_filepath = "";
                $temp_filename = $coordinate_string;

                if ($drawing instanceof MemoryDrawing) {
                    $mime_type = $drawing->getMimeType();
                    $image_content = $this->_getImageContent($drawing);
                    $temp_filepath = $this->_saveTempImageToDisk($mime_type, $image_content, $temp_filename);
                } else if ($drawing instanceof Drawing) {
                    if ($drawing->getPath()) {
                        // Check if the source is a URL or a file path
                        if ($drawing->getIsURL()) {

                            // TODO: refactor this

                            // $image_content = file_get_contents($drawing->getPath());
                            // $filePath = tempnam(sys_get_temp_dir(), 'Drawing');
                            // file_put_contents($filePath , $image_content);
                            // $mimeType = mime_content_type($filePath);
                            // // You could use the below to find the extension from mime type.
                            // // https://gist.github.com/alexcorvi/df8faecb59e86bee93411f6a7967df2c#gistcomment-2722664
                            // $extension = File::mime2ext($mimeType);
                            // unlink($filePath);            
                        } else {
                            $excel_image_path = $drawing->getPath();

                            // Scan-IT to Office saves images in Excel as *.tmp files? Inquire with support if we move forward
                            $mime_type = '';
                            $extension = $drawing->getExtension();
                            if ($extension == 'tmp') {
                                $mime_type = 'image/jpeg';
                            } else {
                                $mime_type = $drawing->getImageMimeType();
                            }

                            $zip_reader = fopen($excel_image_path, 'r');
                            $image_content = '';
                            while (!feof($zip_reader)) {
                                $image_content .= fread($zip_reader, 1024);
                            }
                            fclose($zip_reader);

                            $temp_filepath = $this->_saveTempImageToDisk($mime_type, $image_content, $temp_filename);
                        }
                    }
                }

                $file_data[$row_index][$col_index] = $temp_filepath;
            }

            return new ImportData(
                $file_data,
                $file_headers,
                $this->_getTempImagePath()
            );
        }

        /**
         * Delete all temporary image files created by the parser.
         */
        public function cleanupTempImages()
        {
            $temp_image_folder = $this->_getTempImagePath();
            FileSystem::delete($temp_image_folder);
        }

        /**
         * Create the temp folder to store images in.
         */
        private function _createTempImageFolder(): void
        {
            FileSystem::createDirectory($this->_getTempImagePath());
        }

        /**
         * Save an image to disk.
         * 
         * @param string $mime_type The MIME type of the image
         * @param string $image_content The string representation of the image.
         * @param string $temp_filename The filename for the image (no path, no extension).
         * 
         * @return string The path to the file on disk.
         */
        private function _saveTempImageToDisk(string $mime_type, string $image_content, string $temp_filename): string
        {
            list($type, $extension) = explode('/', $mime_type);
            if ($type != 'image' || !in_array($extension, ['gif', 'jpeg', 'png']))
                throw new \Exception("Invalid mime type [{$mime_type}].");

            $temp_filepath = $this->_getTempImagePath("{$temp_filename}.{$extension}");

            file_put_contents($temp_filepath, $image_content);

            return $temp_filepath;
        }

        /**
         * Return the path to the temporary image folder or a temporary image file for the current import.
         * 
         * @param string $filename If included, the return value will be the path to a file, otherwise the temp folder path is returned.
         * 
         * @return string The path to the temp image folder OR a temp image file. 
         */
        private function _getTempImagePath(?string $filename = null)
        {
            $path_info = pathinfo($this->filename);
            return "{$path_info['dirname']}/images/{$this->_parse_id}/{$filename}";
        }

        /**
         * Get image content from a MemoryDrawing object.
         * 
         * @param MemoryDrawing $drawing
         */
        private function _getImageContent(MemoryDrawing $drawing): string
        {
            ob_start();
            call_user_func(
                $drawing->getRenderingFunction(),
                $drawing->getImageResource()
            );
            $image_content = ob_get_contents();
            ob_end_clean();

            return $image_content;
        }
    }
}
