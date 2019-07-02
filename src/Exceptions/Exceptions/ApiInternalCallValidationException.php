<?php

namespace Napp\Core\Api\Exceptions\Exceptions;

/**
 * Class ApiInternalCallValidationException.
 */
class ApiInternalCallValidationException extends \Exception
{
    /**
     * @var array
     */
    protected $input;

    /**
     * @var array
     */
    protected $errors;

    /**
     * @param array $input
     * @param array $errors
     */
    public function __construct(array $input, array $errors)
    {
        parent::__construct();

        $this->input = $input;
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
