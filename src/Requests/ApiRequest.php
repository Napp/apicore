<?php

namespace Napp\Core\Api\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Napp\Core\Api\Exceptions\Exceptions\ApiInternalCallValidationException;
use Napp\Core\Api\Exceptions\Exceptions\InvalidFieldException;
use Napp\Core\Api\Exceptions\Exceptions\ValidationException;
use Napp\Core\Api\Transformers\ApiTransformer;
use Napp\Core\Api\Transformers\TransformerInterface;

abstract class ApiRequest extends FormRequest
{
    /**
     * @return Validator
     */
    protected function getValidatorInstance()
    {
        $this->replace($this->transformInput());
        $this->validateInputFields();

        return parent::getValidatorInstance();
    }

    /**
     * @param Validator $validator
     * @return void
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        if (false === $this->isApiInternalCall()) {
            $this->handleApiCallFailedValidation($validator);
        } else {
            $this->handleApiInternalCallFailedValidation($validator);
        }
    }

    /**
     * @return void
     * @throws InvalidFieldException
     */
    protected function validateInputFields()
    {
        $input = $this->input();
        $rules = $this->rules();
        if (false === empty(array_diff_key($input, $rules))) {
            $exception = new InvalidFieldException;
            $exception->statusMessage = $exception->statusMessage . ': ' . implode(',', array_keys(array_diff_key($input, $rules)));

            throw $exception;
        }
    }

    /**
     * @return array
     */
    protected function transformInput(): array
    {
        /**
         * Remove input fields like _method, _token, etc.
         */
        $input = array_filter($this->input(), function ($key) {
            return !starts_with($key, '_');
        }, ARRAY_FILTER_USE_KEY);

        return $this->getTransformer()->transformInput($input);
    }

    /**
     * @return TransformerInterface
     */
    protected function getTransformer(): TransformerInterface
    {
        return app(ApiTransformer::class);
    }

    /**
     * @see AppServiceProvider
     * @return bool
     */
    protected function isApiInternalCall(): bool
    {
        $request = request();
        if (true === method_exists($request, 'isApiInternalCall')) {
            return $request->isApiInternalCall();
        }

        return false;
    }

    /**
     * @param Validator $validator
     * @return void
     * @throws ValidationException
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function handleApiCallFailedValidation(Validator $validator)
    {
        $message = $validator->messages()->first();
        $exception = new ValidationException();
        $exception->statusMessage = $exception->statusMessage . ': ' . $message;

        throw $exception;
    }

    /**
     * @param Validator $validator
     * @return void
     * @throws ApiInternalCallValidationException
     */
    protected function handleApiInternalCallFailedValidation(Validator $validator)
    {
        $input = $this->getTransformer()->transformOutput($this->except($this->dontFlash));
        $errors = collect($this->getTransformer()->transformOutput($validator->getMessageBag()->toArray()))
            ->reject(function ($error) {
                return false === is_array($error);
            })
            ->toArray();

        throw new ApiInternalCallValidationException($input, $errors);
    }

    /**
     * @param array $input
     * @param string $key
     * @return bool
     */
    protected function isValueSet(array $input, string $key): bool
    {
        return (true === isset($input[$key]) && false === empty($input[$key]));
    }
}
