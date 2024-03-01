<?php

it('generates GD resource', function () {
    $upn = getDefaultUpn();

    $gdResource = $upn->gdResource();

    expect($gdResource)->toBeInstanceOf(\GdImage::class);
});

it('generates same png as snapshot', function () {
    $png = getDefaultUpn()->png();

    expect(md5($png))->toBe(md5_file(__DIR__.'/../snapshots/default.png'));
});

it('generates GD resource for empty duedate', function () {
    $upn = getDefaultUpn();
    $upn->setDueDate(null);

    $gdResource = $upn->gdResource();

    expect($gdResource)->toBeInstanceOf(\GdImage::class);
});
