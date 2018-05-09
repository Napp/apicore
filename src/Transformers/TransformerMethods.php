<?php

namespace Napp\Core\Api\Transformers;

class TransformerMethods
{
    public static function convertJson($value)
    {
        return json_decode($value);
    }

    public static function convertInteger($value): int
    {
        return (int) $value;
    }

    public static function convertString($value): string
    {
        return (string) $value;
    }

    public static function convertFloat($value, $parameters): float
    {
        if (isset($parameters[0])) {
            return round($value, $parameters[0], PHP_ROUND_HALF_UP);
        }

        return (float) $value;
    }

    public static function convertBoolean($value): bool
    {
        return (bool) $value;
    }

    public static function convertArray($value): array
    {
        return (array) $value;
    }

    public static function convertDatetime($value): string
    {
        return strtotime($value) > 0 ? date('c', strtotime($value)) : '';
    }
}
