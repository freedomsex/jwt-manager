<?php

namespace FreedomSex\Services;

use Firebase\JWT\JWT;

use Symfony\Component\Security\Core\User\UserInterface;

class JWTManager
{
    const TLL = 60 * 60;
    const ALG = 'RS256';
    const KEY_PATH = '/config/keys';

    public $projectDir = null;

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

    private function payload(UserInterface $user, $expire)
    {
        $result = [
            'uid' => $user->getId(),
            'roles' => $user->getRoles(),
            'exp' => $expire ?? $this->expire(),
        ];
        if (method_exists($user, 'getUuid')) {
            $result['uuid'] = $user->getUuid();
        }
        if (method_exists($user, 'getSubject')) {
            $result['sub'] = $user->getSubject();
        }
        return $result;
    }

    public function create(UserInterface $user, $expire = null)
    {
        $payload = $this->payload($user, $expire);
        $privateKey = file_get_contents($this->secret_key);
        return JWT::encode($payload, $privateKey, self::ALG);
    }

    public function load($token)
    {
        $publicKey = file_get_contents($this->public_key);
        return JWT::decode($token, $publicKey, [self::ALG]);
    }

}

/**
 * mkdir -P /config/keys/
 * $projectDir.'/config/keys/private.key'
 * openssl genrsa -out private.key 1024
 * openssl rsa -in private.key -pubout -outform PEM -out public.key
 */
