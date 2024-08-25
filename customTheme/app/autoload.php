<?php

function autoload_php_files_from_directory($directory) {
    if (is_dir($directory)) {
        if ($handle = opendir($directory)) {
            while (false !== ($file = readdir($handle))) {
                if (pathinfo($file, PATHINFO_EXTENSION) == 'php') {
                    require_once $directory . '/' . $file;
                }
            }
            closedir($handle);
        }
    }
}

autoload_php_files_from_directory(APP_DIR . '/shortcodes');
