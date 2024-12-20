<?php

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;


function xendit_generate_qris($amount, $reff_id, $payment_method_code, $payLater = false)
{
    $url = getenv('XENDIT_API_DOMAIN') . 'qr_codes'; // url
    // $reff_id = 'DIGIPAYID-'.strtoupper(substr(md5(Date('YmdHis')), 5, 8)); // kode unik untuk transaksi

    if ($payLater) {
        $object = new stdClass();
        $object->req = (object) array("reff_id" => $reff_id, "amount" => $amount);
        $object->image_src = 'qris/QRIS-PAYLATER.PNG';
        $object->res = (object) array("data" => (object) array("total_bayar" => $amount, "pembayaran" => "QRIS Pay Later", "payment_method_code" => $payment_method_code, "qr_link" => getDomain() . '/qris/QRIS-PAYLATER.PNG'));
        // print_r($object);
        // die();

        return $object;
    }

    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'api-version: 2022-07-31',
        'Authorization: Basic ' . base64_encode((strtolower(getenv('XENDIT_ENV')) === 'production') ? getenv('XENDIT_API_KEY') . ':' : getenv('XENDIT_SD_API_KEY') . ':'),
    ];

    // $req['type'] = $payLater ? "STATIC" : "DYNAMIC";
    $req['type'] = "DYNAMIC";
    $req['reference_id'] = $reff_id;
    if (!$payLater) {
        $req['amount'] = $amount;
    }
    $req['currency'] = 'IDR';
    $req['channel_code'] = 'ID_DANA';
    $bodyReq = json_encode($req);

    $res = curl($url, true, $bodyReq, $headers);
    $resOBJ = (object) array();
    $resOBJ->data = json_decode($res);
    $resOBJ->data->payment_method_code = $payment_method_code;
    $resOBJ->res = (object) array("data" => (object) array("total_bayar" => $amount, "pembayaran" => "QRIS", "payment_method_code" => $payment_method_code, "qr_link" => getDomain() . '/qris/QRIS-' . $reff_id . '.png'));
    // return ($resOBJ);
    // die;
    // unset($resOBJ->data->other);
    // unset($resOBJ->data->panduan_pembayaran);
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
    $object->res = $resOBJ->res;
    $object->data = $resOBJ->data;
    // $object->image = $result;
    if (isset($resOBJ->data->qr_string)) {
        $object->image_src = 'qris/QRIS-' . $reff_id . '.png';
    }

    // print_r($object);
    // die();

    return $object;
}

function xendit_initiate_paylater($cust_id, $trx, $nama, $email='', $discount=0)
{
    $url        = getenv('XENDIT_API_DOMAIN') . 'paylater/plans'; // url
    $urlCharge  = getenv('XENDIT_API_DOMAIN') . 'paylater/charges'; // urlCharge
    // $reff_id = 'DIGIPAYID-'.strtoupper(substr(md5(Date('YmdHis')), 5, 8)); // kode unik untuk transaksi

    $db = db_connect();
    $idUser = explode('-', $trx->invoice_number)[1] ?? '0';
    $trxProducts = $db->table('app_transaction_products_temp_' . $idUser . ' atpt')->join('app_product_category_' . $idUser . ' apc', 'atpt.product_category_id = apc.id_product_category', 'left')
    ->where('atpt.nama', $nama)->get()->getResult();
    $db->close();

    $_trxProducts = array();
    foreach ($trxProducts as $trxProduct) {
        // $trxProduct->url = 'https://digipayid.com';
        $__trxProducts = array(
            'type' => 'PHYSICAL_PRODUCT',
            'reference_id' => $trxProduct->product_code . '-' . strtoupper(substr(md5(Date('YmdHis')), 5, 8)) ?? $trxProduct->product_name . '-' . strtoupper(substr(md5(Date('YmdHis')), 5, 8)),
            'name' => $trxProduct->product_name,
            'description' => $trxProduct->product_desc,
            'net_unit_amount' => (int)$trxProduct->product_price,
            'quantity' => (int)$trxProduct->product_qty,
            'url' => 'https://app.digipayid.com/menu?email=' . $email,
            'category' => $trxProduct->product_category ?? 'Produk UMKM',
        );
        array_push($_trxProducts, $__trxProducts);
    }
    $__trxProducts = array(
        'type' => 'FEE',
        'reference_id' => 'FEE-' . $trx->invoice_number . '-' . strtoupper(substr(md5(Date('YmdHis')), 5, 8)),
        'name' => 'FEE-' . $trx->invoice_number,
        'net_unit_amount' => (int)$trx->fee,
        'quantity' => (int)1,
        'url' => 'https://app.digipayid.com/menu?email=' . $email,
        'category' => 'Fee Transaction',
    );
    array_push($_trxProducts, $__trxProducts);
    
    if ($discount > 0) {
        $__trxProducts2 = array(
            'type' => 'DISCOUNT',
            'reference_id' => 'DISCOUNT-' . $trx->invoice_number . '-' . strtoupper(substr(md5(Date('YmdHis')), 5, 8)),
            'name' => 'DISCOUNT-' . $trx->invoice_number,
            'net_unit_amount' => (int)$discount * -1,
            'quantity' => (int)1,
            'url' => 'https://app.digipayid.com/menu?email=' . $email,
            'category' => 'Discount Transaction',
        );
        array_push($_trxProducts, $__trxProducts2);
    }
    // print_r($_trxProducts);
    // die();

    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode((strtolower(getenv('XENDIT_ENV')) === 'production') ? getenv('XENDIT_API_KEY') . ':' : getenv('XENDIT_SD_API_KEY') . ':'),
    ];

    $req1['external_id'] = $trx->invoice_number;
    $req1['callback_url'] = 'https://be.digipayid.com/callbacks/xendit_qris';
    $req1['type'] = 'DIGITAL_PRODUCT';
    $req1['currency'] = 'IDR';
    $req1['customer_id'] = $cust_id;
    $req1['channel_code'] = $trx->payment_method_code;
    $req1['amount'] = (int)$trx->amount_to_pay;
    $req1['order_items'] = $_trxProducts;

    // return (json_encode($req1));

    $bodyReq1 = json_encode($req1);

    $_res1 = curl($url, true, $bodyReq1, $headers);
    // print_r($_res1);
    // die();

    $res1 = json_decode($_res1);
    // print_r($res1);
    // die();



    $req['plan_id']         = $res1->id;
    $req['reference_id']    = $trx->invoice_number;
    $req['checkout_method'] = "ONE_TIME_PAYMENT";
    $req['success_redirect_url'] = getenv('FE_DOMAIN_BASE_URL');
    $req['failure_redirect_url'] = getenv('FE_DOMAIN_BASE_URL');

    $bodyReq = json_encode($req);

    $res = curl($urlCharge, true, $bodyReq, $headers);

    $_res = json_decode($res);
    // print_r($_res);
    // die();

    // return ($res);


    $object = new stdClass();
    $object->req = (object) array("reff_id" => $trx->invoice_number, "amount" => $trx->amount_to_pay, "payment_method_code" => $trx->payment_method_code);
    $object->res = (object) array("data" => [
        "payment_method_code" => $trx->payment_method_code,
        "pay_url" => getenv('FE_DOMAIN_BASE_URL') . 'paylater?invoice_number=' . $trx->invoice_number,
        "paylater_app_url" => (isset($_res->actions->mobile_deeplink_checkout_url)) ? $_res->actions->mobile_deeplink_checkout_url : $_res->actions->mobile_web_checkout_url,
        "amount" => $trx->amount_to_pay,
    ]);
    $object->data = $_res;

    return ($object);

    /*
{
    "id": "plp_de30828b-20e1-45cf-bb21-250e0d389316",
    "customer_id": "9447e603-0373-4eb7-8a53-e204551c83ec",
    "channel_code": "ID_AKULAKU",
    "currency": "IDR",
    "amount": 401705,
    "order_items": [
        {
            "type": "DIGITAL_PRODUCT",
            "reference_id": "UTAMA001",
            "name": "Produk Fisik Sample",
            "net_unit_amount": 400000,
            "quantity": 1,
            "url": "https://digipayid.com",
            "category": "Utama",
            "subcategory": null,
            "description": null,
            "metadata": null
        },
        {
            "type": "DIGITAL_SERVICE",
            "reference_id": "FEE-DIGIPAYID-40-664166B6",
            "name": "FEE-DIGIPAYID-40-664166B6",
            "net_unit_amount": 1705,
            "quantity": 1,
            "url": "https://digipayid.com",
            "category": "Fee Transaction",
            "subcategory": null,
            "description": null,
            "metadata": null
        }
    ],
    "options": [
        {
            "interval": "MONTH",
            "interval_count": 1,
            "total_recurrence": 1,
            "total_amount": 417900,
            "installment_amount": 417900,
            "downpayment_amount": 0,
            "interest_rate": 4.03,
            "description": "1 month"
        }
    ],
    "created": "2024-10-10T10:43:21.202Z"
}
    */

    // return $object;
}
