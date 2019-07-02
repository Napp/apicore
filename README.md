# Napp API Core

[![Build Status](https://travis-ci.org/Napp/apicore.svg?branch=master)](https://travis-ci.org/Napp/apicore)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Napp/apicore/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Napp/apicore/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Napp/apicore/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Napp/apicore/?branch=master)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

Use as a foundation for APIs.

## Features

* Full request cycle 
  * APU auth guard 
  * Transform request input based on model api mapping
  * Validate transformed data
  * Transform response output (with support for nested relationships using `TransformAware`)
  * Correct HTTP responses backed into ApiController
* Exception handling with two renderers (dev and prod)
* Standard Exceptions
* ETag middleware for cache responses (Not Modified 304)
* Internal Router for internal api requests
* API Proxy to use easy request handling


## Usage

### Transform Mapping

Being able to hide database fields from the outside exposed API. With auto type casting.

```php
<?php

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * @var array
     */
    public $apiMapping = [
        'id'          => ['newName' => 'id',        'dataType' => 'int'],
        'image'       => ['newName' => 'image',     'dataType' => 'string'],
        'title'       => ['newName' => 'name',      'dataType' => 'string'],
        'description' => ['newName' => 'desc',      'dataType' => 'string'],
        'created_at'  => ['newName' => 'createdAt', 'dataType' => 'datetime'],
        'published'   => ['newName' => 'published', 'dataType' => 'boolean'],
    ];
}

```


### Factory

Using a factory pattern.

```php
<?php

namespace App\Posts\Factory;

use App\Posts\Models\Post;
use Napp\Core\Api\Validation\ValidateTrait;

class PostFactory
{
    use ValidateTrait;

    /**
     * @param array $attributes
     * @param bool $validate
     * @return Post
     * @throws \Napp\Core\Api\Exceptions\Exceptions\ValidationException
     */
    public static function create(array $attributes, $validate = true): Post
    {
        if (true === $validate) {
            static::validate($attributes, PostValidationRules::$createRules);
        }

        return new Post($attributes);
    }
}


```


### Requests

Extending the `ApiRequest` will automatically transform the input and validate it if Laravel rules are defined.  

```php
<?php

namespace App\Posts\Request;

use App\Posts\Factory\PostValidationRules;
use App\Posts\Transformer\PostTransformer;
use Napp\Core\Api\Requests\ApiRequest;
use Napp\Core\Api\Transformers\TransformerInterface;

class StorePostRequest extends ApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return PostTransformer::$createRules;
    }

    /**
     * @return TransformerInterface
     */
    protected function getTransformer(): TransformerInterface
    {
        return app(PostTransformer::class);
    }
}
```


### API Controllers

API Controllers can use the requests, the factory for creating a model, transforming the output and finally deliver the correct reponse. 

```php
<?php

namespace App\Posts\Controllers\Api;

use App\Posts\Factory\PostFactory;
use App\Posts\Transformer\PostTransformer;
use Napp\Core\Api\Controllers\ApiController;

class PostController extends ApiController
{
    public function show(int $id, Request $request, PostTransformer $transformer): JsonResponse
    {
        $post = $this->postRepository->find($id);
        if (null === $post) {
            return $this->responseNotFound();
        }

        return $this->respond($transformer->transformOutput($post));
    }
    
    public function store(StorePostRequest $request, PostTransformer $transformer): JsonResponse
    {
        if (/* some logic */) {
            return $this->responseUnauthorized();
        }
        
        $post = PostFactory::create($request->validated(), false);

        return $this->responseCreated($transformer->transformOutput($post));
    }
}

```


### Internal router

Using the Internal router to request APIs. 

```php
<?php

use Napp\Core\Api\Controllers\ApiInternalController;

class MyController extends ApiInternalController
{
    public function someImportantAction()
    {
        // using API get/post/put/delete
        $data = $this->get('/api/v1/some/route');
        $stuff = $this->post('/api/v1/new/stuff', $data);
                
        return view('my.view', compact('stuff'));
    }
}

```

