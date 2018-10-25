<?php 

namespace Napp\Core\Api\Tests\Transformers;

use Napp\Core\Api\Tests\Models\Post;
use Napp\Core\Api\Transformers\ApiTransformer;

/**
 * Class PostTransformer
 * @package Napp\Core\Api\Tests\Transformers
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