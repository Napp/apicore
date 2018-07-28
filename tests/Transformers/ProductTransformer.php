<?php 

namespace Napp\Core\Api\Tests\Transformers;

use Napp\Core\Api\Tests\Models\Product;
use Napp\Core\Api\Transformers\ApiTransformer;

/**
 * Class ProductTransformer
 * @package Napp\Core\Api\Tests\Transformers
 */
class ProductTransformer extends ApiTransformer
{
    protected $strict = true;
    /**
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->setApiMapping($product);
    }
}