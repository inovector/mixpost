
[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/support-ukraine.svg?t=1" />](https://supportukrainenow.org)

[<img src="./art/logo.svg" width="300px" alt="Logo Mixpost" />](https://mixpost.app)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/inovector/mixpost.svg?style=flat-square)](https://packagist.org/packages/inovector/mixpost)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/inovector/mixpost/run-tests?label=tests)](https://github.com/inovector/mixpost/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/inovector/mixpost/Check%20&%20fix%20styling?label=code%20style)](https://github.com/inovector/mixpost/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/inovector/mixpost.svg?style=flat-square)](https://packagist.org/packages/inovector/mixpost)

## Introduction

Mixpost is a Laravel marketing platform for social networks.

[<img src="./art/cover.png" />](https://mixpost.app)


## Installation

You can install the package via composer:

```bash
composer require inovector/mixpost
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="mixpost-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="mixpost-config"
```

This is the contents of the published config file:

```php
return [
];
```

Publish the assets:

```bash
php artisan mixpost:publish-assets
```

## Visit the UI

After performing all these steps, you should be able to visit the Mixpost UI at /mixpost.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/inovector/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Dumitru Botezatu](https://github.com/inovector)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
