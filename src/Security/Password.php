<?php

namespace Spl\Security;

class Password
{
    public function hash($password, $algo = PASSWORD_DEFAULT): string
    {
        return password_hash($password, $algo);
    }

    public function verify($password, $hash): bool
    {
        return password_verify($password, $hash)
    }

}