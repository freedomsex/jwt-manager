<?php

namespace FreedomSex\Tests;

class UserUidIdentity
{
    public function __construct($id = 1, $uid = '2')
    {
        $this->id = $id;
        $this->uid = $uid;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUid()
    {
        return $this->uid;
    }

    public function getUuid()
    {
        return $this->uid;
    }

    public function getIdentityId()
    {
        return $this->uid;
    }

    public function getRoles()
    {
        return [
            'ROLE_USER'
        ];
    }
}