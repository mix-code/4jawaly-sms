<?php

namespace Tests;

use Illuminate\Support\Facades\Http;
use MixCode\JawalySms\Facades\JawalySms;
use MixCode\JawalySms\JawalySmsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            JawalySmsServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'JawalySms' => JawalySms::class,
            'Http' => Http::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('jawaly-sms.base_url', 'https://api.sms.com');
    }
}
