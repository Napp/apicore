<?php

namespace Napp\Core\Api\Tests\Transformers;

use Napp\Core\Api\Tests\Models\Product;
use Napp\Core\Api\Transformers\ApiTransformer;

/**
 * Class ProductTransformer.
 */
class ProductTransformer extends ApiTransformer
{
    /**
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->setApiMapping($product);
    }
}
