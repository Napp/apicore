<?php

namespace Napp\Tests\stubs;

use Napp\Core\Api\Requests\ApiRequest;
use Napp\Core\Api\Transformers\ApiTransformer;
use Napp\Core\Api\Transformers\TransformerInterface;

class ApiRequestStub extends ApiRequest
{
    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param array $rules
     * @return void
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param array $data
     * @return void
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return $this->rules;
    }

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    protected function validationData(): array
    {
        return $this->data;
    }

    /**
     * @return TransformerInterface
     */
    protected function getTransformer(): TransformerInterface
    {
        return app(ApiTransformer::class);
    }
}
