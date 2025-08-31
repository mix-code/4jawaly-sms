# 4Jawaly SMS

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mix-code/jawaly-sms.svg?style=flat-square)](https://packagist.org/packages/mix-code/jawaly-sms)
[![Tests](https://img.shields.io/github/actions/workflow/status/mix-code/jawaly-sms/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mix-code/jawaly-sms/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/mix-code/jawaly-sms.svg?style=flat-square)](https://packagist.org/packages/mix-code/jawaly-sms)
[![License](https://img.shields.io/packagist/l/mix-code/jawaly-sms.svg?style=flat-square)](LICENSE.md)

A Laravel package to send SMS messages using the **4Jawaly SMS API**.

-   Clean, consistent responses via a typed value object: `MixCode\JawalySms\SmsResponse`
-   Use a **Facade** (`JawalySms`) or inject the **service class** (`MixCode\JawalySms\JawalySms`)
-   Helpful error data without throwing exceptions to the application layer

---

## Installation

```bash
composer require mix-code/jawaly-sms
```

Publish the config (optional if you want to customize):

```bash
php artisan jawaly-sms:install
```

### Configuration

Add credentials to your `.env`:

```dotenv
JAWALY_SMS_BASE_URL=https://api.jawaly.com.sa/api/
JAWALY_SMS_API_KEY=your_app_id
JAWALY_SMS_API_SECRET=your_app_secret
```

Config file (`config/jawaly-sms.php`) keys used by the package:

```php
return [
    'base_url'  => env('JAWALY_SMS_BASE_URL'),
    'api_key'   => env('JAWALY_SMS_API_KEY'),
    'api_secret'=> env('JAWALY_SMS_API_SECRET'),
];
```

---

## Usage

### Via the Facade

```php
use MixCode\JawalySms\Facades\JawalySms;

// 1) Get senders (names only)
$response = JawalySms::senders(namesOnly: true);
if ($response->success) {
    // array of strings
    $senders = $response->data['senders'];
}

// 2) Check balance
$balance = JawalySms::balance();
if ($balance->success) {
    $amount = $balance->data['balance']; // e.g. 150
}

// 3) Send SMS
$result = JawalySms::sendSMS(
    message: 'Hello from Jawaly! 🚀',
    numbers: ['9665xxxxxxx', '9665yyyyyyy'],
    sender: 'YourBrand'
);
if ($result->success) {
    $jobId = $result->data['job_id'];
}
```

---

## API Methods

All methods return **`MixCode\JawalySms\SmsResponse`**.

### `senders(bool $namesOnly = false): SmsResponse`

Fetch available senders.

**Success data shape**

```php
[
    'senders' => [
        // if $namesOnly = true → ["Sender1", "Sender2", ...]
        // else → array of sender objects from 4Jawaly API
    ],
]
```

**Error response**

```php
$response->success === false;
$response->error;  // string message
$response->status; // HTTP status code or null
$response->body;   // raw response body (array|null)
```

---

### `balance(): SmsResponse`

Get remaining SMS balance.

**Success data shape**

```php
[
    'balance' => 150,
]
```

---

### `sendSMS(string $message, array $numbers, string $sender): SmsResponse`

Send a single message to one or more numbers from a specific sender name.

**Payload example**

```php
$message = 'Hello from Jawaly!';
$numbers = ['9665xxxxxxx', '9665yyyyyyy'];
$sender  = 'YourBrand';
```

**Success data shape**

```php
[
    'job_id' => 'abc123',
]
```

**Possible error shape**

```php
$response->success === false;
$response->error;  // "Invalid number" or API message
$response->status; // e.g. 422/400/500
$response->body;   // raw API body
```

---

## Response Object: `SmsResponse`

A simple value object consistently returned by all methods.

```php
namespace MixCode\JawalySms;

class SmsResponse
{
    public function __construct(
        public bool $success,
        public mixed $data = null,
        public ?string $error = null,
        public ?int $status = null,
        public mixed $body = null,
    ) {}

    public static function success(mixed $data): self
    {
        return new self(true, $data);
    }

    public static function error(string $error, ?int $status = null, mixed $body = null): self
    {
        return new self(false, null, $error, $status, $body);
    }
}
```

**Typical usage**

```php
$res = JawalySms::balance();
if ($res->success) {
    $amount = $res->data['balance'];
} else {
    logger()->error('Jawaly error', [
        'error' => $res->error,
        'status' => $res->status,
        'body' => $res->body,
    ]);
}
```

---

## Testing

This package is test-friendly with Laravel's HTTP fakes.

```bash
composer test
# or
vendor/bin/pest
```

---

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for recent changes.

## Contributing

PRs welcome! Please see [CONTRIBUTING](CONTRIBUTING.md).

## Security

Please review [our security policy](SECURITY.md) for how to report vulnerabilities.

## Credits

-   [MixCode](https://github.com/mix-code)
-   [All Contributors](CONTRIBUTORS.md)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
