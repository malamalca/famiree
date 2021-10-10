<?php

return [
    'debug' => false,

    'App' => [
        'namespace' => 'App',
        'encoding' => 'UTF-8',
        'baseUrl' => '/www/famiree',
        'db' => ROOT . DS . 'db' . DS . 'famiree.sqlite',
        'defaultName' => 'Famiree',
    ],

    'Log' => [
        'debug' => [
            'className' => 'StreamHandler',
            'file' => 'php://stderr', //LOGS . 'debug.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];
