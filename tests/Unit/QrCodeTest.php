<?php

it('generates correct qr code content', function () {
    $upn = getDefaultUpn();

    $qrCodeContent = explode("\n", $upn->getQRCodeText());

    expect($qrCodeContent)->toHaveCount(21);
    expect($qrCodeContent[19])->toBeString()->toBe('196');
    expect($qrCodeContent[8])->toBeString()->toBe('00000030024');
});

it('trims strings in code', function () {
    $upn = getDefaultUpn();
    $upn->setPayerName('    Janez Novak    ');

    $qrCodeContent = explode("\n", $upn->getQRCodeText());

    expect($qrCodeContent[5])->toBeString()->toBe('Janez Novak');
});

it('output correct price', function () {
    $upn = getDefaultUpn();
    $upn->setAmount(33.30);

    $qrCodeContent = explode("\n", $upn->getQRCodeText());

    expect($qrCodeContent[8])->toBeString()->toBe('00000003330');
});

it('sets empty string for empty duedate', function () {
    $upn = getDefaultUpn();
    $upn->setDueDate(null);

    $qrCodeContent = explode("\n", $upn->getQRCodeText());

    expect($qrCodeContent[13])->toBeString()->toBe('');
});
