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

    public function populatePayload(array $payload, $user = null): array
    {
        if (!$user) {
            return $payload;
        }
        if (method_exists($user, 'getId')) {
            $payload['uid'] = $user->getId();
        }
        if (method_exists($user, 'getRoles')) {
            $payload['roles'] = $user->getRoles();
        }
        if (method_exists($user, 'getIdentityId')) {
            $payload['uuid'] = $user->getIdentityId();
        }
        if (method_exists($user, 'getAccess')) {
            $payload['access'] = $user->getAccess();
        }
        if (method_exists($user, 'getSubject')) {
            $payload['sub'] = $user->getSubject();
        }
        return $payload;
    }

    private function payload($user = null, ?int $expire = null): array
    {
        $payload = [
            'exp' => $expire ?? $this->expire(),
        ];
        return $this->populatePayload($payload, $user);
    }

    public function create($user = null, $expire = null): string
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
