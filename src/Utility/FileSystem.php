<?php

namespace Bnm\Importer\Utility {

    class FileSystem
    {
        /**
         * Delete a file or folder.
         */
        static function delete($path)
        {
            if (is_dir($path) === true) {
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::CHILD_FIRST);

                foreach ($files as $file) {
                    if (in_array($file->getBasename(), array('.', '..')) !== true) {
                        if ($file->isDir() === true) {
                            rmdir($file->getPathName());
                        } else if (($file->isFile() === true) || ($file->isLink() === true)) {
                            unlink($file->getPathname());
                        }
                    }
                }

                return rmdir($path);
            } else if ((is_file($path) === true) || (is_link($path) === true)) {
                return unlink($path);
            }

            return false;
        }
        
        /**
         * Create a directory on disk.
         */
        static function createDirectory($path)
        {
            mkdir($path);
        }

        /**
         * Copy a file, or recursively copy a folder and its contents
         * @param       string   $source    Source path
         * @param       string   $dest      Destination path
         * @param       int      $permissions New folder creation permissions
         * @return      bool     Returns true on success, false on failure
         */
        static function xcopy($source, $dest, $permissions = 0755)
        {
            $sourceHash = self::_hashDirectory($source);
            // Check for symlinks
            if (is_link($source)) {
                return symlink(readlink($source), $dest);
            }

            // Simple copy for a file
            if (is_file($source)) {
                return copy($source, $dest);
            }

            // Make destination directory
            if (!is_dir($dest)) {
                mkdir($dest, $permissions);
            }

            // Loop through the folder
            $dir = dir($source);
            while (false !== $entry = $dir->read()) {
                // Skip pointers
                if ($entry == '.' || $entry == '..') {
                    continue;
                }

                // Deep copy directories
                if ($sourceHash != self::_hashDirectory($source . "/" . $entry)) {
                    self::xcopy("$source/$entry", "$dest/$entry", $permissions);
                }
            }

            // Clean up
            $dir->close();
            return true;
        }

        // In case of coping a directory inside itself, there is a need to hash check the directory otherwise and infinite loop of coping is generated

        static private function _hashDirectory($directory)
        {
            if (!is_dir($directory)) {
                return false;
            }

            $files = array();
            $dir = dir($directory);

            while (false !== ($file = $dir->read())) {
                if ($file != '.' and $file != '..') {
                    if (is_dir($directory . '/' . $file)) {
                        $files[] = self::_hashDirectory($directory . '/' . $file);
                    } else {
                        $files[] = md5_file($directory . '/' . $file);
                    }
                }
            }

            $dir->close();

            return md5(implode('', $files));
        }
    }
}
