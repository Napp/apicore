<?php

namespace Napp\Api\Transformers;

use Illuminate\Contracts\Support\Arrayable;

interface TransformerInterface
{
    /**
     * @param array|Arrayable $data
     * @return array
     */
    public function transformInput($data): array;

    /**
     * @param array|Arrayable $data
     * @return array
     */
    public function transformOutput($data): array;
}
