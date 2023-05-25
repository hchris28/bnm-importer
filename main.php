<?php

namespace Bnm\Importer {

    use \Bnm\Importer\ImportDefinitions\ImportDefinitionFactory;
    use \Bnm\Importer\ImportValidationStatus;
    use \Bnm\Importer\Importer;
    use \Bnm\Importer\Utility\ConsoleLogger as Logger;
    use \Bnm\Importer\Utility\Messenger;

    require __DIR__ . '/vendor/autoload.php';

    const INCOMING_DIR = __DIR__ . '/incoming_data';
    const BACKUP_DIR = __DIR__ . '/backup';

    $logger = new Logger();
    $messenger = new Messenger();

    // files in the incoming directory are broken out into sub directories for each import definition
    foreach (new \DirectoryIterator(INCOMING_DIR) as $dir_info) {
        if (!$dir_info->isDir() || $dir_info->isDot()) {
            continue;
        }

        foreach (new \DirectoryIterator($dir_info->getPathname()) as $file_info) {
            if (!$file_info->isFile() || $file_info->isDot() || $file_info->getExtension() !== 'xlsx') {
                continue;
            }

            $full_path_to_folder = $dir_info->getPathname();
            $folder_name = $dir_info->getBasename();

            $full_path_to_file = $file_info->getPathname();
            $file_name = $file_info->getFilename();

            $excel_parser = new ExcelParser($full_path_to_file);
            $import_data = $excel_parser->parse();

            $import_definition = ImportDefinitionFactory::getImportDefinitionByLocation($folder_name);
            if ($import_definition == null) {
                $messenger->send_message("Could not locate an import definition for {$file_name}");
                $logger->writeLine("Could not locate an import definition for {$file_name}");
                continue;
            }

            $import_context = new ImportContext();
            $importer = new Importer($import_context, $import_data, $import_definition);

            $import_validation_result = $importer->validate();
            if ($import_validation_result->status == ImportValidationStatus::Error) {
                $messenger->send_message("Import for file {$folder_name}/{$file_name} did not pass validation: " . implode(" ", $import_validation_result->errors));
                $logger->writeLine("Errors:", $import_validation_result->errors);
                continue;
            }

            try {
                $import_success = $importer->import();
                if ($import_success) {
                    rename($file_info->getRealPath(), "{$full_path_to_folder}/backup/{$import_context->getImportId()}-{$file_name}");
                }
                $messenger->send_message("File {$folder_name}/{$file_name} imported successfully.");
            } catch (\Exception $ex) {
                $messenger->send_message("Import for file {$folder_name}/{$file_name} failed: {$ex->getMessage()}");
                $logger->writeLine($ex->getMessage());
                continue;
            } finally {
                $excel_parser->cleanupTempImages();
            }
        }
    }
}