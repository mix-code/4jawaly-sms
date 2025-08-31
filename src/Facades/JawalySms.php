<?php

namespace MixCode\JawalySms\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \MixCode\JawalySms\SmsResponse senders($namesOnly = false)
 * @method static \MixCode\JawalySms\SmsResponse balance()
 * @method static \MixCode\JawalySms\SmsResponse sendSMS(string $message, array $numbers, string $sender)
 */
class JawalySms extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'jawaly-sms';
    }
}
