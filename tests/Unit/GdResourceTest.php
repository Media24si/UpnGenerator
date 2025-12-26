<?php

it('generates GD resource', function () {
    $upn = getDefaultUpn();

    $gdResource = $upn->gdResource();

    expect($gdResource)->toBeInstanceOf(\GdImage::class);
});

it('generates same png as snapshot', function () {
    expect(md5(getDefaultUpn()->png()))
        ->toMatchSnapshot();
});

it('generates GD resource for empty duedate', function () {
    $upn = getDefaultUpn();
    $upn->setDueDate(null);

    $gdResource = $upn->gdResource();

    expect($gdResource)->toBeInstanceOf(\GdImage::class);
});
