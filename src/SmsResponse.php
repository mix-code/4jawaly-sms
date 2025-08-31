<?php

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
        return new self(
            success: true,
            data: $data
        );
    }

    public static function error(string $error, ?int $status = null, mixed $body = null): self
    {
        return new self(
            success: false,
            data: null,
            error: $error,
            status: $status,
            body: $body
        );
    }
}
