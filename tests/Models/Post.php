<?php

namespace Napp\Core\Api\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [
        'uuid',
    ];

    protected $dates = [
        'created_at',
    ];

    protected $casts = [
        'tags' => 'json',
    ];

    protected $hidden = [
        'updated_at',
    ];

    public $apiMapping = [
        'id'         => ['newName' => 'id',         'dataType' => 'int'],
        'title'      => ['newName' => 'name',       'dataType' => 'string'],
        'desc'       => ['newName' => 'desc',       'dataType' => 'string'],
        'tags'       => ['newName' => 'tags',       'dataType' => 'array'],
        'other_tags' => ['newName' => 'otherTags',  'dataType' => 'nullable[array'],
        'owner'      => ['newName' => 'owner_id',   'dataType' => 'nullable[int'],
        'created_at' => ['newName' => 'postedAt',   'dataType' => 'datetime'],
    ];
}
