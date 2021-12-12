<?php

namespace FreedomSex\Tests;

class UserUid
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

    public function getRoles()
    {
        return [
            'ROLE_USER'
        ];
    }
}