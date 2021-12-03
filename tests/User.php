<?php

namespace FreedomSex\Tests;

class User implements \FreedomSex\User\MinimalUserInterface
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