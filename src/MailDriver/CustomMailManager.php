<?php

namespace SilasWinter\MailCatcher\MailDriver;

use SilasWinter\MailCatcher\MailDriver\Transport\Smtp;
use Aws\Ses\SesClient;
use Exception;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Arr;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class CustomMailManager extends MailManager
{
    /**
     * @throws Exception
     */
    protected function createMailcatcherTransport(array $config)
    {
        if (! isset($config['layer'])) {
            throw new Exception('The mailcatcher mailer is not configured to use a transport layer. (Examples: ses, smtp');
        }

        if ($config['layer'] === 'ses') {
            $config = config('mail.mailers.ses');

            return $this->createMailcatcherSesTransport($config);
        }

        if ($config['layer'] === 'smtp') {
            $config = config('mail.mailers.smtp');

            return $this->createMailcatcherSmtpTransport($config);
        }

        throw new Exception('The mailcatcher mailer is not configured to use a existing transport layer. (Examples: ses, smtp');
    }

    protected function createMailcatcherSesTransport(array $config)
    {
        $config = array_merge(
            $this->app['config']->get('services.ses', []),
            ['version' => 'latest', 'service' => 'email'],
            $config
        );

        $config = Arr::except($config, ['transport']);

        return new Transport\Ses\Ses(
            new SesClient($this->addSesCredentials($config)),
            $config['options'] ?? []
        );
    }

    protected function createMailcatcherSmtpTransport(array $config)
    {
        $factory = new Smtp\EsmtpTransportFactory;

        $scheme = $config['scheme'] ?? null;

        if (! $scheme) {
            $scheme = ! empty($config['encryption']) && $config['encryption'] === 'tls'
                ? (($config['port'] == 465) ? 'smtps' : 'smtp')
                : '';
        }

        $transport = $factory->create(new Dsn(
            $scheme,
            $config['host'],
            $config['username'] ?? null,
            $config['password'] ?? null,
            $config['port'] ?? null,
            $config
        ));

        return $this->configureMailcatcherSmtpTransport($transport, $config);
    }

    protected function configureMailcatcherSmtpTransport(Smtp\EsmtpTransport $transport, array $config)
    {
        $stream = $transport->getStream();

        if ($stream instanceof SocketStream) {
            if (isset($config['source_ip'])) {
                $stream->setSourceIp($config['source_ip']);
            }

            if (isset($config['timeout'])) {
                $stream->setTimeout($config['timeout']);
            }
        }

        return $transport;
    }
}
