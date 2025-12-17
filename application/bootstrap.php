<?php
/**
 * Используйте этот файл для разовой инициализации компонентов до старта приложения,
 * которые не могут быть инициализированы средствами ядра (через файл конфигурации)
 */

// Устанавливаем кодировку по умолчанию для всех строковых операций
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_regex_encoding('UTF-8');

// Устанавливаем локаль для корректной работы с русскими символами
setlocale(LC_ALL, 'ru_RU.UTF-8');
\ItForFree\SimpleAsset\SimpleAssetManager::$assetsPath = '/assets'; //инициализация пути добавления ассетов

// Устанавливаем заголовок для корректной обработки UTF-8
if (!headers_sent()) {
    header('Content-Type: text/html; charset=utf-8');
}



