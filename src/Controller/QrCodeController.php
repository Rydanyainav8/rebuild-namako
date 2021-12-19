<?php

namespace App\Controller;

use Endroid\QrCode\Builder\BuilderInterface;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCodeBundle\Response\QrCodeResponse;

class QrCodeController
{
    /**
     * @var BuilderInterface
     */
    protected $builder;

    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    public function qrcode($t)
    {
        // $day = date("Ymd");
        // $hour = date("His");
        // $c = $day . $hour;
        // $t = strtotime($c);

        $result = $this->builder
            ->data($t)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10)
            ->build();

        $namePng = uniqid('', '') . '.png';

        // $result->savetoFile((\dirname(__DIR__, 3) . '/public/uploads/qr/' . $namePng));

        return $result->getDataUri();
        // return $result;

        // $response = new QrCodeResponse($result);
        // return $namePng;
    }
}

