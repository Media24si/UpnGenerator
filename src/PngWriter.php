<?php

namespace Media24si\UpnGenerator;

use BaconQrCode\Common\Version;
use BaconQrCode\Encoder\Encoder;
use Endroid\QrCode\Bacon\ErrorCorrectionLevelConverter;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\Matrix\Matrix;
use Endroid\QrCode\Matrix\MatrixFactoryInterface;
use Endroid\QrCode\Matrix\MatrixInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\AbstractGdWriter;
use Endroid\QrCode\Writer\Result\GdResult;
use Endroid\QrCode\Writer\Result\PngResult;
use Endroid\QrCode\Writer\Result\ResultInterface;

final class PngWriter extends AbstractGdWriter
{
    public const WRITER_OPTION_COMPRESSION_LEVEL = 'compression_level';

    protected function getMatrix(QrCodeInterface $qrCode): MatrixInterface
    {
        $matrixFactory = new class implements MatrixFactoryInterface
        {
            public function create(QrCodeInterface $qrCode): MatrixInterface
            {
                $baconErrorCorrectionLevel = ErrorCorrectionLevelConverter::convertToBaconErrorCorrectionLevel($qrCode->getErrorCorrectionLevel());
                $baconMatrix = Encoder::encode(
                    $qrCode->getData(),
                    $baconErrorCorrectionLevel,
                    strval($qrCode->getEncoding()),
                    Version::getVersionForNumber(15)
                )->getMatrix();

                $blockValues = [];
                $columnCount = $baconMatrix->getWidth();
                $rowCount = $baconMatrix->getHeight();
                for ($rowIndex = 0; $rowIndex < $rowCount; $rowIndex++) {
                    $blockValues[$rowIndex] = [];
                    for ($columnIndex = 0; $columnIndex < $columnCount; $columnIndex++) {
                        $blockValues[$rowIndex][$columnIndex] = $baconMatrix->get($columnIndex, $rowIndex);
                    }
                }

                return new Matrix(
                    $blockValues,
                    $qrCode->getSize(),
                    $qrCode->getMargin(),
                    $qrCode->getRoundBlockSizeMode()
                );
            }
        };

        return $matrixFactory->create($qrCode);
    }

    public function write(
        QrCodeInterface $qrCode,
        ?LogoInterface $logo = null,
        ?LabelInterface $label = null,
        array $options = []
    ): ResultInterface {
        if (! isset($options[self::WRITER_OPTION_COMPRESSION_LEVEL])) {
            $options[self::WRITER_OPTION_COMPRESSION_LEVEL] = -1;
        }

        /** @var GdResult $gdResult */
        $gdResult = parent::write($qrCode, $logo, $label, $options);

        return new PngResult(
            $gdResult->getMatrix(),
            $gdResult->getImage(),
            $options[self::WRITER_OPTION_COMPRESSION_LEVEL]
        );
    }
}
