<?php

namespace Napp\Core\Api\Tests\Transformers;

use Napp\Core\Api\Transformers\ApiTransformer;

class CategoryTransformerWithDifferentOutputKey extends ApiTransformer
{
    public function __construct()
    {
        $this->setApiMapping([
            'id'         => ['newName' => 'id',         'dataType' => 'int'],
            'title'      => ['newName' => 'name',       'dataType' => 'string'],
            'products'   => ['newName' => 'indexes',     'dataType' => 'array'],
        ]);
    }
}
