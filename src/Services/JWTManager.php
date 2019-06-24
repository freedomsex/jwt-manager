<?php

namespace FreedomSex\Services;

use Firebase\JWT\JWT;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTManager
{
    const TLL = 60 * 60;
    const ALG = 'RS256';
    const KEY_PATH = '/config/keys';

    public $projectDir = null;

    public function __construct($projectDir)
    {
        $this->projectDir = $projectDir;
    }

    /**
     * @return null
     */
    public function expire()
    {
        return time() + self::TLL;
    }

    private function payload($user, $expire)
    {
        $result = [
            'uid' => $user->getId(),
            'exp' => $expire ?? $this->expire(),
//            'ip'  => IP,
        ];
        return $result;
    }

    public function create(UserInterface $user, $expire = null)
    {
        $payload = $this->payload($user, $expire);
        $privateKey = file_get_contents($this->projectDir . self::KEY_PATH . '/private.key');
        return JWT::encode($payload, $privateKey, self::ALG);
    }

    public function load($token)
    {
        $publicKey = file_get_contents($this->projectDir . self::KEY_PATH . '/public.key');
        return JWT::decode($token, $publicKey);
    }

}

/**
 * mkdir -P /config/keys/
 * $projectDir.'/config/keys/private.key'
 * ssh-keygen -t rsa -b 4096 -f private.key # Hit enter for all questions
 * openssl rsa -in private.key -pubout -outform PEM -out public.key
 */ 
