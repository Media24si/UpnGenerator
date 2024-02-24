<?php

it('generates GD resource', function () {
    $upn = getDefaultUpn();

    $gdResource = $upn->gdResource();

    expect($gdResource)->toBeInstanceOf(\GdImage::class);
});
