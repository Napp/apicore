<?php

namespace Napp\Core\Api\Transformers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ApiTransformer implements TransformerInterface
{
    /**
     * @var array
     */
    protected $apiMapping = [];

    /**
     * Strict mode removes keys that are
     * not specified in api mapping array.
     *
     * @var bool
     */
    protected $strict = false;

    /**
     * @param array|Model $apiMapping
     * @return void
     */
    public function setApiMapping($apiMapping)
    {
        $this->apiMapping = [];

        if (true === $apiMapping instanceof Model && true === property_exists($apiMapping, 'apiMapping')) {
            $this->apiMapping = $apiMapping->apiMapping;
        } elseif (true === \is_array($apiMapping)) {
            $this->apiMapping = $apiMapping;
        }
    }

    /**
     * @param array|Arrayable $data
     * @return array
     */
    public function transformInput($data): array
    {
        $input = [];

        $data = (true === \is_array($data)) ? $data : $data->toArray();
        foreach ($data as $key => $value) {
            $input[$this->findOriginalKey($key)] = $value;
        }

        return $input;
    }

    /**
     * @param array|Arrayable $data
     * @return array
     */
    public function transformOutput($data): array
    {
        $output = [];

        if (true === $data instanceof Collection) {
            foreach ($data as $item) {
                $output[] = $this->transformOutput($item);
            }
        } else {
            $data = (true === \is_array($data)) ? $data : $data->toArray();
            foreach ($data as $key => $value) {
                if (true === $this->strict && false === array_key_exists($key, $this->apiMapping)) {
                    continue;
                }
                $output[$this->findNewKey($key)] = $this->convertValueType($key, $value);
            }
        }

        return $output;
    }

    /**
     * @param string $newKey
     * @return string
     */
    protected function findOriginalKey(string $newKey)
    {
        foreach ($this->apiMapping as $key => $value) {
            if (true === \in_array($newKey, $value)) {
                return $key;
            }
        }

        return $newKey;
    }

    /**
     * @param string $originalKey
     * @return string
     */
    protected function findNewKey(string $originalKey): string
    {
        if (true === array_key_exists($originalKey, $this->apiMapping)) {
            return $this->apiMapping[$originalKey]['newName'];
        }

        return $originalKey;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    protected function convertValueType(string $key, $value)
    {
        $type = (true === array_key_exists($key, $this->apiMapping))
            ? $this->apiMapping[$key]['dataType']
            : 'string';

        foreach (static::normalizeType($type) as list($method, $parameters)) {
            if (true === empty($method)) {
                return $value;
            }

            if ('Nullable' === $method) {
                if (true === empty($value) && false === \is_numeric($value)) {
                    return null;
                }

                continue;
            }

            $method = "convert{$method}";

            if (false === method_exists(TransformerMethods::class, $method)) {
                return $value;
            }

            return TransformerMethods::$method($value, $parameters);
        }
    }

    protected static function parseStringDataType($type): array
    {
        $parameters = [];

        // The format for transforming data-types and parameters follows an
        // easy {data-type}:{parameters} formatting convention. For instance the
        // data-type "float:3" states that the value will be converted to a float with 3 decimals.
        if (mb_strpos($type, ':') !== false) {
            list($dataType, $parameter) = explode(':', $type, 2);

            $parameters = static::parseParameters($parameter);
        }

        $dataType = static::normalizeDataType(trim($dataType ?? $type));

        return [Str::studly($dataType), $parameters ?? []];
    }

    /**
     * Parse a parameter list.
     *
     * @param  string  $parameter
     * @return array
     */
    protected static function parseParameters($parameter): array
    {
        return str_getcsv($parameter);
    }

    protected static function parseManyDataTypes($type): array
    {
        $parsed = [];

        $dataTypes = explode('|', $type);

        foreach ($dataTypes as $dataType) {
            $parsed[] = static::parseStringDataType(trim($dataType));
        }

        return $parsed;
    }

    protected static function normalizeType($type): array
    {
        if (false !== mb_strpos($type, '|')) {
            return self::normalizeNullable(
                static::parseManyDataTypes($type)
            );
        }

        return [static::parseStringDataType(trim($type))];
    }

    /**
     * @param $type
     * @return bool
     */
    protected static function hasParameters($type): bool
    {
        return false !== mb_strpos($type, ':');
    }

    /**
     * @param $dataTypes
     * @return array
     */
    protected static function normalizeNullable($dataTypes): array
    {
        if (isset($dataTypes[1][0]) && $dataTypes[1][0] === 'Nullable') {
            return array_reverse($dataTypes);
        }

        return $dataTypes;
    }

    protected static function normalizeDataType($type): string
    {
        switch ($type) {
            case 'int':
                return 'integer';
            case 'bool':
                return 'boolean';
            case 'date':
                return 'datetime';
            default:
                return $type;
        }
    }
}
