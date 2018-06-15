<?php

namespace Napp\Core\Api\Tests\Unit;

use Napp\Core\Api\Exceptions\Exceptions\InvalidFieldException;
use Napp\Core\Api\Exceptions\Exceptions\ValidationException;
use Napp\Core\Api\Requests\Provider\RequestServiceProvider;
use Napp\Core\Api\Tests\stubs\ApiRequestStub;
use Napp\Core\Api\Tests\TestCase;

class ApiRequestTest extends TestCase
{
    /**
     * @var ApiRequestStub
     */
    protected $request;

    public function setUp()
    {
        parent::setUp();

        $this->request = new ApiRequestStub();
        $this->request->setContainer($this->app);
    }

    public function test_required_field()
    {
        $this->expectException(ValidationException::class);

        $this->request->setRules(['name' => 'required']);
        $this->request->setData(['name' => '']);

        // Laravel 5.6 changed the method validate method on the FormRequest to validateResolved.
        if (true === version_compare($this->app->version(), '5.6', '>=')) {
            $this->request->validateResolved();
            return;
        }

        $this->request->validate();
    }

    public function test_field_with_wrong_format()
    {
        $this->expectException(ValidationException::class);

        $this->request->setRules(['number' => 'integer']);
        $this->request->setData(['number' => 'some integer']);

        // Laravel 5.6 changed the method validate method on the FormRequest to validateResolved.
        if (true === version_compare($this->app->version(), '5.6', '>=')) {
            $this->request->validateResolved();
            return;
        }

        $this->request->validate();
    }

    public function test_invalid_field()
    {
        $this->expectException(InvalidFieldException::class);

        $this->request->setRules(['name' => 'string']);
        $this->request->replace(['name' => 'some name', 'title' => 'some title']);

        // Laravel 5.6 changed the method validate method on the FormRequest to validateResolved.
        if (true === version_compare($this->app->version(), '5.6', '>=')) {
            $this->request->validateResolved();
            return;
        }

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