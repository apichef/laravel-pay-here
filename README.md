# laravel-pay-here

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-ci]][link-ci]
[![Total Downloads][ico-downloads]][link-downloads]

ApiChef PayHere provides an expressive, fluent interface to PayHere’s payment services.

## Installation

You can install the package via composer:

```bash
composer require apichef/laravel-pay-here
```

| Laravel | Minimum Versions |
|---------|:----------------:|
| 6.x     |     `^1.0.0`     |
| 7.x     |     `^2.0.0`     |
| 8.x     |     `^2.0.0`     |
| 9.x     |     `^3.0.0`     |


You can publish the config file with:
```bash
php artisan vendor:publish --provider="ApiChef\PayHere\PayHereServiceProvider" --tag="config"
```

If your application accepting one time payments, you need to publish payments migration:
```bash
php artisan vendor:publish --provider="ApiChef\PayHere\PayHereServiceProvider" --tag="migrations:payments"
```

If your application supporting subscriptions, you need to publish subscriptions migration:
```bash
php artisan vendor:publish --provider="ApiChef\PayHere\PayHereServiceProvider" --tag="migrations:subscriptions"
```

Migrate
```bash
php artisan migrate
```

## Usage

[Documentation](https://milroy.me/laravel-pay-here)

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email milroy@outlook.com instead of using the issue tracker.

## Credits

- [Milroy E. Fraser](https://github.com/milroyfraser)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/apichef/laravel-pay-here.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-ci]: https://github.com/apichef/laravel-pay-here/workflows/CI/badge.svg
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/apichef/laravel-pay-here.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/apichef/laravel-pay-here.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/apichef/laravel-pay-here.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/apichef/laravel-pay-here
[link-ci]: https://github.com/apichef/laravel-pay-here/actions
[link-downloads]: https://packagist.org/packages/apichef/laravel-pay-here
[link-author]: https://github.com/milroyfraser
[link-contributors]: ../../contributors
