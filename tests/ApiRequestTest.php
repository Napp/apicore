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

    public function setUp(): void
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
        $this->request->validateResolved();
    }

    public function test_field_with_correct_validation_object()
    {
        $this->request->setRules(['number' => 'integer']);
        $this->request->setData(['number' => 'some integer']);

        try {
            $this->request->validateResolved();
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('number', $exception->validation);
            $this->assertEquals('The number must be an integer.', $exception->validation['number'][0]);

            return;
        }

        $this->fail('fails');
    }

    public function test_field_with_required_validation_object_multiple()
    {
        $this->request->setRules(['number' => 'required|integer', 'email' => 'required|email']);

        try {
            $this->request->validateResolved();
        } catch (ValidationException $exception) {
            $this->assertCount(2, $exception->validation);
            $this->assertArrayHasKey('email', $exception->validation);
            $this->assertEquals('The email field is required.', $exception->validation['email'][0]);

            return;
        }

        $this->fail('fails');
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
     * @param \Illuminate\Foundation\Application $app
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
