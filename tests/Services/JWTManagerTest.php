<?php

namespace FreedomSex\Tests\Services;

use FreedomSex\Tests\User;
use FreedomSex\Services\JWTManager;
use PHPUnit\Framework\TestCase;

class JWTManagerTest extends TestCase
{
    private $ttl = 600;
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->object = new JWTManager(
            '../keys/private.key',
            '../keys/public.key',
            $this->ttl
        );
        $this->user = new User();
    }

    public function testExpire()
    {
        self::assertEquals(time() + $this->ttl, $this->object->expire());
    }

    public function testCreate()
    {
        $token = $this->object->create($this->user);
        self::assertEquals(3, count(explode('.', $token)));
        $payload = (array) $this->object->load($token);
        self::assertEquals(1, $payload['uid']);
    }

    public function testCreateWithoutUser()
    {
        $token = $this->object->create();
        $payload = (array) $this->object->load($token);
        self::assertNotNull($payload['exp']);
        self::assertNull($payload['uid']);
    }

    public function testLoad()
    {
        $token = $this->object->create($this->user);
        $payload = (array) $this->object->load($token);
        self::assertEquals('ROLE_USER', $payload['roles'][0]);
    }
}
