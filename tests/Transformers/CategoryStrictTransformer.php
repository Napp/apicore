<?php

namespace Napp\Core\Api\Tests\Transformers;

use Napp\Core\Api\Transformers\ApiTransformer;

class CategoryStrictTransformer extends ApiTransformer
{
    protected $strict = true;

    public function __construct()
    {
        $this->setApiMapping([
            'id'         => ['newName' => 'id',         'dataType' => 'int'],
        ]);
    }
}
