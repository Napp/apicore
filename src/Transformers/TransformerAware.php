<?php

namespace Napp\Core\Api\Transformers;

/**
 * Trait TransformerAware
 * @package Napp\Core\Api\Transformers
 */
trait TransformerAware
{
    /**
     * @return \Napp\Core\Api\Transformers\ApiTransformer
     */
    abstract public function getTransformer(): ApiTransformer;
}
