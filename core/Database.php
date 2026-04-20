<?php

namespace Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    protected static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (static::$connection instanceof PDO) {
            return static::$connection;
        }

        $config = config('database', []);

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['host'] ?? '127.0.0.1',
            $config['port'] ?? '3306',
            $config['dbname'] ?? '',
            $config['charset'] ?? 'utf8mb4'
        );

        try {
            static::$connection = new PDO(
                $dsn,
                $config['username'] ?? 'root',
                $config['password'] ?? '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $exception) {
            throw new RuntimeException(
                'Không thể kết nối MySQL. Hãy kiểm tra file config/database.php và database glass_shop_db_test. Chi tiết: '
                . $exception->getMessage()
            );
        }

        return static::$connection;
    }
}
