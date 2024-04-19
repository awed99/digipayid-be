<?php

function brick_get_token() {        
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode(getenv('BRICK_CLIENT_ID').':'.getenv('BRICK_CLIENT_SECRET')),
    ];
    $res = curl(getenv('BRICK_HOST_URL').'auth/token', false, false, $headers);
    $resOBJ = json_decode($res);
    // print_r($headers);
    // print_r('<br/>');

    return $resOBJ->data->accessToken;
}

function brick_generate_qris($amount) {      
    $reff_id = 'DIGIPAYID-'.strtoupper(substr(md5(Date('YmdHis')), 5, 6)); // kode unik untuk transaksi  
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'publicAccessToken: Bearer ' . brick_get_token(),
    ];
    $bodyPost = '{
        "referenceId": "'.$reff_id.'",
        "amount": '.$amount.',
        "expiredAt": "'.date('Y-m-d\TH:i:s', strtotime('1 hour')).'"
    }';
    // print_r($bodyPost);
    $res = curl(getenv('BRICK_HOST_URL').'gs/qris/dynamic', 1, $bodyPost, $headers);
    $resOBJ = json_decode($res);

    $result = Builder::create()
    ->writer(new PngWriter())
    ->writerOptions([])
    ->data($resOBJ->data->qrData)
    ->encoding(new Encoding('UTF-8'))
    ->errorCorrectionLevel(ErrorCorrectionLevel::High)
    ->size(300)
    ->margin(10)
    ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
    ->logoPath(__DIR__.'/../../public/logo.png')
    ->logoResizeToWidth(50)
    ->logoPunchoutBackground(true)
    ->labelText('DIGIPAYID '.$amount)
    ->labelFont(new NotoSans(20))
    ->labelAlignment(LabelAlignment::Center)
    ->validateResult(false)
    ->build();
    $result->saveToFile(__DIR__.'/../../public/qris/QRIS-'.$reff_id.'.png');

    return $result;
}

function brick_generate_qris2($amount) {      
    $reff_id = 'DIGIPAYID-'.strtoupper(substr(md5(Date('YmdHis')), 5, 6)); // kode unik untuk transaksi  
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'publicAccessToken: Bearer ' . get_token_brick(),
    ];
    $bodyPost = '{
        "referenceId": "'.$reff_id.'",
        "amount": '.$amount.',
        "expiredAt": "'.date('Y-m-d\TH:i:s', strtotime('1 hour')).'"
    }';
    // print_r($bodyPost);
    $res = curl(getenv('BRICK_HOST_URL').'gs/qris/dynamic', 1, $bodyPost, $headers);
    $resOBJ = json_decode($res);
}
