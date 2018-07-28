<?php

namespace Napp\Core\Api\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Napp\Core\Api\Tests\Transformers\VariantTransformer;
use Napp\Core\Api\Transformers\ApiTransformer;
use Napp\Core\Api\Transformers\TransformerAware;

class Variant extends Model
{
    use TransformerAware;

    protected $guarded = [];

    /**
     * @var array
     */
    public $apiMapping = [
        'id'         => ['newName' => 'id',         'dataType' => 'int'],
        'name'       => ['newName' => 'title',      'dataType' => 'string'],
        'sku_id'     => ['newName' => 'sku',        'dataType' => 'string'],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return ApiTransformer
     */
    public function getTransformer(): ApiTransformer
    {
        return app(VariantTransformer::class);
    }
}
