<?php

namespace SilasWinter\MailCatcher\MailDriver\Transport\Ses;

use Illuminate\Mail\Transport\SesTransport;
use SilasWinter\MailCatcher\MailDriver\Traits\OverrideAddress;

class Ses extends SesTransport
{
    use OverrideAddress;
}
