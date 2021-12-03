<?php

namespace FreedomSex\Services;

use Firebase\JWT\JWT;

class JWTManager
{
    const TLL = 60 * 60;
    const ALG = 'RS256';

    public function __construct($secret_key, $public_key, $token_ttl = null, $pass_phrase = null)
    {
        $this->secret_key = $secret_key;
        $this->public_key = $public_key;
        $this->pass_phrase = $pass_phrase;
        $this->token_ttl = $token_ttl ?? self::TLL;
    }

    public function expire(): int
    {
        return time() + $this->token_ttl;
    }

    private function payload($user, $expire): array
    {
        $result = [
            'exp' => $expire ?? $this->expire(),
        ];
        if (method_exists($user, 'getId')) {
            $result['uid'] = $user->getId();
        }
        if (method_exists($user, 'getRoles')) {
            $result['roles'] = $user->getRoles();
        }
        if (method_exists($user, 'getIdentityId')) {
            $result['uuid'] = $user->getIdentityId();
        }
        if (method_exists($user, 'getAccess')) {
            $result['access'] = $user->getAccess();
        }
        if (method_exists($user, 'getSubject')) {
            $result['sub'] = $user->getSubject();
        }
        return $result;
    }

    public function create($user, $expire = null): string
    {
        $payload = $this->payload($user, $expire);
        $privateKey = file_get_contents($this->secret_key);
        if (!$privateKey) {
            throw new \RuntimeException('No JWT private Key');
        }
        return JWT::encode($payload, $privateKey, self::ALG);
    }

    public function load($token): object
    {
        $publicKey = file_get_contents($this->public_key);
        if (!$publicKey) {
            throw new \RuntimeException('No JWT public Key');
        }
        return JWT::decode($token, $publicKey, [self::ALG]);
    }

}
