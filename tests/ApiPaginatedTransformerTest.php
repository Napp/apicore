<?php

namespace Napp\Core\Api\Tests\Unit;

use Faker\Factory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Napp\Core\Api\Tests\Models\Category;
use Napp\Core\Api\Tests\TestCase;
use Napp\Core\Api\Tests\Transformers\CategoryStrictTransformer;
use Napp\Core\Api\Transformers\ApiTransformer;

class ApiPaginatedTransformerTest extends TestCase
{
    /**
     * @var ApiTransformer
     */
    protected $transformer;

    /**
     * @var Factory
     */
    protected $faker;

    public function setUp(): void
    {
        parent::setUp();

        $apiMapping = [
            'id'            => ['newName' => 'id', 'dataType' => 'int'],
            'first_name'    => ['newName' => 'firstName', 'dataType' => 'string'],
            'last_name'     => ['newName' => 'lastName', 'dataType' => 'string'],
            'age'           => ['newName' => 'age', 'dataType' => 'int'],
        ];

        $this->transformer = new ApiTransformer();
        $this->transformer->setApiMapping($apiMapping);

        $this->faker = \Faker\Factory::create();
    }

    public function test_transform_paginated_output_data()
    {
        $input = [];

        for ($x = 1; $x <= 10; $x++) {
            $input[] = [
                'id'         => $x,
                'first_name' => $this->faker->firstName,
                'last_name'  => $this->faker->lastName,
                'age'        => $this->faker->numberBetween(20, 80),
            ];
        }

        $paginatedInput = new Paginator($input, count($input));

        $transformedOutput = $this->transformer->transformOutput($paginatedInput);

        $this->assertArrayHasKey('pagination', $transformedOutput);
        $this->assertArrayHasKey('data', $transformedOutput);
        $this->assertArrayHasKey('currentPage', $transformedOutput['pagination']);
        $this->assertArrayHasKey('perPage', $transformedOutput['pagination']);
        $this->assertArrayHasKey('firstPageUrl', $transformedOutput['pagination']);
        $this->assertArrayHasKey('nextPageUrl', $transformedOutput['pagination']);
        $this->assertArrayHasKey('prevPageUrl', $transformedOutput['pagination']);

        foreach ((array) $transformedOutput['data'] as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('firstName', $item);
            $this->assertArrayHasKey('lastName', $item);
            $this->assertArrayHasKey('age', $item);
        }
    }

    public function test_transform_length_aware_paginated_output_data()
    {
        $input = [];

        for ($x = 1; $x <= 10; $x++) {
            $input[] = [
                'id'         => $x,
                'first_name' => $this->faker->firstName,
                'last_name'  => $this->faker->lastName,
                'age'        => $this->faker->numberBetween(20, 80),
            ];
        }

        $paginatedInput = new LengthAwarePaginator($input, count($input) * 4, count($input));

        $transformedOutput = $this->transformer->transformOutput($paginatedInput);

        $this->assertArrayHasKey('pagination', $transformedOutput);
        $this->assertArrayHasKey('data', $transformedOutput);
        $this->assertArrayHasKey('currentPage', $transformedOutput['pagination']);
        $this->assertArrayHasKey('totalPages', $transformedOutput['pagination']);
        $this->assertArrayHasKey('total', $transformedOutput['pagination']);
        $this->assertArrayHasKey('perPage', $transformedOutput['pagination']);
        $this->assertArrayHasKey('firstPageUrl', $transformedOutput['pagination']);
        $this->assertArrayHasKey('lastPageUrl', $transformedOutput['pagination']);
        $this->assertArrayHasKey('nextPageUrl', $transformedOutput['pagination']);
        $this->assertArrayHasKey('prevPageUrl', $transformedOutput['pagination']);

        foreach ((array) $transformedOutput['data'] as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('firstName', $item);
            $this->assertArrayHasKey('lastName', $item);
            $this->assertArrayHasKey('age', $item);
        }
    }

    /** @group me */
    public function test_transform_length_aware_paginated_with_relationships()
    {
        $category = Category::create(['title' => 'Electronics']);
        $category->products()->create(['name' => 'iPhone', 'price'=> 100.0]);
        $category->products()->create(['name' => 'Google Pixel', 'price'=> 80.0]);
        $category->products()->create(['name' => 'Samsung Galaxy 9', 'price'=> 110.0]);

        $category2 = Category::create(['title' => 'Computers']);
        $category2->products()->create(['name' => 'Mac', 'price'=> 28860.0]);
        $category2->products()->create(['name' => 'Windows', 'price'=> 11000.0]);

        $input = Category::with('products')->get();

        $paginatedInput = new LengthAwarePaginator($input, count($input) * 4, count($input));

        $transformedOutput = $category->getTransformer()->transformOutput($paginatedInput);

        $this->assertArrayHasKey('pagination', $transformedOutput);
        $this->assertArrayHasKey('data', $transformedOutput);
        $this->assertArrayHasKey('currentPage', $transformedOutput['pagination']);
        $this->assertArrayHasKey('totalPages', $transformedOutput['pagination']);
        $this->assertArrayHasKey('total', $transformedOutput['pagination']);
        $this->assertArrayHasKey('perPage', $transformedOutput['pagination']);
        $this->assertArrayHasKey('firstPageUrl', $transformedOutput['pagination']);
        $this->assertArrayHasKey('lastPageUrl', $transformedOutput['pagination']);
        $this->assertArrayHasKey('nextPageUrl', $transformedOutput['pagination']);
        $this->assertArrayHasKey('prevPageUrl', $transformedOutput['pagination']);

        $this->assertEquals('iPhone', $transformedOutput['data'][0]['products'][0]['title']);
        $this->assertEquals('Google Pixel', $transformedOutput['data'][0]['products'][1]['title']);
        $this->assertEquals('Mac', $transformedOutput['data'][1]['products'][0]['title']);
        $this->assertEquals('Windows', $transformedOutput['data'][1]['products'][1]['title']);
    }

    public function test_transform_length_aware_paginated_with_relationships_with_strict_mode_on()
    {
        $category = Category::create(['title' => 'Electronics']);
        $category->products()->create(['name' => 'iPhone', 'price'=> 100.0]);
        $category->products()->create(['name' => 'Google Pixel', 'price'=> 80.0]);
        $category->products()->create(['name' => 'Samsung Galaxy 9', 'price'=> 110.0]);

        $category2 = Category::create(['title' => 'Computers']);
        $category2->products()->create(['name' => 'Mac', 'price'=> 28860.0]);
        $category2->products()->create(['name' => 'Windows', 'price'=> 11000.0]);

        $input = Category::with('products')->get();

        $paginatedInput = new LengthAwarePaginator($input, count($input) * 4, count($input));

        $transformedOutput = (new CategoryStrictTransformer())->transformOutput($paginatedInput);

        $this->assertArrayHasKey('pagination', $transformedOutput);
        $this->assertArrayHasKey('data', $transformedOutput);
        $this->assertArrayHasKey('currentPage', $transformedOutput['pagination']);
        $this->assertArrayHasKey('totalPages', $transformedOutput['pagination']);
        $this->assertArrayHasKey('total', $transformedOutput['pagination']);
        $this->assertArrayHasKey('perPage', $transformedOutput['pagination']);
        $this->assertArrayHasKey('firstPageUrl', $transformedOutput['pagination']);
        $this->assertArrayHasKey('lastPageUrl', $transformedOutput['pagination']);
        $this->assertArrayHasKey('nextPageUrl', $transformedOutput['pagination']);
        $this->assertArrayHasKey('prevPageUrl', $transformedOutput['pagination']);

        $this->assertArrayNotHasKey('products', $transformedOutput['data'][0]);
        $this->assertArrayNotHasKey('products', $transformedOutput['data'][1]);
        $this->assertArrayNotHasKey('title', $transformedOutput['data'][0]);
        $this->assertArrayNotHasKey('title', $transformedOutput['data'][1]);

        $this->assertEquals(1, $transformedOutput['data'][0]['id']);
        $this->assertEquals(2, $transformedOutput['data'][1]['id']);
    }
}
