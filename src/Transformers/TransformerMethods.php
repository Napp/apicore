<?php

namespace Napp\Core\Api\Transformers;

/**
 * Class TransformerMethods
 * @package Napp\Core\Api\Transformers
 */
class TransformerMethods
{
    /**
     * @param $value
     * @return mixed
     */
    public static function convertJson($value)
    {
        return json_decode($value);
    }

    /**
     * @param $value
     * @return int
     */
    public static function convertInteger($value): int
    {
        return (int) $value;
    }

    /**
     * @param $value
     * @return string
     */
    public static function convertString($value): string
    {
        return (string) $value;
    }

    /**
     * @param $value
     * @param $parameters
     * @return float
     */
    public static function convertFloat($value, $parameters): float
    {
        if (isset($parameters[0])) {
            return round($value, $parameters[0], PHP_ROUND_HALF_UP);
        }

        return (float) $value;
    }

    /**
     * @param $value
     * @return bool
     */
    public static function convertBoolean($value): bool
    {
        return (bool) $value;
    }

    /**
     * @param $value
     * @return array
     */
    public static function convertArray($value): array
    {
        return (array) $value;
    }

    /**
     * @param $value
     * @return string
     */
    public static function convertDatetime($value): string
    {
        return strtotime($value) > 0 ? date('c', strtotime($value)) : '';
    }
}
