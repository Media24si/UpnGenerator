# Upn Generator

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

# Usage

First generate UPN:
```php
$upn = (new \Media24si\UpnGenerator\UpnGenerator())
    ->setPayerName('Janez Novak')
    ->setPayerAddress('Dunajska ulica 1')
    ->setPayerPost('1000 Ljubljana')
    ->setReceiverName('RentaCar d.o.o.')
    ->setReceiverAddress('Pohorska ulica 22')
    ->setReceiverPost('2000 Maribor')
    ->setReceiverIban('SI56020170014356205')
    ->setAmount(300.24)
    ->setCode('RENT')
    ->setReference('SI121234567890120')
    ->setPurpose('Plačilo najemnine za marec')
    ->setDueDate(new DateTime('+1 month'));
```

Then you have 3 options:

Output to browser: `$upn->render();`

Get PNG as a string: `$upn->png();`

Get GD Image resource: `$upn->gdResource();`

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
