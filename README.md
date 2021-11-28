![](https://banners.beyondco.de/Laravel-CoinDCX-API.png?theme=light&packageManager=composer+require&packageName=techtailor%2Flaravel-coindcx-api&pattern=architect&style=style_2&description=A+laravel+wrapper+for+the+CoinDCX+API.&md=1&showWatermark=0&fontSize=100px&images=server)

[![GitHub release](https://img.shields.io/github/release/techtailor/laravel-coindcx-api.svg?include_prereleases&style=for-the-badge&&colorB=7E57C2)](https://packagist.org/packages/techtailor/laravel-CoinDCX-api)
[![GitHub issues](https://img.shields.io/github/issues/TechTailor/Laravel-CoinDCX-Api.svg?style=for-the-badge)](https://github.com/TechTailor/Laravel-CoinDCX-Api/issues)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=for-the-badge&&colorB=F27E40)](license.md)
[![Total Downloads](https://img.shields.io/packagist/dt/techtailor/laravel-coindcx-api.svg?style=for-the-badge)](https://packagist.org/packages/techtailor/laravel-coindcx-api)

This package provides a Laravel Wrapper for the [CoinDCX API](https://docs.coindcx.com/) and allows you to easily communicate with it.

 ---
#### Important Note
This package is in early development stage. It is not advisable to use it in a production app until **`v1.0`** is released. Feel free to open a PR to contribute to this project and help me reach a production ready build.

---

### Installation

You can install the package via composer:

```bash
composer require techtailor/laravel-coindcx-api
```

You can publish the config file with:
```bash
php artisan vendor:publish --tag="coindcx-api-config"
```

Open your `.env` file and add the following (replace ``YOUR_API_KEY`` and ``YOUR_SECRET`` with the API Key & Secret you received from [CoinDCX](https://coindcx.com/api-dashboard)) -
```php
COINDCX_KEY=YOUR_API_KEY
COINDCX_SECRET=YOUR_SECRET
```
Or

Open the published config file available at `config/coindcx-api.php` and add your API and Secret Keys:

```php
return [
    'auth' => [
        'key'        => env('COINDCX_KEY', 'YOUR_API_KEY'),
        'secret'     => env('COINDCX_SECRET', 'YOUR_SECRET')
    ],
];
```

### Usage

Using this package is very simple. Just initialize the Api and call one of the available methods: 
```php
use TechTailor\CoinDCXApi\CoinDCXApi;

$cdx = new CoinDCXApi();

$time = $cdx->getTime();
```

You can also set an API & Secret for a user by passing it after initalization (useful when you need to isolate api keys for individual users):

```php
$cdx = new CoinDCXApi();

$cdx->setApi($apiKey, $secretKey);

$accountInfo = $cdx->getAccountInfo();
```

### Available Methods

Available Public Methods (Security Type : `NONE`) **[API Keys Not Required]**
```
- getTicker()               // returns the exchange ticker
- getMarkets()              // returns the exchange markets list
- getMarketsDetails()       // returns the exchange markets details
- getMarketTrades()         // returns the exchange markets trade history
```
Available Private Methods (Security Type : `AUTHENTICATED`) **[API Keys Required]**
```
- getAccountInfo()          // returns user account info (coindcx_id, email, etc)
- getBalances()             // returns user account balances
- getTradeHistory()         // returns user's complete spot trade history
- getActiveOrders()         // returns user's active orders list
```

### TODO

List of features or additional functionality we are working on (in no particular order) -

```bash
- Improve exception handling.
- Add rate limiting to API Calls.
- Add response for API ban/blacklisting response.
- Improve ReadMe.
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

### Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

### Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

### Credits

- [Moinuddin S. Khaja](https://github.com/TechTailor)
- [All Contributors](../../contributors)

### License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
