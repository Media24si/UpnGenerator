<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function getDefaultUpn(): \Media24si\UpnGenerator\UpnGenerator
{
    return (new \Media24si\UpnGenerator\UpnGenerator())
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
        ->setDueDate(new DateTime('2024-03-24'));
}
