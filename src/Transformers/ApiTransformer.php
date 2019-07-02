<?php

namespace Napp\Core\Api\Transformers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class ApiTransformer.
 */
class ApiTransformer implements TransformerInterface
{
    /**
     * @var array
     */
    public $apiMapping = [];

    /**
     * Strict mode removes keys that are
     * not specified in api mapping array.
     *
     * @var bool
     */
    protected $strict = false;

    /**
     * @param array|Model $apiMapping
     *
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
     *
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
     * @param array|Arrayable|LengthAwarePaginator|Paginator $data
     *
     * @return array
     */
    public function transformOutput($data): array
    {
        $output = [];

        if (true === $data instanceof LengthAwarePaginator || true === $data instanceof Paginator) {
            return $this->transformPaginatedOutput($data);
        }

        if (true === $data instanceof Collection) {
            $output = $this->transformCollection($output, $data);
        } elseif (true === $data instanceof Model) {
            $output = $this->transformAttributes($output, $data->attributesToArray());
            $output = $this->transformRelationships($output, $data);
        } else {
            $data = (true === \is_array($data)) ? $data : $data->toArray();
            $output = $this->transformAttributes($output, $data);
        }

        return $output;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function transformOutputKeys(array $data): array
    {
        $output = [];
        foreach ($data as $key => $value) {
            $output[$this->findNewKey($key)] = $value;
        }

        return $output;
    }

    /**
     * @param array $output
     * @param array $data
     *
     * @return array
     */
    protected function transformAttributes(array $output, array $data): array
    {
        foreach ($data as $key => $value) {
            if (true === $this->strict && !$this->isMapped($key)) {
                continue;
            }

            $output[$this->findNewKey($key)] = $this->convertValueType($key, $value);
        }

        return $output;
    }

    /**
     * @param array $output
     * @param Model $data
     *
     * @return array
     */
    protected function transformRelationships(array $output, Model $data): array
    {
        /** @var Model $data */
        $relationships = $data->getRelations();
        foreach ($relationships as $relationshipName => $relationship) {
            if (true === $this->strict && !$this->isMapped($relationshipName)) {
                continue;
            }

            $outputKey = $this->findNewKey($relationshipName);

            if (null === $relationship) {
                $output[$outputKey] = $this->convertValueType($relationshipName, null);
            } elseif (true === $relationship instanceof Collection) {
                if ($relationship->isEmpty()) {
                    $output[$outputKey] = $this->convertValueType($relationshipName, null);
                    continue;
                }

                if ($this->isTransformAware($relationship->first())) {
                    $output[$outputKey] = $relationship->first()->getTransformer()->transformOutput($relationship);
                } else {
                    $output[$outputKey] = $relationship->toArray();
                }
            } else {
                // model
                if ($this->isTransformAware($relationship)) {
                    $output[$outputKey] = $relationship->getTransformer()->transformOutput($relationship);
                } else {
                    $output[$outputKey] = $relationship->getAttributes();
                }
            }
        }

        return $output;
    }

    /**
     * @param $data
     *
     * @return array
     */
    protected function transformPaginatedOutput($data): array
    {
        $items = $this->transformOutput($data->getCollection());

        $output = [
            'data'       => $items,
            'pagination' => [
                'currentPage'  => $data->currentPage(),
                'perPage'      => $data->perPage(),
                'firstPageUrl' => $data->url(1),
                'nextPageUrl'  => $data->nextPageUrl(),
                'prevPageUrl'  => $data->previousPageUrl(),
            ],
        ];

        if (true === $data instanceof LengthAwarePaginator) {
            $output['pagination']['totalPages'] = $data->lastPage();
            $output['pagination']['total'] = $data->total();
            $output['pagination']['lastPageUrl'] = $data->url($data->lastPage());
        }

        return $output;
    }

    /**
     * @param array      $output
     * @param Collection $data
     *
     * @return array
     */
    protected function transformCollection(array $output, Collection $data): array
    {
        foreach ($data as $item) {
            $output[] = $this->transformOutput($item);
        }

        return $output;
    }

    /**
     * @param string $newKey
     *
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
     *
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
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function convertValueType(string $key, $value)
    {
        $type = (true === array_key_exists($key, $this->apiMapping))
            ? $this->apiMapping[$key]['dataType']
            : 'unknown';

        foreach (static::normalizeType($type) as list($method, $parameters)) {
            if (true === empty($method)) {
                return $value;
            }

            if ('Nullable' === $method) {
                if (true === empty($value) && false === \is_numeric($value)) {
                    return;
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

    /**
     * @param $type
     *
     * @return array
     */
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
     * @param string $parameter
     *
     * @return array
     */
    protected static function parseParameters($parameter): array
    {
        return str_getcsv($parameter);
    }

    /**
     * @param $type
     *
     * @return array
     */
    protected static function parseManyDataTypes($type): array
    {
        $parsed = [];

        $dataTypes = explode('|', $type);

        foreach ($dataTypes as $dataType) {
            $parsed[] = static::parseStringDataType(trim($dataType));
        }

        return $parsed;
    }

    /**
     * @param $type
     *
     * @return array
     */
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
     *
     * @return bool
     */
    protected static function hasParameters($type): bool
    {
        return false !== mb_strpos($type, ':');
    }

    /**
     * @param $dataTypes
     *
     * @return array
     */
    protected static function normalizeNullable($dataTypes): array
    {
        if (isset($dataTypes[1][0]) && $dataTypes[1][0] === 'Nullable') {
            return array_reverse($dataTypes);
        }

        return $dataTypes;
    }

    /**
     * @param $type
     *
     * @return string
     */
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

    /**
     * @param $model
     *
     * @return bool
     */
    protected function isTransformAware($model): bool
    {
        return array_key_exists(TransformerAware::class, class_uses($model));
    }

    /**
     * Check if key is mapped with apiMapping.
     *
     * @param $key
     *
     * @return bool
     */
    protected function isMapped($key): bool
    {
        return true === array_key_exists($key, $this->apiMapping);
    }
}
