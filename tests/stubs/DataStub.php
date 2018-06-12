<?php

namespace Napp\Core\Api\Tests\stubs;

use Illuminate\Database\Eloquent\Model;
use Napp\Core\Api\Transformers\ApiTransformer;
use Napp\Core\Api\Transformers\TransformerAware;

class DataStub extends Model
{
    protected $guarded = [];

    use TransformerAware;

    public function getTransformer(): ApiTransformer
    {
        return tap(new ApiTransformer, function (ApiTransformer $transformer) {
            $transformer->setApiMapping([
                'array' => ['newName' => 'items', 'dataType' => 'relationship'],
                'name' => ['newName' => 'title', 'dataType' => 'string']
            ]);
        });
    }
}
