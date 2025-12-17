<?php
/**
 * Конфигурационной файл консольного приложения
 */
$config = [
    'core' => [ // подмассив, используемый самим ядром фреймворка
        'db' => [
            'dns' => 'mysql:host=db;dbname=smvcbase_in_docker;charset=utf8mb4',
            'username' => 'myuser',
            'password' => '12345',
            'options' => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        ],
        'router' => [ // подсистема маршрутизации
            'class' => \ItForFree\SimpleMVC\Router\ConsoleRouter::class,
	    'alias' => '@router'
        ]
    ]    
];

return $config;