<?php

namespace Napp\Core\Api\Transformers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

/**
 * Class NoneTransformer.
 */
class NoneTransformer implements TransformerInterface
{
    /**
     * @param array|Arrayable $data
     *
     * @return array
     */
    public function transformInput($data): array
    {
        if ($data instanceof Arrayable) {
            return $data->toArray();
        }

        return Arr::wrap($data);
    }

    /**
     * @param array|Arrayable $data
     *
     * @return array
     */
    public function transformOutput($data): array
    {
        if ($data instanceof Arrayable) {
            return $data->toArray();
        }

        return Arr::wrap($data);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function transformOutputKeys(array $data): array
    {
        return $data;
    }
}
