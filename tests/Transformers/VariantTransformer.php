<?php

namespace Napp\Core\Api\Tests\Transformers;

use Napp\Core\Api\Tests\Models\Variant;
use Napp\Core\Api\Transformers\ApiTransformer;

/**
 * Class VariantTransformer.
 */
class VariantTransformer extends ApiTransformer
{
    /**
     * @param Variant $variant
     */
    public function __construct(Variant $variant)
    {
        $this->setApiMapping($variant);
    }
}
