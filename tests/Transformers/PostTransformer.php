<?php

namespace Napp\Core\Api\Tests\Transformers;

use Napp\Core\Api\Tests\Models\Post;
use Napp\Core\Api\Transformers\ApiTransformer;

/**
 * Class PostTransformer.
 */
class PostTransformer extends ApiTransformer
{
    /**
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->setApiMapping($post);
    }
}
