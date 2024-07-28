<?php

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

function tokopay_generate_qris($amount, $channel, $reff_id, $user = null)
{
    $url = getenv('TOKOPAY_HOST_URL') . 'v1/order'; // url
    // $reff_id = 'DIGIPAYID-'.strtoupper(substr(md5(Date('YmdHis')), 5, 8)); // kode unik untuk transaksi
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
    ];

    // $res = curl($url . '?merchant=' . getenv('TOKOPAY_MERCHANT_ID') . '&secret=' . getenv('TOKOPAY_SECRET_KEY') . '&metode=' . $channel . '&ref_id=' . $reff_id . '&nominal=' . $amount . '&redirect_url=https://digipayid.com/', false, false, $headers);

    $req['merchant_id'] = getenv('TOKOPAY_MERCHANT_ID');
    $req['kode_channel'] = $channel;
    $req['reff_id'] = $reff_id;
    $req['amount'] = $amount;
    $req['redirect_url'] = 'https://digipayid.com/';
    $req['expired_ts'] = 0;
    $req['signature'] = md5($req['merchant_id'] . ':' . getenv('TOKOPAY_SECRET_KEY') . ':' . $req['reff_id']);
    $req['customer_name'] = 'DIGIPAYID ' .  $user->merchant_name;
    $req['customer_email'] = $user->email;
    $req['customer_phone'] = $user->merchant_wa;
    $bodyReq = json_encode($req);

    $res = curl($url, true, $bodyReq, $headers);
    $resOBJ = json_decode($res);
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

function tokopay_generate_va($amount, $channel)
{
    $url = getenv('TOKOPAY_HOST_URL') . 'v1/order'; // url
    $reff_id = 'DIGIPAYID-' . strtoupper(substr(md5(Date('YmdHis')), 5, 8)); // kode unik untuk transaksi
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
    ];
    $res = curl($url . '?merchant=' . getenv('TOKOPAY_MERCHANT_ID') . '&secret=' . getenv('TOKOPAY_SECRET_KEY') . '&metode=' . $channel . '&ref_id=' . $reff_id . '&nominal=' . $amount . '', false, false, $headers);
    $resOBJ = json_decode($res);
    // print_r($headers);
    // print_r('<br/>');

    $object = new stdClass();
    $object->req = (object) array("reff_id" => $reff_id, "amount" => $amount);
    $object->res = $resOBJ;

    // print_r($object);
    // die();

    return $object;
}

function tokopay_generate_ewallet($amount, $channel)
{
    $url = getenv('TOKOPAY_HOST_URL') . 'v1/order'; // url
    $reff_id = 'DIGIPAYID-' . strtoupper(substr(md5(Date('YmdHis')), 5, 8)); // kode unik untuk transaksi
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
    ];
    $res = curl($url . '?merchant=' . getenv('TOKOPAY_MERCHANT_ID') . '&secret=' . getenv('TOKOPAY_SECRET_KEY') . '&metode=' . $channel . '&ref_id=' . $reff_id . '&nominal=' . $amount . '', false, false, $headers);
    $resOBJ = json_decode($res);
    // print_r($headers);
    // print_r('<br/>');

    $object = new stdClass();
    $object->req = (object) array("reff_id" => $reff_id, "amount" => $amount);
    $object->res = $resOBJ;

    // print_r($object);
    // die();

    return $object;
}

function tokopay_generate_pulsa($amount, $channel)
{
    $url = getenv('TOKOPAY_HOST_URL') . 'v1/order'; // url
    $reff_id = 'DIGIPAYID-' . strtoupper(substr(md5(Date('YmdHis')), 5, 8)); // kode unik untuk transaksi
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
    ];
    $res = curl($url . '?merchant=' . getenv('TOKOPAY_MERCHANT_ID') . '&secret=' . getenv('TOKOPAY_SECRET_KEY') . '&metode=' . $channel . '&ref_id=' . $reff_id . '&nominal=' . $amount . '', false, false, $headers);
    $resOBJ = json_decode($res);
    // print_r($headers);
    // print_r('<br/>');

    $object = new stdClass();
    $object->req = (object) array("reff_id" => $reff_id, "amount" => $amount);
    $object->res = $resOBJ;

    // print_r($object);
    // die();

    return $object;
}
