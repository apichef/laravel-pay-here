# laravel-pay-here

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

ApiChef PayHere provides an expressive, fluent interface to PayHere’s payment services.

## Installation

You can install the package via composer:

```bash
composer require apichef/laravel-pay-here
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="ApiChef\PayHere\PayHereServiceProvider" --tag="migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="ApiChef\PayHere\PayHereServiceProvider" --tag="config"
```

## Usage

``` php
soon. still alpha
```

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
[ico-travis]: https://img.shields.io/travis/apichef/laravel-pay-here/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/apichef/laravel-pay-here.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/apichef/laravel-pay-here.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/apichef/laravel-pay-here.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/apichef/laravel-pay-here
[link-travis]: https://travis-ci.org/apichef/laravel-pay-here
[link-scrutinizer]: https://scrutinizer-ci.com/g/apichef/laravel-pay-here/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/apichef/laravel-pay-here
[link-downloads]: https://packagist.org/packages/apichef/laravel-pay-here
[link-author]: https://github.com/milroyfraser
[link-contributors]: ../../contributors
