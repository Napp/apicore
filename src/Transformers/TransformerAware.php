<?php

namespace Napp\Core\Api\Transformers;

/**
 * Trait TransformerAware.
 */
trait TransformerAware
{
    /**
     * @return \Napp\Core\Api\Transformers\ApiTransformer
     */
    abstract public function getTransformer(): ApiTransformer;
}
