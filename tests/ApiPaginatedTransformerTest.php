<?php

namespace Napp\Core\Api\Tests\Unit;

use Faker\Factory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Napp\Core\Api\Transformers\ApiTransformer;
use Napp\Core\Api\Tests\TestCase;

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

    public function setUp()
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
                'id' => $x,
                'first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
                'age' => $this->faker->numberBetween(20, 80),
            ];
        }

        $paginatedInput = new Paginator($input, count($input));

        $transformedOutput = $this->transformer->transformOutput($paginatedInput);
        $this->assertArrayHasKey('current_page', $transformedOutput);
        $this->assertArrayHasKey('data', $transformedOutput);
        $this->assertArrayHasKey('first_page_url', $transformedOutput);
        $this->assertArrayHasKey('from', $transformedOutput);
        $this->assertArrayHasKey('next_page_url', $transformedOutput);
        $this->assertArrayHasKey('path', $transformedOutput);
        $this->assertArrayHasKey('per_page', $transformedOutput);
        $this->assertArrayHasKey('prev_page_url', $transformedOutput);
        $this->assertArrayHasKey('to', $transformedOutput);

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
                'id' => $x,
                'first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
                'age' => $this->faker->numberBetween(20, 80),
            ];
        }

        $paginatedInput = new LengthAwarePaginator($input, count($input) * 4, count($input));

        $transformedOutput = $this->transformer->transformOutput($paginatedInput);

        $this->assertArrayHasKey('current_page', $transformedOutput);
        $this->assertArrayHasKey('data', $transformedOutput);
        $this->assertArrayHasKey('first_page_url', $transformedOutput);
        $this->assertArrayHasKey('from', $transformedOutput);
        $this->assertArrayHasKey('last_page', $transformedOutput);
        $this->assertArrayHasKey('last_page_url', $transformedOutput);
        $this->assertArrayHasKey('next_page_url', $transformedOutput);
        $this->assertArrayHasKey('path', $transformedOutput);
        $this->assertArrayHasKey('per_page', $transformedOutput);
        $this->assertArrayHasKey('prev_page_url', $transformedOutput);
        $this->assertArrayHasKey('to', $transformedOutput);
        $this->assertArrayHasKey('total', $transformedOutput);

        foreach ((array) $transformedOutput['data'] as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('firstName', $item);
            $this->assertArrayHasKey('lastName', $item);
            $this->assertArrayHasKey('age', $item);
        }
    }

}