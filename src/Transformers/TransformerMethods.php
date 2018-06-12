<?php

namespace Napp\Core\Api\Transformers;

use Illuminate\Support\Collection;

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

    /**
     * @param $value
     * @param $parameters
     * @param $key
     * @return array
     * @throws \Napp\Core\Api\Exceptions\Exceptions\Exception
     */
    public static function convertRelationship($value)
    {
        $output = [];

        if ($value instanceof Collection || \is_array($value)) {
            foreach ($value as $valKey => $valValue) {
                if (\is_object($valValue) &&
                    true === array_key_exists(TransformerAware::class, class_uses($valValue))
                ) {
                    if (is_numeric($valKey)) {
                        $output[$valKey] = $valValue->getTransformer()->transformOutput($valValue);
                    } else {
                        $output = $valValue->getTransformer()->transformOutput($valValue);
                    }
                }
            }

            return $output;
        }

        if (\is_object($value) && array_key_exists(TransformerAware::class, class_uses($value))) {
            return $value->getTransformer()->transformOutput($value);
        }

        //throw new InvalidTransformException()
    }
}
