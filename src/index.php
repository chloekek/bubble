<?php

// This source file sets up the autoloading, error handling boilerplate, and
// php.ini sanitization that every PHP application needs, and calls the entry
// point of the application. This source file is exempt from type checking by
// Psalm.

require_once __DIR__ . '/../composer-vendor/autoload.php';

set_error_handler(
    function($severity, $message, $file, $line) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    },
);

Bubble\Support\Main::main();
