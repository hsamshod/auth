<?php
    $lib = [
        "lib/Singleton",
        "lib/Application",
        "lib/Controller",
        "lib/Config",
        "lib/Db",
        "lib/Request",
        "lib/Response",
        "lib/Validator",
        "lib/Logger",
    ];

    $models = [
        "models/User",
        "models/Attachment",
    ];

    $others = [
        "utils",
    ];

    foreach (array_merge($lib, $models, $others) as $file) {
        require_once "{$file}.php";
    }

    return new Application();
