<?php

namespace Napp\Core\Api\Transformers;

trait TransformerAware
{
    abstract public function getTransformer(): ApiTransformer;
}
