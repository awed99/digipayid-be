<?php

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;


function xendit_generate_qris($amount, $channel, $reff_id, $user = null)
{
    $url = getenv('XENDIT_API_DOMAIN') . 'qr_codes'; // url
    // $reff_id = 'DIGIPAYID-'.strtoupper(substr(md5(Date('YmdHis')), 5, 8)); // kode unik untuk transaksi
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'api-version: 2022-07-31'
    ];

    $req['type'] = "DYNAMIC";
    $req['reference_id'] = $reff_id;
    $req['amount'] = $amount;
    $req['currency'] = 'IDR';
    $req['channel_code'] = 'ID_DANA';
    $bodyReq = json_encode($req);

    $res = curl($url, true, $bodyReq, $headers);
    $resOBJ = json_decode($res);
    print_r($resOBJ);
    die;
    unset($resOBJ->data->other);
    unset($resOBJ->data->panduan_pembayaran);
    // print_r($headers);
    // print_r('<br/>');

    if (isset($resOBJ->data->qr_string)) {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($resOBJ->data->qr_string)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->logoPath(__DIR__ . '/../../public/logo.png')
            ->logoResizeToWidth(50)
            ->logoPunchoutBackground(true)
            ->labelText($reff_id)
            ->labelFont(new NotoSans(20))
            ->labelAlignment(LabelAlignment::Center)
            ->validateResult(false)
            ->build();
        $result->saveToFile(__DIR__ . '/../../public/qris/QRIS-' . $reff_id . '.png');
    }

    $object = new stdClass();
    $object->req = (object) array("reff_id" => $reff_id, "amount" => $amount);
    $object->res = $resOBJ;
    // $object->image = $result;
    if (isset($resOBJ->data->qr_string)) {
        $object->image_src = 'qris/QRIS-' . $reff_id . '.png';
    }

    // print_r($object);
    // die();

    return $object;
}
