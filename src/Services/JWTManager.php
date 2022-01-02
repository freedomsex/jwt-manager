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
            $payload['id'] = $user->getId();
            $payload['uid'] = $user->getId();
        }
        if (method_exists($user, 'getRoles')) {
            $payload['roles'] = $user->getRoles();
        }
        if (method_exists($user, 'getAccess')) {
            $payload['access'] = $user->getAccess();
        }
        if (method_exists($user, 'getSubject')) {
            $payload['sub'] = $user->getSubject();
        }

        if (method_exists($user, 'getIdentityId')) {
            if (!method_exists($user, 'getUid') and method_exists($user, 'getId')) {
                $payload['uid'] = $user->getIdentityId();
            } else {
                $payload['uuid'] = $user->getIdentityId();
            }
        } else
        if (method_exists($user, 'getUuid')) {
            trigger_deprecation('freedomsex/jwt-manager', '0.3.0',
                'Using "%s" is deprecated. Use "%s" instead. Will be remover in 0.4',
                'UUID(Universally User ID)[getUuid]', 'ID[getId] and UID(Universally ID)[getUid]'
            );
            if (method_exists($user, 'getUid')) {
                trigger_deprecation('freedomsex/jwt-manager', '0.3.0',
                    'Using "%s" is deprecated. Use "%s" instead. Will be remover in 0.4',
                    'UID(User ID)[getUid]', 'ID[getId] and UID(Universally ID)[getUid]'
                );
            }
            $payload['uuid'] = $user->getUuid();
        } else
        if (method_exists($user, 'getUid')) {
            $payload['uid'] = $user->getUid();
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
        if (!file_exists($this->secret_key)) {
            throw new \RuntimeException('No JWT private Key');
        }
        $privateKey = file_get_contents($this->secret_key);
        if (!$privateKey) {
            throw new \RuntimeException('Wrong private key content');
        }
        return JWT::encode($payload, $privateKey, self::ALG);
    }

    public function load($token): object
    {
        if (!file_exists($this->public_key)) {
            throw new \RuntimeException('No JWT private Key');
        }
        $publicKey = file_get_contents($this->public_key);
        if (!$publicKey) {
            throw new \RuntimeException('Wrong public key content');
        }
        $payload = JWT::decode($token, $publicKey, [self::ALG]);

        if (property_exists($payload, 'uuid')) {
            trigger_deprecation('freedomsex/jwt-manager', '0.3.0',
                'Using "%s" is deprecated. Use "%s" instead. Will be remover in 0.4',
            'UUID(Universally User ID)[uuid]', 'ID and UID(Universally ID)[uid]'
            );
            if (property_exists($payload, 'uid')) {
                trigger_deprecation('freedomsex/jwt-manager', '0.3.0',
                    'Using "%s" is deprecated. Use "%s" instead. Will be remover in 0.4',
                    'UID(User ID)[uid]', 'ID and UID(Universally ID)[uid]'
                );
            }
        }
        return $payload;
    }

}
