# Email Notifications

## Recommended starter provider

Use Brevo SMTP for the first production setup.

Brevo is the best fit for the current stage because it has a free plan with 300 email sends per day, includes transactional email, does not require a credit card, and works through Laravel's built-in SMTP mailer without adding another Composer package.

Laravel 12 also supports API transports such as Resend, Mailgun, Postmark, and SES. Those can be better later if volume grows or if we want an API transport, but Brevo has the easiest zero-payment start.

## Production environment

Set these variables on the remote server:

```env
APP_URL=https://mypiggybox.com

MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your-brevo-smtp-login
MAIL_PASSWORD=your-brevo-smtp-key
MAIL_FROM_ADDRESS=noreply@mypiggybox.com
MAIL_FROM_NAME="MyPiggyBox"

# Use database with a running worker. If the host cannot run workers yet, use sync temporarily.
QUEUE_CONNECTION=database
```

Use the Brevo SMTP key as `MAIL_PASSWORD`, not the Brevo API key or account password.

Before switching production traffic, add and authenticate the `mypiggybox.com` sender domain in Brevo, then create a transactional sender such as `noreply@mypiggybox.com`.

## Deployment checks

After updating remote environment variables, clear cached config:

```sh
php artisan config:clear
php artisan optimize:clear
```

Send a test email:

```sh
php artisan mail:test sales@mypiggybox.com
```

The app sends contribution and box-created emails from queued listeners. For the proper production setup, keep `QUEUE_CONNECTION=database` and run a queue worker:

```sh
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

For a normal Linux server, run that command under Supervisor or the hosting provider's background worker feature so it restarts automatically.

If the remote host cannot run background workers yet, set `QUEUE_CONNECTION=sync` temporarily. Emails will send during the web request or webhook instead of in the background, which is simpler but slower.
