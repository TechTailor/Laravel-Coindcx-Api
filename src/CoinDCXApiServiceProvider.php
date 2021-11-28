<?php

namespace TechTailor\CoinDCXApi;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CoinDCXApiServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('coindcx-api')
            ->hasConfigFile();
    }

    public function packageRegistered()
    {
        //
    }
}
