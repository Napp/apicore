<?php

namespace Napp\Api\Validation;

use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Contracts\Validation\Validator;
use Napp\Api\Exceptions\Exceptions\ValidationException;

trait ValidateTrait
{
    /**
     * @param array $attributes
     * @param array $rules
     * @return void
     * @throws ValidationException
     */
    public static function validate(array $attributes, array $rules)
    {
        /** @var Validator $validator */
        $validator = static::getValidatorFactory()->make($attributes, $rules);
        if (true === $validator->fails()) {
            $message = $validator->messages()->first();

            $exception = new ValidationException;
            $exception->statusMessage = $exception->statusMessage . ': ' . $message;

            throw $exception;
        }
    }

    /**
     * @return ValidatorFactory
     */
    protected static function getValidatorFactory()
    {
        return app(ValidatorFactory::class);
    }
}
