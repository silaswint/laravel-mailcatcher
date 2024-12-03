<?php

namespace SilasWinter\MailCatcher\Helper;

use Illuminate\Support\Str;

class MailCatcher
{
    public static function getEmail(?string $name)
    {
        $email = config('mailcatcher.address');

        if (! $name) {
            return str_replace('{+name}', '', $email);
        }

        $name = Str::slug($name, '_');

        return str_replace('{+name}', '+'.$name, $email);
    }
}
