<?php

namespace Napp\Core\Api\Tests\Transformers;

use Napp\Core\Api\Tests\Models\Category;
use Napp\Core\Api\Transformers\ApiTransformer;

/**
 * Class CategoryTransformer.
 */
class CategoryTransformer extends ApiTransformer
{
    /**
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->setApiMapping($category);
    }
}
