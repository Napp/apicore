<?php

namespace Napp\Core\Api\Auth;

use Illuminate\Http\Request;
use Illuminate\Auth\TokenGuard;
use Illuminate\Contracts\Auth\UserProvider;

/**
 * Class ApiGuard.
 */
class ApiGuard extends TokenGuard
{
    /**
     * @param UserProvider $provider
     * @param Request      $request
     */
    public function __construct(UserProvider $provider, Request $request)
    {
        parent::__construct($provider, $request);

        $this->storageKey = 'api_key';
    }

    /**
     * @param array $credentials
     *
     * @return bool
     */
    public function attempt(array $credentials = []): bool
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if (null === $user) {
            return false;
        }

        if (true === $this->provider->validateCredentials($user, $credentials)) {
            $this->setUser($user);

            return true;
        }

        return false;
    }

    /**
     * @return string|null
     */
    public function getTokenForRequest(): ?string
    {
        return $this->request->header(NappHttpHeaders::NAPP_API_KEY);
    }
}
