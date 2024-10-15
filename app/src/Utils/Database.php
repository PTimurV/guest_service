<?php

/**
 * Класс для управления подключением к базе данных.
 */

class Database
{
    private static $connection = null;

    /**
     * Возвращает объект подключения к базе данных.
     *
     * @return PDO Объект PDO для работы с базой данных.
     */
    public static function getConnection()
    {
        if (!self::$connection) {
            $dsn = 'pgsql:host=db;port=5432;dbname=guest_db;';
            $user = 'postgres';
            $password = 'postgres';
            self::$connection = new PDO($dsn, $user, $password);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$connection;
    }
}
