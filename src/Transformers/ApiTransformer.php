<?php

namespace Napp\Core\Api\Transformers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

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

        if (true === $data instanceof LengthAwarePaginator || true === $data instanceof Paginator) {
            return $this->transformPaginatedOutput($data);
        }

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

    protected function transformPaginatedOutput($data): array
    {
        $result = $data->toArray();

        $result['data'] = $this->transformOutput($data->getCollection());

        return $result;
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

        switch ($type) {
            case 'datetime':
                return strtotime($value) > 0 ? date("c", strtotime($value)) : '';
            case 'int':
                return (int) $value;
            case 'bool':
                return (bool) $value;
            case 'array':
                return (array) $value;
            case 'json':
                return json_decode($value);
            case 'float':
                return (float) $value;
            default:
                return $value;
        }
    }
}
