<?php

it('generates GD resource', function () {
    $upn = getDefaultUpn();

    $gdResource = $upn->gdResource();

    expect($gdResource)->toBeInstanceOf(\GdImage::class);
});

it('generates GD resource for empty duedate', function () {
    $upn = getDefaultUpn();
    $upn->setDueDate(null);

    $gdResource = $upn->gdResource();

    expect($gdResource)->toBeInstanceOf(\GdImage::class);
});
