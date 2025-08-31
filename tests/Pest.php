<?php

use MixCode\JawalySms\JawalySmsServiceProvider;

uses(Orchestra\Testbench\TestCase::class)
    ->beforeEach(function () {
        $this->app->register(JawalySmsServiceProvider::class);
    })
    ->in('Feature');
