<?php

namespace MixCode\JawalySms;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class JawalySmsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('jawaly-sms')
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('mix-code/jawaly-sms');
            });
    }

    public function packageRegistered()
    {
        $this->app->singleton('jawaly-sms', fn () => new JawalySms);
    }
}
