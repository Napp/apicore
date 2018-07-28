<?php

namespace Napp\Core\Api\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Napp\Core\Api\Tests\Transformers\CategoryTransformer;
use Napp\Core\Api\Transformers\ApiTransformer;
use Napp\Core\Api\Transformers\TransformerAware;

class Category extends Model
{
    use TransformerAware;

    protected $guarded = [];

    /**
     * @var array
     */
    public $apiMapping = [
        'id'         => ['newName' => 'id',         'dataType' => 'int'],
        'title'      => ['newName' => 'name',       'dataType' => 'string'],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return ApiTransformer
     */
    public function getTransformer(): ApiTransformer
    {
        return app(CategoryTransformer::class);
    }
}
