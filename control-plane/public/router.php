<?php

// Let the built-in server serve real static files and send application paths to Laravel.
if (php_sapi_name() === 'cli-server') {
    $requested = __DIR__.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($requested)) {
        return false;
    }
}

require __DIR__.'/index.php';
