<?php

namespace SilasWinter\MailCatcher;

use Aws\Ses\SesClient;
use Closure;
use Exception;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Arr;
use SilasWinter\MailCatcher\MailDriver\CustomMailManager;
use SilasWinter\MailCatcher\MailDriver\Transport;
use SilasWinter\MailCatcher\MailDriver\Transport\Smtp;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class MailCatcherServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-mailcatcher')
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command->publishConfigFile();
            });
    }

    public function packageBooted(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/mailcatcher.php', 'mailcatcher');

        config()->set('mail.mailers.mailcatcher', [
            'transport' => 'mailcatcher',
            'layer' => config('mailcatcher.layer'),
        ]);

        $this->app->extend('mail.manager', function (MailManager $mailManager) {
            return new CustomMailManager($mailManager->getApplication());
        });
    }
}
