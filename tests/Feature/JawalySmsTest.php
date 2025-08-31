<?php

use Illuminate\Support\Facades\Http;
use MixCode\JawalySms\JawalySms;
use MixCode\JawalySms\SmsResponse;

it('can fetch senders successfully', function () {
    Http::fake([
        config('jawaly-sms.base_url') . 'account/area/senders' => Http::response([
            'code' => 200,
            'items' => [
                'data' => [
                    ['sender_name' => 'TestSender1'],
                    ['sender_name' => 'TestSender2'],
                ],
            ],
        ], 200),
    ]);

    $sms = new JawalySms();

    $response = $sms->senders(namesOnly: true);

    expect($response)->toBeInstanceOf(SmsResponse::class)
        ->and($response->success)->toBeTrue()
        ->and($response->data['senders'])->toEqual(['TestSender1', 'TestSender2']);
});

it('can check balance successfully', function () {
    Http::fake([
        config('jawaly-sms.base_url') . 'account/area/me/packages' => Http::response([
            'code' => 200,
            'total_balance' => 150,
        ], 200),
    ]);

    $sms = new JawalySms();

    $response = $sms->balance();

    expect($response)->toBeInstanceOf(SmsResponse::class)
        ->and($response->success)->toBeTrue()
        ->and($response->data['balance'])->toEqual(150);
});

it('can send sms successfully', function () {
    Http::fake([
        config('jawaly-sms.base_url') . 'account/area/sms/send' => Http::response([
            'job_id' => '12345',
        ], 200),
    ]);

    $sms = new JawalySms();

    $response = $sms->sendSMS('Hello', ['1234567890'], 'TestSender');

    expect($response)->toBeInstanceOf(SmsResponse::class)
        ->and($response->success)->toBeTrue()
        ->and($response->data['job_id'])->toEqual('12345');
});

it('handles error when sending sms', function () {
    Http::fake([
        config('jawaly-sms.base_url') . 'account/area/sms/send' => Http::response([
            'messages' => [
                ['err_text' => 'Invalid number'],
            ],
        ], 400),
    ]);

    $sms = new JawalySms();

    $response = $sms->sendSMS('Hello', ['invalid'], 'TestSender');

    expect($response)->toBeInstanceOf(SmsResponse::class)
        ->and($response->success)->toBeFalse();
});
