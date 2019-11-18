# Empyr

[![Latest Stable Version](https://poser.pugx.org/linusx/empyr/v/stable)](https://packagist.org/packages/linusx/empyr)
[![Latest Unstable Version](https://poser.pugx.org/linusx/empyr/v/unstable)](https://packagist.org/packages/linusx/empyr)
[![Total Downloads](https://poser.pugx.org/linusx/empyr/downloads)](https://packagist.org/packages/linusx/empyr)
[![Build Status][ico-travis]][link-travis]
[![StyleCI](https://github.styleci.io/repos/222042425/shield?branch=master)](https://github.styleci.io/repos/222042425)

Package for using Empyr's (Mogl) API.

## Installation

Via Composer

``` bash
$ composer require linusx/empyr
```

Run
```
php artisan vendor:publish --provider="Linusx\Empyr\EmpyrServiceProvider"
```

## Usage

## Todo
* Billing Accounts (https://www.mogl.com/api/docs/v2/BillingAccounts/accounts)
* Cards (https://www.mogl.com/api/docs/v2/Cards/get)
* Devices (https://www.mogl.com/api/docs/v2/Devices/get)
* Fundraisers (https://www.mogl.com/api/docs/v2/Fundraisers/get)
* Invoices (https://www.mogl.com/api/docs/v2/Invoices/invoice)
* Metros (https://www.mogl.com/api/docs/v2/Metros/get)
* Offers (https://www.mogl.com/api/docs/v2/Offers/add)
* Payments (https://www.mogl.com/api/docs/v2/Payments/get)
* Stats Lookup (https://www.mogl.com/api/docs/v2/Reports/statsLookup)
* Transaction Report (https://www.mogl.com/api/docs/v2/Reports/txReportLookup)
* Subscriptions (https://www.mogl.com/api/docs/v2/Subscriptions/get)
* Utilities


## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [Bill Van Pelt][link-author]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/linusx/empyr.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/linusx/empyr.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/linusx/empyr/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/linusx/empyr
[link-downloads]: https://packagist.org/packages/linusx/empyr
[link-travis]: https://travis-ci.org/linusx/empyr
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/linusx
[link-contributors]: ../../contributors
