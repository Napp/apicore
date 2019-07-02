<?php

namespace Napp\Core\Api\Tests\Unit;

use Napp\Core\Api\Tests\Models\Category;
use Napp\Core\Api\Tests\Models\Post;
use Napp\Core\Api\Tests\Models\Product;
use Napp\Core\Api\Tests\TestCase;
use Napp\Core\Api\Tests\Transformers\CategoryStrictTransformer;
use Napp\Core\Api\Tests\Transformers\CategoryTransformerWithDifferentOutputKey;
use Napp\Core\Api\Tests\Transformers\PostTransformer;
use Napp\Core\Api\Tests\Transformers\ProductTransformer;
use Napp\Core\Api\Transformers\ApiTransformer;

class ApiTransformerTest extends TestCase
{
    /**
     * @var ApiTransformer
     */
    protected $transformer;

    public function setUp(): void
    {
        parent::setUp();

        $apiMapping = [
            'id'         => ['newName' => 'id', 'dataType' => 'int'],
            'name'       => ['newName' => 'companyName', 'dataType' => 'string'],
            'has_access' => ['newName' => 'hasAccess', 'dataType' => 'bool'],
            'categories' => ['newName' => 'cats', 'dataType' => 'array'],
            'price'      => ['newName' => 'price', 'dataType' => 'float'],
        ];

        $this->transformer = new ApiTransformer();
        $this->transformer->setApiMapping($apiMapping);
    }

    public function test_input_transforming()
    {
        $input = [
            'id'                  => 1,
            'companyName'         => 'Wayne Industries',
            'hasAccess'           => 0,
            'someAdditionalField' => 'someAdditionalValue',
            'cats'                => ['foo' => 'bar'],
            'price'               => '1000',
        ];

        $expected = [
            'id'                  => 1,
            'name'                => 'Wayne Industries',
            'has_access'          => 0,
            'someAdditionalField' => 'someAdditionalValue',
            'categories'          => ['foo' => 'bar'],
            'price'               => (float) 1000,
        ];
        $transformedInput = $this->transformer->transformInput($input);

        $this->assertEquals($expected, $transformedInput);
    }

    public function test_strict_output_transforming()
    {
        $reflection = new \ReflectionProperty(\get_class($this->transformer), 'strict');
        $reflection->setAccessible(true);
        $reflection->setValue($this->transformer, true);

        $output = [
            'id'                    => 1,
            'name'                  => 'Wayne Industries',
            'has_access'            => 0,
            'some_additional_field' => 'some_additional_value',
        ];

        $expectedOutput = [
            'id'          => 1,
            'companyName' => 'Wayne Industries',
            'hasAccess'   => false,
        ];

        $transformedOutput = $this->transformer->transformOutput($output);
        $this->assertEquals($expectedOutput, $transformedOutput);
        $this->assertArrayNotHasKey('some_additional_field', $transformedOutput);
    }

    public function test_output_transforming()
    {
        $output = [
            'id'                    => 1,
            'name'                  => 'Wayne Industries',
            'has_access'            => 0,
            'some_additional_field' => 'some_additional_value',
            'price'                 => '1000',
        ];

        $expectedOutput = [
            'id'                    => 1,
            'companyName'           => 'Wayne Industries',
            'hasAccess'             => false,
            'some_additional_field' => 'some_additional_value',
            'price'                 => (float) 1000,
        ];

        $transformedOutput = $this->transformer->transformOutput($output);

        $this->assertEquals($expectedOutput, $transformedOutput);
    }

    public function test_output_transforming_with_collection()
    {
        $output = collect();
        $output->push([
            'id'                    => 1,
            'name'                  => 'Wayne Industries',
            'has_access'            => 0,
            'some_additional_field' => 'some_additional_value',
        ]);

        $output->push([
            'id'                    => 2,
            'name'                  => 'LexCorp',
            'has_access'            => 1,
            'some_additional_field' => 'some_additional_value',
        ]);

        $expectedOutput = [
            [
                'id'                    => 1,
                'companyName'           => 'Wayne Industries',
                'hasAccess'             => false,
                'some_additional_field' => 'some_additional_value',
            ],
            [
                'id'                    => 2,
                'companyName'           => 'LexCorp',
                'hasAccess'             => true,
                'some_additional_field' => 'some_additional_value',
            ],
        ];

        $transformedOutput = $this->transformer->transformOutput($output);

        $this->assertEquals($expectedOutput, $transformedOutput);
    }

    public function test_output_transforming_with_collection_strict_mode()
    {
        $reflection = new \ReflectionProperty(\get_class($this->transformer), 'strict');
        $reflection->setAccessible(true);
        $reflection->setValue($this->transformer, true);

        $output = collect();
        $output->push([
            'id'                    => 1,
            'name'                  => 'Wayne Industries',
            'has_access'            => 0,
            'some_additional_field' => 'some_additional_value',
        ]);

        $output->push([
            'id'                    => 2,
            'name'                  => 'LexCorp',
            'has_access'            => 1,
            'some_additional_field' => 'some_additional_value',
        ]);

        $expectedOutput = [
            [
                'id'          => 1,
                'companyName' => 'Wayne Industries',
                'hasAccess'   => false,
            ],
            [
                'id'          => 2,
                'companyName' => 'LexCorp',
                'hasAccess'   => true,
            ],
        ];

        $transformedOutput = $this->transformer->transformOutput($output);

        $this->assertEquals($expectedOutput, $transformedOutput);
    }

    public function test_the_datatype_is_nullable()
    {
        $this->transformer->setApiMapping([
            'price' => ['newName' => 'price_new', 'dataType' => 'nullable|int'],
        ]);

        $input = [
            'price' => '100',
        ];

        $expectedOutput = [
            'price_new' => 100,
        ];

        $this->assertSame($expectedOutput, $this->transformer->transformOutput($input));

        $input = [
            'price' => 0,
        ];

        $expectedOutput = [
            'price_new' => 0,
        ];

        $this->assertSame($expectedOutput, $this->transformer->transformOutput($input));

        $this->transformer->setApiMapping([
            'description' => ['newName' => 'description', 'dataType' => 'array|nullable'],
        ]);

        $input = [
            'description' => [],
        ];

        $expectedOutput = [
            'description' => null,
        ];

        $this->assertSame($expectedOutput, $this->transformer->transformOutput($input));
    }

    public function test_arguments_can_be_passed_to_the_datatype()
    {
        $this->transformer->setApiMapping([
            'price' => ['newName' => 'price', 'dataType' => 'float:2'],
        ]);

        $input = [
            'price' => '100.5542',
        ];

        $expectedOutput = [
            'price' => 100.55,
        ];

        $this->assertSame($expectedOutput, $this->transformer->transformOutput($input));
    }

    public function test_transform_model_hasMany_relation_returns_transformed_relation_with_it()
    {
        /** @var Category $category */
        $category = Category::create(['title' => 'Electronics']);
        $category->products()->create(['name' => 'iPhone', 'price'=> 100.0]);
        $category->load('products');
        $result = $category->getTransformer()->transformOutput($category);

        $this->assertArrayHasKey('products', $result);
        $this->assertEquals('iPhone', $result['products'][0]['title']);
    }

    public function test_empty_relation_returns_null()
    {
        /** @var Category $category */
        $category = Category::create(['title' => 'Electronics']);
        $category->load('products');
        $result = $category->getTransformer()->transformOutput($category);
        $this->assertNull($result['products']);
    }

    public function test_without_relation_loaded_returns_only_transformed_base_model()
    {
        /** @var Category $category */
        $category = Category::create(['title' => 'Electronics']);
        $result = $category->getTransformer()->transformOutput($category);

        $this->assertArrayNotHasKey('products', $result);
    }

    public function test_transform_collection_with_belongsTo_relation_transforms()
    {
        $category = Category::create(['title' => 'Electronics']);
        $category->products()->create(['name' => 'iPhone', 'price'=> 100.0]);
        $category->products()->create(['name' => 'Google Pixel', 'price'=> 80.0]);
        $category->products()->create(['name' => 'Samsung Galaxy 9', 'price'=> 110.0]);

        $products = Product::with('category')->get();
        $result = app(ProductTransformer::class)->transformOutput($products);

        $this->assertEquals('iPhone', $result[0]['title']);
        $this->assertEquals('Electronics', $result[0]['category']['name']);
        $this->assertEquals('Electronics', $result[1]['category']['name']);
        $this->assertEquals('Electronics', $result[2]['category']['name']);

        $category->load('products');
        $result = $category->getTransformer()->transformOutput($category);
        $this->assertCount(3, $result['products']);
    }

    public function test_transform_deeply_nested_relationships()
    {
        $category = Category::create(['title' => 'Electronics']);
        $category->products()->create(['name' => 'iPhone', 'price'=> 100.0])->variants()->create(['name' => 'iPhone 8', 'sku_id' => 'IPHONE2233']);
        $category->load(['products', 'products.variants']);
        $result = $category->getTransformer()->transformOutput($category);

        $this->assertEquals('iPhone 8', $result['products'][0]['variants'][0]['title']);
    }

    public function test_transform_not_strict_model()
    {
        $post = Post::create([
            'title'      => 'My First post',
            'desc'       => 'body text',
            'tags'       => [2, '222', 'wow'],
            'other_tags' => null,
            'owner'      => 34,
            'uuid'       => '66220588-c944-3425-a3ea-0fc80f8c32fe',
        ]);
        $result = app(PostTransformer::class)->transformOutput($post);

        $this->assertArrayNotHasKey('updated_at', $result);
        $this->assertEquals(3, count($result['tags']));
        $this->assertNull($result['otherTags']);
    }

    public function test_transform_model_relations_is_exluded_if_not_found_in_transform_map_and_strict_mode_is_enabled()
    {
        /** @var Category $category */
        $category = Category::create(['title' => 'Electronics']);
        $category->products()->create(['name' => 'iPhone', 'price'=> 100.0]);
        $category->load('products');

        $result = (new CategoryStrictTransformer())->transformOutput($category);

        $this->assertArrayNotHasKey('products', $result);
    }

    public function test_transform_model_relations_with_different_output_key()
    {
        /** @var Category $category */
        $category = Category::create(['title' => 'Electronics']);
        $category->products()->create(['name' => 'iPhone', 'price'=> 100.0]);
        $category->load('products');

        $result = (new CategoryTransformerWithDifferentOutputKey())->transformOutput($category);

        $this->assertArrayNotHasKey('products', $result);
        $this->assertArrayHasKey('indexes', $result);
    }
}
