<?php

namespace Web\Security;

class Password
{
    public static function hash($password, $algo = PASSWORD_DEFAULT): string
    {
        return password_hash($password, $algo);
    }

    public static function verify($password, $hash): bool
    {
        return password_verify($password, $hash)
    }

}