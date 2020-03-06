<?php

namespace Napp\Core\Api\Validation;

use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Contracts\Validation\Validator;
use Napp\Core\Api\Exceptions\Exceptions\ValidationException;

/**
 * Trait ValidateTrait.
 */
trait ValidateTrait
{
    /**
     * @param array $attributes
     * @param array $rules
     *
     * @throws ValidationException
     *
     * @return void
     */
    public static function validate(array $attributes, array $rules)
    {
        /** @var Validator $validator */
        $validator = static::getValidatorFactory()->make($attributes, $rules);
        if (true === $validator->fails()) {
            $message = $validator->messages()->first();

            $exception = new ValidationException();
            $exception->statusMessage = $exception->statusMessage . ': ' . $message;
            $exception->validation = $validator->messages();

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
