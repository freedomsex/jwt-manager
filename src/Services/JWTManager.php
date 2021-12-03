<?php

namespace FreedomSex\Services;

use Firebase\JWT\JWT;

use FreedomSex\User\MinimalUserInterface;

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

    /**
     * @return null
     */
    public function expire()
    {
        return time() + $this->token_ttl;
    }

    private function payload(MinimalUserInterface $user, $expire)
    {
        $result = [
            'uid' => $user->getId(),
            'roles' => $user->getRoles(),
            'exp' => $expire ?? $this->expire(),
        ];
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

    public function create(MinimalUserInterface $user, $expire = null)
    {
        $payload = $this->payload($user, $expire);
        $privateKey = file_get_contents($this->secret_key);
        if (!$privateKey) {
            throw new \RuntimeException('No JWT private Key');
        }
        return JWT::encode($payload, $privateKey, self::ALG);
    }

    public function load($token)
    {
        $publicKey = file_get_contents($this->public_key);
        if (!$publicKey) {
            throw new \RuntimeException('No JWT public Key');
        }
        return JWT::decode($token, $publicKey, [self::ALG]);
    }

}
