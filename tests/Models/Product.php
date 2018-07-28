<?php

namespace Napp\Core\Api\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Napp\Core\Api\Tests\Transformers\ProductTransformer;
use Napp\Core\Api\Transformers\ApiTransformer;
use Napp\Core\Api\Transformers\TransformerAware;

class Product extends Model
{
    use TransformerAware;

    protected $guarded = [];

    /**
     * @var array
     */
    public $apiMapping = [
        'id'         => ['newName' => 'id',         'dataType' => 'int'],
        'name'       => ['newName' => 'title',      'dataType' => 'string'],
        'price'      => ['newName' => 'price',      'dataType' => 'float'],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variants()
    {
        return $this->hasMany(Variant::class);
    }

    /**
     * @return ApiTransformer
     */
    public function getTransformer(): ApiTransformer
    {
        return app(ProductTransformer::class);
    }
}
