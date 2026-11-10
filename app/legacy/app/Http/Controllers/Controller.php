<?php

namespace App\Http\Controllers;

class Controller
{
    protected static ?int $id;

    protected static ?array $body;

    public static function id(): ?int
    {
        if (! isset(static::$id)) {
            static::$id = legacy_explode_last('/', $_SERVER['REQUEST_URI']);

            if (is_numeric(static::$id)) {
                return static::$id = (int) static::$id;
            }
        }

        return static::$id;
    }

    public static function body(?string $key = null)
    {
        if (! isset(static::$body)) {
            static::$body = json_decode(file_get_contents('php://input'), true) ?: [];
        }

        if ($key) {
            return static::$body[$key] ?? null;
        }

        return static::$body;
    }
}
