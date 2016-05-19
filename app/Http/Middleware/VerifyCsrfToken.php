<?php

namespace Cd\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    /**
     * The URIs that should be included for drupal token verification
     * 
     * @var array
     */
    protected $drupal = [
        'opportunities',
        'opportunities/*'
    ];

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        // If request url is in $drupal array then use drupal token verifier
        foreach ($this->drupal as $drupal) {
            if ($drupal !== '/') {
                $drupal = trim($drupal, '/');
            }

            if ($request->is($drupal)) {
                return drupalTokensMatch($request);
            }
        }

        $sessionToken = $request->session()->token();

        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

        if (! $token && $header = $request->header('X-XSRF-TOKEN')) {
            $token = $this->encrypter->decrypt($header);
        }

        if (! is_string($sessionToken) || ! is_string($token)) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }
}
