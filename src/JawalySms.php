<?php

namespace MixCode\JawalySms;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class JawalySms
{
    protected $baseUrl;

    protected $appId;

    protected $appSecret;

    public function __construct()
    {
        $this->baseUrl = config('jawaly-sms.base_url');
        $this->appId = config('jawaly-sms.api_key');
        $this->appSecret = config('jawaly-sms.api_secret');
    }

    public function senders($namesOnly = false): SmsResponse
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->get($this->baseUrl . 'account/area/senders')
                ->throw();

            $data = $response->json();

            if (($data['code'] ?? null) === 200) {
                return SmsResponse::success(data: [
                    'senders' => collect($data['items']['data'])
                        ->when($namesOnly, fn($collection) => $collection->pluck('sender_name'))
                        ->unless($namesOnly, fn($collection) => $collection->map(fn($sender) => $sender))
                        ->toArray(),
                ]);
            }

            return SmsResponse::error(
                error: $data['message'] ?? 'Error fetching senders',
                status: $response->status(),
                body: $data
            );
        } catch (RequestException $e) {
            return SmsResponse::error(
                error: $e->getMessage(),
                status: $e->response?->status(),
                body: $e->response?->json()
            );
        } catch (\Throwable $e) {
            return SmsResponse::error(error: $e->getMessage());
        }
    }

    public function balance(): SmsResponse
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->get($this->baseUrl . 'account/area/me/packages')
                ->throw();

            $data = $response->json();

            if (($data['code'] ?? null) === 200) {
                return SmsResponse::success(data: [
                    'balance' => $data['total_balance'],
                ]);
            }

            return SmsResponse::error(
                error: $data['message'] ?? 'Error checking balance',
                status: $response->status(),
                body: $data
            );
        } catch (RequestException $e) {
            return SmsResponse::error(
                error: $e->getMessage(),
                status: $e->response?->status(),
                body: $e->response?->json()
            );
        } catch (\Throwable $e) {
            return SmsResponse::error(error: $e->getMessage());
        }
    }

    public function sendSMS(string $message, array $numbers, string $sender): SmsResponse
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->post($this->baseUrl . 'account/area/sms/send', [
                    'messages' => [
                        [
                            'text' => $message,
                            'numbers' => $numbers,
                            'sender' => $sender,
                        ],
                    ],
                ])
                ->throw();

            $data = $response->json();

            if (isset($data['job_id'])) {
                return SmsResponse::success(data: [
                    'job_id' => $data['job_id'],
                ]);
            }

            $errorMessage = $data['messages'][0]['err_text'] ?? $data['message'] ?? 'Error sending message';
            return SmsResponse::error($errorMessage, $response->status(), $data);
        } catch (RequestException $e) {
            return SmsResponse::error(
                error: $e->getMessage(),
                status: $e->response?->status(),
                body: $e->response?->json()
            );
        } catch (\Throwable $e) {
            return SmsResponse::error(error: $e->getMessage());
        }
    }

    protected function headers(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->appId . ':' . $this->appSecret),
        ];
    }
}
