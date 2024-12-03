<?php

namespace SilasWinter\MailCatcher\MailDriver\Transport\Smtp;

use Symfony\Component\Mailer\Transport\Smtp\SmtpTransport;
use SilasWinter\MailCatcher\MailDriver\Traits\OverrideAddress;

class Smtp extends SmtpTransport
{
    use OverrideAddress;
}
