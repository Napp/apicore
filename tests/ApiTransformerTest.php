<?php

namespace Napp\Core\Api\Tests\Unit;

use Napp\Core\Api\Transformers\ApiTransformer;
use Napp\Core\Api\Tests\TestCase;

class ApiTransformerTest extends TestCase
{
    /**
     * @var ApiTransformer
     */
    protected $transformer;

    public function setUp()
    {
        parent::setUp();

        $apiMapping = [
            'id' => ['newName' => 'id', 'dataType' => 'int'],
            'name' => ['newName' => 'companyName', 'dataType' => 'string'],
            'has_access' => ['newName' => 'hasAccess', 'dataType' => 'bool'],
            'categories' => ['newName' => 'cats', 'dataType' => 'array'],
        ];

        $this->transformer = new ApiTransformer();
        $this->transformer->setApiMapping($apiMapping);
    }

    public function test_input_transforming()
    {
        $input = [
            'id' => 1,
            'companyName' => 'Wayne Industries',
            'hasAccess' => 0,
            'someAdditionalField' => 'someAdditionalValue',
            'cats' => ['foo' => 'bar']
        ];

        $expectedInput = [
            'id' => 1,
            'name' => 'Wayne Industries',
            'has_access' => 0,
            'someAdditionalField' => 'someAdditionalValue',
            'categories' => ['foo' => 'bar']
        ];
        $transformedInput = $this->transformer->transformInput($input);

        $this->assertEquals($expectedInput, $transformedInput);
    }

    public function test_strict_output_transforming()
    {
        $reflection = new \ReflectionProperty(\get_class($this->transformer), 'strict');
        $reflection->setAccessible(true);
        $reflection->setValue($this->transformer, true);

        $output = [
            'id' => 1,
            'name' => 'Wayne Industries',
            'has_access' => 0,
            'some_additional_field' => 'some_additional_value'
        ];

        $expectedOutput = [
            'id' => 1,
            'companyName' => 'Wayne Industries',
            'hasAccess' => false,
        ];

        $transformedOutput = $this->transformer->transformOutput($output);
        $this->assertEquals($expectedOutput, $transformedOutput);
        $this->assertArrayNotHasKey('some_additional_field', $transformedOutput);
    }

    public function test_output_transforming()
    {
        $output = [
            'id' => 1,
            'name' => 'Wayne Industries',
            'has_access' => 0,
            'some_additional_field' => 'some_additional_value'
        ];

        $expectedOutput = [
            'id' => 1,
            'companyName' => 'Wayne Industries',
            'hasAccess' => false,
            'some_additional_field' => 'some_additional_value'
        ];

        $transformedOutput = $this->transformer->transformOutput($output);

        $this->assertEquals($expectedOutput, $transformedOutput);
    }

    public function test_output_transforming_with_collection()
    {
        $output = collect();
        $output->push([
            'id' => 1,
            'name' => 'Wayne Industries',
            'has_access' => 0,
            'some_additional_field' => 'some_additional_value'
        ]);

        $output->push([
            'id' => 2,
            'name' => 'LexCorp',
            'has_access' => 1,
            'some_additional_field' => 'some_additional_value'
        ]);

        $expectedOutput = [
            [
                'id' => 1,
                'companyName' => 'Wayne Industries',
                'hasAccess' => false,
                'some_additional_field' => 'some_additional_value'
            ],
            [
                'id' => 2,
                'companyName' => 'LexCorp',
                'hasAccess' => true,
                'some_additional_field' => 'some_additional_value'
            ]
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
            'id' => 1,
            'name' => 'Wayne Industries',
            'has_access' => 0,
            'some_additional_field' => 'some_additional_value'
        ]);

        $output->push([
            'id' => 2,
            'name' => 'LexCorp',
            'has_access' => 1,
            'some_additional_field' => 'some_additional_value'
        ]);

        $expectedOutput = [
            [
                'id' => 1,
                'companyName' => 'Wayne Industries',
                'hasAccess' => false,
            ],
            [
                'id' => 2,
                'companyName' => 'LexCorp',
                'hasAccess' => true,
            ]
        ];

        $transformedOutput = $this->transformer->transformOutput($output);

        $this->assertEquals($expectedOutput, $transformedOutput);
    }
}