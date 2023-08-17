<?php

namespace FreedomSex\Tests\Services;

use FreedomSex\Tests\Identity;
use FreedomSex\Tests\User;
use FreedomSex\Services\JWTManager;
use FreedomSex\Tests\UserUid;
use FreedomSex\Tests\UserUidIdentity;
use FreedomSex\Tests\UserUidUuid;
use FreedomSex\Tests\UserUuid;
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
            $this->ttl,
            'fs-issuer'
        );
        $this->user = new User();
    }

    public function testNoKeys()
    {
        $object = new JWTManager(
            '../key5/private.key',
            '../key5/public.key',
            $this->ttl
        );
        self::expectException(\RuntimeException::class);
        $object->create();
    }

    public function testWrongKeysContent()
    {
        $object = new JWTManager(
            '../keys/private5.key',
            '../keys/public5.key',
            $this->ttl
        );
        self::expectException(\RuntimeException::class);
        $object->create();
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

    public function testCreateIdentity()
    {
        $token = $this->object->create(new Identity());
        $payload = (array) $this->object->load($token);
        self::assertArrayHasKey('id', $payload);
        self::assertArrayHasKey('uid', $payload);
        self::assertArrayNotHasKey('uuid', $payload);
    }

    public function testCreateUuid()
    {
        $token = $this->object->create(new UserUuid());
        $payload = (array) $this->object->load($token);
        self::assertArrayHasKey('id', $payload);
        self::assertArrayHasKey('uid', $payload);
        self::assertArrayNotHasKey('uuid', $payload);
    }

    public function testCreateUid()
    {
        $token = $this->object->create(new UserUid());
        $payload = (array) $this->object->load($token);
        self::assertArrayHasKey('id', $payload);
        self::assertArrayHasKey('uid', $payload);
        self::assertArrayNotHasKey('uuid', $payload);
        self::assertNotEquals($payload['id'], $payload['uid']);
    }

    public function testCreateUidUuid()
    {
        $token = $this->object->create(new UserUidUuid());
        $payload = (array) $this->object->load($token);
        self::assertArrayHasKey('id', $payload);
        self::assertArrayHasKey('uid', $payload);
        self::assertArrayHasKey('uuid', $payload);
        self::assertEquals($payload['id'], $payload['uid']);
        self::assertNotEquals($payload['uid'], $payload['uuid']);
    }

    public function testCreateUidIdentity()
    {
        $token = $this->object->create(new UserUidIdentity());
        $payload = (array) $this->object->load($token);
        self::assertArrayHasKey('id', $payload);
        self::assertArrayHasKey('uid', $payload);
        self::assertArrayHasKey('uuid', $payload);
        self::assertEquals($payload['id'], $payload['uid']);
        self::assertNotEquals($payload['uid'], $payload['uuid']);
    }

    public function testIssuer()
    {
        $token = $this->object->create($this->user);
        $payload = (array) $this->object->load($token);
        self::assertEquals('fs-issuer', $payload['iss']);
    }
}
