<?php

namespace Napp\Core\Api\Tests\Unit;

use Napp\Core\Api\Tests\stubs\DataStub;
use Napp\Core\Api\Transformers\ApiTransformer;
use Napp\Core\Api\Tests\TestCase;

class ApiTransformerTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application   $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        /** @var $app \Illuminate\Foundation\Application */
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('database.default', 'testing');
    }

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

    public function test_the_datatype_is_nullable()
    {
        $this->transformer->setApiMapping([
            'price' => ['newName' => 'price', 'dataType' => 'nullable|int']
        ]);

        $input = [
            'price' => '100'
        ];

        $expectedOutput = [
            'price' => 100
        ];

        $this->assertSame($expectedOutput, $this->transformer->transformOutput($input));

        $input = [
            'price' => 0
        ];

        $expectedOutput = [
            'price' => 0
        ];

        $this->assertSame($expectedOutput, $this->transformer->transformOutput($input));

        $this->transformer->setApiMapping([
            'description' => ['newName' => 'description', 'dataType' => 'array|nullable']
        ]);

        $input = [
            'description' => []
        ];

        $expectedOutput = [
            'description' => null
        ];

        $this->assertSame($expectedOutput, $this->transformer->transformOutput($input));
    }

    public function test_arguments_can_be_passed_to_the_datatype()
    {
        $this->transformer->setApiMapping([
            'price' => ['newName' => 'price', 'dataType' => 'float:2']
        ]);

        $input = [
            'price' => '100.5542'
        ];

        $expectedOutput = [
            'price' => 100.55
        ];

        $this->assertSame($expectedOutput, $this->transformer->transformOutput($input));
    }

    public function test_it_transforms_instances_if_transformeraware_interfaces()
    {
        $this->transformer->setApiMapping([
            'title' => ['newName' => 'title', 'dataType' => 'string'],
            'relationship' => ['newName' => 'some-relationship', 'dataType' => 'relationship'],
            'testing-relationship' => ['newName' => 'new-relationship', 'dataType' => 'relationship']
        ]);

        $input = [
            'title' => 'testing-123',
            'relationship' => collect([
                new DataStub([
                    'title' => 'string',
                    'array' => [
                        [
                            'name' => 'some-name'
                        ],
                        [
                            'name' => 'some-name'
                        ]
                    ]
                ]),
                new DataStub([
                    'title' => 'string',
                    'array' => [
                        [
                            'name' => 'some-name'
                        ],
                        [
                            'name' => 'some-name'
                        ]
                    ]
                ])
            ]),
            'testing-relationship' => new DataStub([
                'title' => 'string',
                'array' => [
                    [
                        'name' => 'some-name'
                    ],
                    [
                        'name' => 'some-name'
                    ]
                ]
            ]),
        ];

        $output = $this->transformer->transformOutput($input);

        $this->assertEquals([
            'title' => 'testing-123',
            'some-relationship' => [
                [
                    'title' => 'string',
                    'items' => [
                        ['title' => 'some-name'],
                        ['title' => 'some-name'],
                    ]
                ],
                [
                    'title' => 'string',
                    'items' => [
                        ['title' => 'some-name'],
                        ['title' => 'some-name'],
                    ]
                ],
            ],
            'new-relationship' => [
                'title' => 'string',
                'items' => [
                    ['title' => 'some-name'],
                    ['title' => 'some-name'],
                ]
            ]
        ], $output);
    }
}
