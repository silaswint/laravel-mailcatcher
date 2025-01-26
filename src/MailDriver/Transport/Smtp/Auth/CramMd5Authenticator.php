<?php

namespace SilasWinter\MailCatcher\MailDriver\Transport\Smtp\Auth;

use Symfony\Component\Mailer\Exception\InvalidArgumentException;
use Symfony\Component\Mailer\Transport\Smtp\Auth as BaseAuthenticator;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport as BaseEsmtpTransport;
use SilasWinter\MailCatcher\MailDriver\Transport\Smtp\EsmtpTransport;

class CramMd5Authenticator extends BaseAuthenticator\CramMd5Authenticator
{
    public function authenticate(BaseEsmtpTransport|EsmtpTransport $client): void
    {
        $challenge = $client->executeCommand("AUTH CRAM-MD5\r\n", [334]);
        $challenge = base64_decode(substr($challenge, 4));
        $message = base64_encode($client->getUsername().' '.$this->getResponse($client->getPassword(), $challenge));
        $client->executeCommand(sprintf("%s\r\n", $message), [235]);
    }

    /**
     * Generates a CRAM-MD5 response from a server challenge.
     */
    private function getResponse(#[\SensitiveParameter] string $secret, string $challenge): string
    {
        if (!$secret) {
            throw new InvalidArgumentException('A non-empty secret is required.');
        }

        if (\strlen($secret) > 64) {
            $secret = pack('H32', md5($secret));
        }

        if (\strlen($secret) < 64) {
            $secret = str_pad($secret, 64, \chr(0));
        }

        $kipad = substr($secret, 0, 64) ^ str_repeat(\chr(0x36), 64);
        $kopad = substr($secret, 0, 64) ^ str_repeat(\chr(0x5C), 64);

        $inner = pack('H32', md5($kipad.$challenge));
        $digest = md5($kopad.$inner);

        return $digest;
    }
}