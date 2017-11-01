<?php

namespace Napp\Core\Api\Tests\Unit;

use Napp\Core\Api\Exceptions\Exceptions\InvalidFieldException;
use Napp\Core\Api\Exceptions\Exceptions\ValidationException;
use Napp\Core\Api\Requests\Provider\RequestServiceProvider;
use Napp\Tests\stubs\ApiRequestStub;
use Napp\Tests\TestCase;

class ApiRequestTest extends TestCase
{
    /**
     * @var ApiRequestStub
     */
    protected $request;

    public function setUp()
    {
        parent::setUp();
        $container = $this->app->make(\Illuminate\Contracts\Container\Container::class);

        $this->request = new ApiRequestStub();
        $this->request->setContainer($container);
    }

    public function test_required_field()
    {
        $this->expectException(ValidationException::class);

        $this->request->setRules(['name' => 'required']);
        $this->request->setData(['name' => '']);
        $this->request->validate();
    }

    public function test_field_with_wrong_format()
    {
        $this->expectException(ValidationException::class);

        $this->request->setRules(['number' => 'integer']);
        $this->request->setData(['number' => 'some integer']);
        $this->request->validate();
    }

    public function test_invalid_field()
    {
        $this->expectException(InvalidFieldException::class);

        $this->request->setRules(['name' => 'string']);
        $this->request->replace(['name' => 'some name', 'title' => 'some title']);
        $this->request->validate();
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            RequestServiceProvider::class,
        ];
    }
}