<?php

declare(strict_types=1);

// Включаем отображение ошибок (для разработки)
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Начало измерения времени и памяти
$startTime = microtime(true);
$startMemory = memory_get_usage();

// Подключаем необходимые файлы классов
require_once __DIR__ . '/../src/Utils/Database.php';
require_once __DIR__ . '/../src/Models/Guest.php';
require_once __DIR__ . '/../src/Controllers/GuestController.php';

// Подключаем роуты
require_once __DIR__ . '/../src/Routes/routes.php';
