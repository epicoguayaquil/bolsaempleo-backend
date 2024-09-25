<?php

return [
        'displayErrorDetails'    => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Database settings seguridad
        'db_seg' => [
            'driver' => DB_SEG_DRIVER,
            'host' => DB_SEG_HOST,
            'database' => DB_SEG_NAME,
            'username' => DB_SEG_USERNAME,
            'password' => DB_SEG_PASSWORD,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],

        // Database settings transaccional
        'db_trans' => [
            'driver' => DB_TRANS_DRIVER,
            'host' => DB_TRANS_HOST,
            'database' => DB_TRANS_NAME,
            'username' => DB_TRANS_USERNAME,
            'password' => DB_TRANS_PASSWORD,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],

        // Database settings programas
        'db_prog' => [
            'driver' => DB_PROG_DRIVER,
            'host' => DB_PROG_HOST,
            'database' => DB_PROG_NAME,
            'username' => DB_PROG_USERNAME,
            'password' => DB_PROG_PASSWORD,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],

        // Database settings empleabilidad
        'db_empleabilidad' => [
            'driver' => DB_EMPL_DRIVER,
            'host' => DB_EMPL_HOST,
            'database' => DB_EMPL_NAME,
            'username' => DB_EMPL_USERNAME,
            'password' => DB_EMPL_PASSWORD,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]
];
