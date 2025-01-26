<?php

namespace SilasWinter\MailCatcher\MailDriver\Traits;

use SilasWinter\MailCatcher\Helper\MailCatcher;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Address;

trait OverrideAddress
{
    /**
     * This works because https://stackoverflow.com/questions/67258172/dont-send-email-to-to-recipient
     * "rcpt to" is the SMTP command that specifies the recipient of the email even if someone is in the TO field.
     *
     * {@inheritDoc}
     *
     * @throws TransportExceptionInterface
     */
    protected function doSend(SentMessage $message): void
    {
        // replace all recipients with config('mailcatcher.address'), f.E. mailcatcher+maxmustermann@web.de
        $newRecipients = collect($message->getEnvelope()->getRecipients())
            ->map(function (Address $address) {
                $newEmail = MailCatcher::getEmail($address->getAddress());

                return new Address($newEmail, $address->getName());
            })
            ->values()
            ->all();

        $message->getEnvelope()->setRecipients($newRecipients);

        parent::doSend($message);
    }
}
