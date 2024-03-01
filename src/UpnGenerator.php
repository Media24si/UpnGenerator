<?php

namespace Media24si\UpnGenerator;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use InvalidArgumentException;

class UpnGenerator
{
    private string $payer_name;

    private string $payer_address;

    private string $payer_post;

    private string $receiver_name;

    private string $receiver_address;

    private string $receiver_post;

    private string $receiver_iban;

    private string $reference;

    private float $amount;

    private string $code;

    private string $purpose = '';

    private ?\DateTime $due_date = null;

    private const FONT = __DIR__.'/courbd.ttf';

    private const FONT_SIZE = 17;

    private const FONT_SMALL = 11;

    private \GdImage $image;

    private int $color;

    public function __construct()
    {
        $this->image = imagecreatefrompng(__DIR__.'/upn_sl.png');
        $this->color = imagecolorallocate($this->image, 0x00, 0x00, 0x00);
    }

    public function gdResource(): \GdImage
    {
        $this->writeText(697, 170, $this->payer_name ?? '');
        $this->writeText(697, 201, $this->payer_address ?? '');
        $this->writeText(697, 233, $this->payer_post ?? '');

        $this->writeText(30, 62, $this->payer_name ?? '', self::FONT_SMALL);
        $this->writeText(30, 87, $this->payer_address ?? '', self::FONT_SMALL);
        $this->writeText(30, 112, $this->payer_post ?? '', self::FONT_SMALL);

        $this->writeText(418, 507, $this->receiver_name ?? '');
        $this->writeText(418, 538, $this->receiver_address ?? '');
        $this->writeText(418, 570, $this->receiver_post ?? '');

        $this->writeText(30, 405, $this->receiver_name ?? '', self::FONT_SMALL);
        $this->writeText(30, 430, $this->receiver_address ?? '', self::FONT_SMALL);
        $this->writeText(30, 455, $this->receiver_post ?? '', self::FONT_SMALL);

        $this->writeText(418, 400, $this->getFormattedReceiverIban() ?? '');
        $this->writeText(30, 300, $this->getFormattedReceiverIban() ?? '', self::FONT_SMALL);

        $this->writeText(418, 451, $this->getReferencePrefix());
        $this->writeText(528, 451, $this->getReferenceSuffix());
        $this->writeText(30, 351, $this->getFormattedReference(), self::FONT_SMALL);

        $this->writeText(528, 340, $this->purpose ?? '');
        $this->writeText(30, 165, $this->purpose ?? '', 10);

        if ($this->due_date) {
            $this->writeText(1155, 340, $this->due_date->format('d.m.Y'));
            $this->writeText(30, 195, $this->due_date->format('d.m.Y'), self::FONT_SMALL);
        }

        $this->writeText(110, 247, '***'.$this->getFormattedPrice(), self::FONT_SMALL);
        $this->writeText(750, 285, '***'.$this->getFormattedPrice());

        $this->writeText(418, 340, $this->code ?? '');

        $qr = $this->getQRCode();
        imagecopyresampled($this->image, $qr, 433, 60, 0, 0, 220, 220, imagesx($qr), imagesy($qr));

        return $this->image;
    }

    public function render(): void
    {
        $image = $this->gdResource();

        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
    }

    public function png(): string
    {
        $image = $this->gdResource();

        ob_start();
        imagepng($image);
        $img = ob_get_contents();
        ob_end_clean();

        imagedestroy($image);

        return $img;
    }

    public function getQRCode(): \GdImage
    {
        return Builder::create()
            ->data($this->getQRCodeText())
            ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->encoding(new Encoding('ISO-8859-2'))
            ->writer(new PngWriter())
            ->size(400)
            ->build()
            ->getImage();
    }

    public function getQRCodeText(): string
    {
        $text = [
            'UPNQR',
            '',
            '',
            '',
            '',
            $this->payer_name ?? '',
            $this->payer_address ?? '',
            $this->payer_post ?? '',
            sprintf('%011d', round($this->amount * 100, 2)) ?? '',
            '',
            '',
            $this->code ?? '',
            $this->purpose ?? '',
            $this->due_date?->format('d.m.Y') ?? '',
            $this->receiver_iban ?? '',
            $this->reference ?? '',
            $this->receiver_name ?? '',
            $this->receiver_address ?? '',
            $this->receiver_post ?? '',
        ];

        $text = implode("\n", array_map('trim', $text))."\n";
        $text .= mb_strlen($text)."\n"; // append control code

        return $text;
    }

    public function getFormattedPrice(): string
    {
        return number_format($this->amount, 2, ',', '.');
    }

    public function getFormattedReceiverIban(): string
    {
        return wordwrap($this->receiver_iban, 4, ' ', true);
    }

    public function getFormattedReference(): string
    {
        return $this->getReferencePrefix().' '.$this->getReferenceSuffix();
    }

    public function getReferencePrefix(): string
    {
        return substr($this->reference, 0, 4);
    }

    public function getReferenceSuffix(): string
    {
        return substr($this->reference, 4);
    }

    private function writeText(int $x, int $y, $text, $fontSize = self::FONT_SIZE): void
    {
        if ($text) {
            imagefttext($this->image, $fontSize, 0, $x, $y, $this->color, self::FONT, $text);
        }
    }

    public function getPayerName(): string
    {
        return $this->payer_name;
    }

    public function setPayerName(string $payer_name): self
    {
        $this->payer_name = mb_substr($payer_name, 0, 33);

        return $this;
    }

    public function getPayerAddress(): string
    {
        return $this->payer_address;
    }

    public function setPayerAddress(string $payer_address): self
    {
        $this->payer_address = mb_substr($payer_address, 0, 33);

        return $this;
    }

    public function getPayerPost(): string
    {
        return $this->payer_post;
    }

    public function setPayerPost(string $payer_post): self
    {
        $this->payer_post = mb_substr($payer_post, 0, 33);

        return $this;
    }

    public function getReceiverName(): string
    {
        return $this->receiver_name;
    }

    public function setReceiverName(string $receiver_name): self
    {
        $this->receiver_name = mb_substr($receiver_name, 0, 33);

        return $this;
    }

    public function getReceiverAddress(): string
    {
        return $this->receiver_address;
    }

    public function setReceiverAddress(string $receiver_address): self
    {
        $this->receiver_address = mb_substr($receiver_address, 0, 33);

        return $this;
    }

    public function getReceiverPost(): string
    {
        return $this->receiver_post;
    }

    public function setReceiverPost(string $receiver_post): self
    {
        $this->receiver_post = mb_substr($receiver_post, 0, 33);

        return $this;
    }

    public function getReceiverIban(): string
    {
        return $this->receiver_iban;
    }

    public function setReceiverIban(string $receiver_iban): self
    {
        $iban = str_replace(' ', '', $receiver_iban);

        if (strlen($iban) !== 19) {
            throw new InvalidArgumentException('IBAN must be 19 characters long;');
        }

        $this->receiver_iban = $iban;

        return $this;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $reference = str_replace(' ', '', $reference);

        if (strlen($reference) > 26) {
            throw new InvalidArgumentException('Max length for reference is 26 char');
        }

        $this->reference = $reference;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        if (strlen($code) !== 4) {
            throw new InvalidArgumentException('CODE must be 4 charatcers');
        }
        $this->code = strtoupper($code);

        return $this;
    }

    public function getPurpose(): string
    {
        return $this->purpose;
    }

    public function setPurpose(string $purpose): self
    {
        $this->purpose = mb_substr($purpose, 0, 42);

        return $this;
    }

    public function getDueDate(): \DateTime
    {
        return $this->due_date;
    }

    public function setDueDate(?\DateTime $due_date): self
    {
        $this->due_date = $due_date;

        return $this;
    }
}
