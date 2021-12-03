<?php

namespace FreedomSex\Tests;

class User
{

    public function getId()
    {
        return '1';
    }

    public function getRoles()
    {
        return [
            'ROLE_USER'
        ];
    }
}