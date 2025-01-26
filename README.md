# Mailcatcher for Laravel

## What is Mailcatcher for Laravel?
Mailcatcher for Laravel is a package designed to intercept and redirect all outgoing emails to a specified address instead of the actual recipient. This feature is particularly useful for staging environments of small businesses, where testing email functionalities without sending actual emails to customers or users is crucial.

## Installation

Follow these steps to install Mailcatcher for Laravel:

1. Run the composer command to add the package to your project:
   ```bash
   composer require silaswinter/laravel-mailcatcher
   ```

2. Append the following configuration settings to your `.env` file:
   ```plaintext
   MAIL_MAILER="mailcatcher"
   MAIL_MAILCATCHER_LAYER="smtp" # or "ses"
   MAILCATCHER_ADDRESS="mailcatcher@example.com" # all mails are redirected to this address
   ```

## How It Works

Mailcatcher acts as a wrapper for Laravel's default mailer. By setting the `MAIL_MAILER` configuration to `mailcatcher`, the `MailCatcherServiceProvider.php` class is invoked, which overrides the default mail manager:

```php
$this->app->extend('mail.manager', function (MailManager $mailManager) {
    return new CustomMailManager($mailManager->getApplication());
});
```

During the package bootstrapping process, this method determines the appropriate transport layer based on the `MAIL_MAILCATCHER_LAYER` setting.

### Transport Layers

Emails are handled by specific transport layers, determined by your configuration:

1. **SES Transport**: `src/MailDriver/Transport/Ses/Ses.php`
2. **SMTP Transport**: `src/MailDriver/Transport/Smtp/Smtp.php`

Both layers utilize the `src/MailDriver/Traits/OverrideAddress.php` trait, which adjusts the email header fields. The primary field affected is the recipient address, allowing you to manipulate both visible and actual recipient addresses.

For a deeper dive into the technical details of how email headers can be manipulated, refer to this Stack Overflow discussion: [Understanding Email Headers and Routing](https://stackoverflow.com/questions/67258172/dont-send-email-to-to-recipient).

## Testing and Caution

This package has been rigorously tested in two production environments since March 2024, utilizing both the SES and SMTP transport layers. However, caution is advised as it involves overriding core Symfony code, which may not be compatible with all Laravel versions. Please ensure compatibility before deploying in your production environment.
