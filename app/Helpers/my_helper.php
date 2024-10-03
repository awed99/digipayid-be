<?php

// use CodeIgniter\API\ResponseTrait;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;


//Load Composer's autoloader
require '../vendor/autoload.php';

date_default_timezone_set("Asia/Bangkok");

function cekValidation($uri, $_this = false)
{

    // $returnErrorSignature = service('returnErrorSignature');
    // print_r($returnErrorSignature);
    // exit(1);
    $request = request();
    $response = response();

    $secret_key     = trim(getenv("SECRET_KEY"));
    $http_method    = $_SERVER["REQUEST_METHOD"];
    $time           = $request->header('X-Timestamp')->getValue() ?? time();
    $now            = time();

    $pattern = strtoupper($http_method . ":" . $uri . ":" . $time);
    $signature = hash_hmac('sha256', $pattern, $secret_key);

    // print_r($signature);
    // print_r($request->header('X-Signature')->getValue());
    // exit(1);

    if ($signature !== $request->header('X-Signature')->getValue()) {
        $data = [
            "error"    => true,
            "status"    => "102",
            "error_message"   => "Invalid Signature.",
            "message"   => "Invalid Signature.",
            "data"      => []
        ];
        echo json_encode($data);
        exit(1);
        die(1);
        // return ResponseTrait::respond($data);
        // echo $response->setStatusCode(200)
        //     ->setHeader('Connection', 'close')
        //     ->setHeader('content-type', 'application/json')
        //     ->setHeader('Access-Control-Allow-Origin', '*')
        //     ->setHeader('Access-Control-Expose-Headers', '*')
        //     ->setJSON(($data));
        // throw new \Exception('Some message goes here');

        // ResponseTrait::respond($data, 200);
        // print_r($res);
        // return ResponseTrait::fail($res, 200);
        // dd($res);
        // exit($data);
        // die(json_encode($data));
    } elseif ($now > ((int)$time + getenv('TIMEOUT_SIGNATURE'))) {
        $data = [
            "error"    => true,
            "status"    => "101",
            "error_message"   => "Expired Signature.",
            "message"   => "Expired Signature.",
            "data"      => []
        ];
        echo json_encode($data);
        exit(1);
        die(1);
        // return ResponseTrait::respond($data);
        // return $response->setStatusCode(200)
        //     ->setHeader('Connection', 'close')
        //     ->setHeader('content-type', 'application/json')
        //     ->setHeader('Access-Control-Allow-Origin', '*')
        //     ->setHeader('access-control-expose-headers', '*')
        //     ->setJSON(($data));
    }

    $db = db_connect();
    if ($request->header('Authorization')) {
        $builder = $db->table('app_users')->where('token_login', $request->header('Authorization')->getValue());
        $dataUser = $builder->get()->getRow();

        if (isset($dataUser->user_role) && (int)$dataUser->user_role > 1) {
            if ((int)$dataUser->id_user_parent > 0) {
                $saldo = $db->query("SELECT COALESCE((SELECT SUM(amount_credit) FROM `app_journal_finance_" . $dataUser->id_user_parent . "` where status = 2 AND NOT (id_payment_method = 0 AND accounting_type = 1)), 0) - COALESCE((SELECT SUM(amount_debet) FROM `app_journal_finance_" . $dataUser->id_user_parent . "` where status = 2 AND NOT (id_payment_method = 0 AND accounting_type = 1)), 0) as saldo")->getRow()->saldo;
            } else {

                $saldo = $db->query("SELECT COALESCE((SELECT SUM(amount_credit) FROM `app_journal_finance_" . $dataUser->id_user . "` where status = 2 AND NOT (id_payment_method = 0 AND accounting_type = 1)), 0) - COALESCE((SELECT SUM(amount_debet) FROM `app_journal_finance_" . $dataUser->id_user . "` where status = 2 AND NOT (id_payment_method = 0 AND accounting_type = 1)), 0) as saldo")->getRow()->saldo;
            }
            $realSaldo = 0;
        } else {
            $saldo = $db->query("SELECT COALESCE((SELECT SUM(amount_credit) FROM `admin_journal_finance` where status = 2 AND NOT (id_payment_method = 0 AND accounting_type = 1)), 0) - COALESCE((SELECT SUM(amount_debet) FROM `admin_journal_finance` where status = 2 AND NOT (id_payment_method = 0 AND accounting_type = 1)), 0) as saldo")->getRow()->saldo;
            $realSaldo = $db->query("SELECT COALESCE((SELECT SUM(amount_credit) FROM `admin_journal_finance` where status = 2 AND (accounting_type = 1001 OR accounting_type = 2001 OR accounting_type = 3001 OR accounting_type = 8003) AND NOT (id_payment_method = 0 AND accounting_type = 1)), 0) - COALESCE((SELECT SUM(amount_debet) FROM `admin_journal_finance` where status = 2 AND (accounting_type = 4 OR accounting_type = 4002) AND NOT (id_payment_method = 0 AND accounting_type = 1)), 0) as saldo", 0)->getRow()->saldo;
        }

        if ($dataUser) {
            $dataUser->saldo = (int)$saldo;
            $dataUser->real_saldo = (int)$realSaldo;
        } else {
            $dataUser = null;
        }
    } else {
        $dataUser = null;
    }
    $db->close();

    return $dataUser;
}

function  generate_signature($uri, $service = null)
{
    $secret_key     = trim(getenv("SECRET_KEY"));
    $http_method    = 'POST';
    $time           = time();

    $pattern = strtoupper($http_method . ":" . $uri . ":" . $time);
    $signature = hash_hmac('sha256', $pattern, $secret_key);

    return [
        "X-Signature" => $signature,
        "X-Timestamp" => $time,
        "Secret-Key"  => $secret_key
    ];
}

function getDomain()
{
    if (isset($_SERVER['SERVER_NAME'])) {
        return 'https://' . $_SERVER['SERVER_NAME'];
    } elseif (isset($_SERVER['HTTP_HOST'])) {
        return 'https://' . $_SERVER['HTTP_HOST'];
    } elseif (isset($_SERVER['SERVER_ADDR'])) {
        return 'https://' . $_SERVER['SERVER_ADDR'];
    } else {
        return 'https://127.0.0.1';
    }
}

function get_token_brick()
{
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode(getenv('BRICK_CLIENT_ID') . ':' . getenv('BRICK_CLIENT_SECRET')),
    ];
    $res = curl(getenv('BRICK_HOST_URL') . 'auth/token', false, false, $headers);
    $resOBJ = json_decode($res);
    // print_r($headers);
    // print_r('<br/>');

    return $resOBJ->data->accessToken;
}

function generate_qris($amount)
{
    $reff_id = 'DIGIPAYID-' . strtoupper(substr(md5(Date('YmdHis')), 5, 6)); // kode unik untuk transaksi  
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'publicAccessToken: Bearer ' . get_token_brick(),
    ];
    $bodyPost = '{
            "referenceId": "' . $reff_id . '",
            "amount": ' . $amount . ',
            "expiredAt": "' . date('Y-m-d\TH:i:s', strtotime('1 hour')) . '"
        }';
    // print_r($bodyPost);
    $res = curl(getenv('BRICK_HOST_URL') . 'gs/qris/dynamic', 1, $bodyPost, $headers);
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
        ->logoPath(__DIR__ . '/../../public/logo.png')
        ->logoResizeToWidth(50)
        ->logoPunchoutBackground(true)
        ->labelText('DIGIPAYID ' . $amount)
        ->labelFont(new NotoSans(20))
        ->labelAlignment(LabelAlignment::Center)
        ->validateResult(false)
        ->build();
    $result->saveToFile(__DIR__ . '/../../public/qris/QRIS-' . $reff_id . '.png');

    return $result;
}

function cek_session_login()
{
    // $request = request();
    // $session = session();

    // if($request->hasHeader('Authorization')) {
    //     $db = db_connect();
    //     $tokenLogin = $request->header('Authorization')->getValue();
    //     $builder = $db->table('app_users')->where('token_login', $tokenLogin);
    //     $dataUser = $builder->get()->getRow();
    //     $db->close();
    //     if ($dataUser) {
    //         $user = $dataUser;
    //         $session->set('login', $user);
    //         $session->set('token_login', $user->token_login);
    //         $session->set('token_api', $user->token_api);
    //     } else {
    //         echo '{
    //             "code": 1,
    //             "error": "Token is not valid!",
    //             "message": "Token is not valid!",
    //             "data": null
    //         }';
    //         exit();
    //     }
    // } else {
    //     echo '{
    //         "code": 1,
    //         "error": "Token is not valid!",
    //         "message": "Token is not valid!",
    //         "data": null
    //     }';
    //     exit();
    // }
}

function cek_token_login($postData)
{
    $request = request();
    // $session = session();

    if (isset($postData['token_login'])) {
        $db = db_connect();
        $tokenLogin = $postData['token_login'];
        // echo getenv('DB_NAME').'.app_users';
        // die();
        $builder = $db->table('app_users')->where('token_login', $tokenLogin);
        $dataUser = $builder->get()->getRow();
        $db->close();
        if ($dataUser) {
            // $user = $dataUser;
            // $session->set('login', $user);
            // $session->set('token_login', $user['token_login']);
            // $session->set('token_api', $user->token_api);

            unset($postData['token_login']);
            return ["user" => $dataUser, "request" => $postData];
        } else {
            echo '{
                    "code": 1,
                    "error": "Token is not valid!",
                    "message": "Token is not valid!",
                    "data": null
                }';
            exit();
        }
    } else {
        echo '{
                "code": 1,
                "error": "Token is not valid!",
                "message": "Token is not valid!",
                "data": null
            }';
        exit();
    }
}

function format_rupiah($angka)
{
    $rupiah = number_format($angka, 0, ',', '.');
    return $rupiah;
}

function curl($url, $isPost = false, $postFields = false, $headers = false, $async = false)
{
    set_time_limit(20);
    ignore_user_abort(false);
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_RESOLVE, [$url]);
    // curl_setopt($ch, CURLOPT_TCP_FASTOPEN, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    if ($isPost) {
        curl_setopt($ch, CURLOPT_POST, $isPost);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    }

    if ($async) {
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 200000); //timeout in seconds
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    } else {
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300); //timeout in seconds
    }

    // In real life you should use something like:
    // curl_setopt($ch, CURLOPT_POSTFIELDS, 
    //          http_build_query(array('postvar1' => 'value1')));
    if ($headers) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    // Receive server response ...
    // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
    // curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    // curl_setopt($ch, CURLOPT_TIMEOUT_MS, 200);


    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);

    $server_output = curl_exec($ch);

    // $info = curl_getinfo($ch);
    // print_r($info);

    // $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // $curl_errno= curl_errno($ch);
    // $error_msg = curl_error($ch);
    // echo $url . ' - ' . $http_status;
    // echo "<br/>";
    // echo $curl_errno;
    // echo "<br/>";
    // echo $error_msg;
    // echo "<br/>";
    // if (curl_errno($ch)) {
    //     $error_msg = curl_error($ch);
    //     print_r($error_msg);
    // }

    // $redirect_url = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    // echo $redirect_url;
    // echo "<br/>";
    // $redirectedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    // echo $redirectedUrl;
    // echo "<br/>";

    curl_close($ch);

    return $server_output;
    // Further processing ...
    // if ($server_output == "OK") { ... } else { ... }
}

function get_services_price0($country)
{
    // print_r($country);
    $data0 = json_decode(curl('https://api.sms-activate.org/stubs/handler_api.php?api_key=' . getenv('API_SERVICE_KEY') . '&action=getPrices&country=' . $country));
    // print_r($data0);
    // die();
    $data1 = get_object_vars(get_object_vars($data0)[$country]);
    // print_r($data1);
    // die();
    $dataX = [];
    foreach ($data1 as $key => $value) {
        $dataX[$key] = (get_object_vars($value)['cost'] * 1.6) . '-' . get_object_vars($value)['count'];
    }
    // print_r($dataX);
    // die();
    return ($dataX);
}

function get_services_price($country)
{
    // print_r($country);
    $data0 = json_decode(curl('https://api.sms-activate.org/stubs/handler_api.php?api_key=' . getenv('API_SERVICE_KEY') . '&action=getPrices&country=' . $country));
    // print_r($data0);
    // die();
    $data1 = get_object_vars(get_object_vars($data0)[$country]);
    // print_r($data1);
    // die();
    $dataX = [];
    foreach ($data1 as $key => $value) {
        $dataX[$key] = (get_object_vars($value)['cost'] * 1) . '-' . get_object_vars($value)['count'];
    }
    // print_r($dataX);
    // die();
    return ($dataX);
}

function upload_file($_request)
{
    $file = $_request->getFile('userfile');
    $validationRule = [
        'userfile' => [
            'label' => 'Image File',
            'rules' => [
                'uploaded[userfile]',
                'is_image[userfile]',
                'mime_in[userfile,image/jpg,image/jpeg,image/gif,image/png,image/webp]',
                'max_size[userfile,100]',
                'max_dims[userfile,1024,768]',
            ],
        ],
    ];
    if ($file->getSizeByUnit('mb') > 2) {
        return ['errors' => "File size must < 2mb!"];
    }
    if (
        $file->getMimeType() !== 'image/jpg' &&
        $file->getMimeType() !== 'image/jpeg' &&
        $file->getMimeType() !== 'image/png' &&
        $file->getMimeType() !== 'image/webp'
    ) {
        return ['errors' => "File type must an image!"];
    }

    $newName = $file->getRandomName();
    $x = $file->move(ROOTPATH  . 'public/images', $newName);

    $data = ['name' => '/images/' . $newName];
    return $data;
    // return view('upload_form', $data);
}

function upload_file_custom($_request, $fileXname)
{
    $file = $_request->getFile($fileXname);
    $validationRule = [
        $fileXname => [
            'label' => 'Image File',
            'rules' => [
                'uploaded[' . $fileXname . ']',
                'is_image[' . $fileXname . ']',
                'mime_in[' . $fileXname . ',image/jpg,image/jpeg,image/gif,image/png,image/webp]',
                'max_size[' . $fileXname . ',100]',
                'max_dims[' . $fileXname . ',1024,768]',
            ],
        ],
    ];
    if ($file->getSizeByUnit('mb') > 2) {
        return ['errors' => "File size must < 2mb!"];
    }
    if (
        $file->getMimeType() !== 'image/jpg' &&
        $file->getMimeType() !== 'image/jpeg' &&
        $file->getMimeType() !== 'image/png' &&
        $file->getMimeType() !== 'image/webp'
    ) {
        return ['errors' => "File type must an image!"];
    }

    $newName = $file->getRandomName();
    $x = $file->move(ROOTPATH  . 'public/images', $newName);

    $data = '/images/' . $newName;
    return $data;
    // return view('upload_form', $data);
}

function create_random_captcha()
{
    $seed = str_split('abcdefghijklmnopqrstuvwxyz'
        . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
        . '0123456789'); // and any other characters
    shuffle($seed); // probably optional since array_is randomized; this may be redundant
    $rand = '';
    foreach (array_rand($seed, 6) as $k) $rand .= $seed[$k];
    return $rand;
}

function getUserIP()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }

    return $ip;
}

function maskingString(string $string = NULL)
{
    if (!$string) {
        return NULL;
    }
    $length = strlen($string);
    $visibleCount = (int) round($length / 4);
    $hiddenCount = $length - ($visibleCount * 2);
    return substr($string, 0, $visibleCount) . str_repeat('*', $hiddenCount) . substr($string, ($visibleCount * -1), $visibleCount);
}


function sendMail($toMail = false, $subject = '', $message = '', $linkAttachment = false)
{
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = getenv('SMTP_HOST');                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = getenv('SMTP_USER');                     //SMTP username
        $mail->Password   = getenv('SMTP_PASS');                               //SMTP password
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
        // $mail->SMTPSecure = getenv('SMTP_TLS');            //Enable implicit TLS encryption
        $mail->Port       = getenv('SMTP_PORT');                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom(getenv('SMTP_USER'), getenv('SMTP_NAME'));
        $mail->addReplyTo(getenv('SMTP_USER'), getenv('SMTP_NAME'));

        if ($toMail) {
            $mail->addAddress($toMail, 'DIGIPAY-ID Customer');     //Add a recipient
        } else {
            $mail->addAddress(getenv('SMTP_USER'), 'DIGIPAY-ID Customer');     //Add a recipient
        }
        // $mail->addAddress('ellen@example.com');               //Name is optional

        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        if ($linkAttachment) {
            //Attachments
            $mail->addAttachment($linkAttachment);         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
        }

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;
        // $mail->AltBody = $message;

        // print_r($mail);
        // die();

        $mail->send();
        // echo 'Message has been sent';
    } catch (Exception $e) {
        // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function sendEmail($toMail = false, $subject = '', $message = '', $linkAttachment = false)
{
    $data['to'] = $toMail;
    $data['subject'] = $subject;
    $data['message'] = $message;
    if ($linkAttachment) {
        $data['link_attachment'] = $linkAttachment;
    }
    curl(getenv('API_DOMAIN_BASE_URL') . 'notifications/send_email', true, http_build_query($data), false, true);
}

function sendWhatsapp($phone, $message, $file = false)
{
    $data = [
        'appkey' => '7205f1ec-f32f-4bed-b700-983535861478',
        'authkey' => '44asQgtUNOe95EkHS9qgSA7VpvTa8884t63vNnDHPILxgM8SAP',
        'to' => str_replace(' ', '', str_replace('-', '', preg_replace('/^0/', '62', (preg_replace('/^\+/', '', $phone))))),
        'message' => $message,
        'sandbox' => 'false'
    ];
    // print_r($data);
    if ($file) {
        if (strpos($file, 'spoo.me') > 0) {
            $data['file'] = $file;
        } else {
            $data['file'] = $file;
            // $data['file'] = urlShortener($file) . '?data.pdf';
        }
        // $data['file'] = urlShortener($file) . '?data.pdf';
    }
    // print_r($data);
    $res = curl('https://app.wapanels.com/api/create-message', true, $data, false, false);
    // print_r($res);
    // die();
    // return $res;
}

function sendWA($phone, $message, $imageLink = null)
{


    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://app.wapanels.com/api/create-message',
        // CURLOPT_RESOLVE => ['https://app.wapanels.com/api/create-message'],
        // CURLOPT_TCP_FASTOPEN => true,
        CURLOPT_ENCODING  => '',
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT_MS => 200000,
        CURLOPT_NOSIGNAL => 1,
        CURLOPT_FRESH_CONNECT => 1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'appkey' => '7205f1ec-f32f-4bed-b700-983535861478',
            'authkey' => '44asQgtUNOe95EkHS9qgSA7VpvTa8884t63vNnDHPILxgM8SAP',
            'to' => str_replace(' ', '', str_replace('-', '', preg_replace('/^0/', '62', (preg_replace('/^\+/', '', $phone))))),
            'message' => $message,
            'file' => $imageLink,
            // 'file' => getDomain().$urlPDF,
            'sandbox' => 'false'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
}

function urlShortener($url)
{
    if (strpos($url, 'spoo.me') > 0) {
        return $url;
    } else {
        $headers = [
            'content-type: application/x-www-form-urlencoded',
            'Accept: application/json',
        ];
        // $data = '{"url": "' . $url . '"}';
        $data = http_build_query(array('url' => $url));
        $res0 = curl('https://spoo.me/', true, $data, $headers);

        $res = json_decode($res0);
        return $res->short_url;
    }
}

function htmlToImage1($html)
{
    $url = 'https://htmlcsstoimage.com/demo_run';
    // $url = 'https://api.pictify.io/image';
    // $data = json_encode(array('html' => $html, 'render_when_ready' => false));
    $data = '{
    "currentPath":[],
    "command":"PJMuYKOvTfmdug7g",
    "controlUniqueId":"",
    "validationTargetPath":"/",
    "renderedResources":["knockout","dotvvm"],
    "commandArgs":[],
    "knownTypeMetadata":["pnX2qnyRfVHcAsCV","2jG/+MprLOr2TIOL","Akzg6iWnvjGyA/HB"],
    "viewModelDiff":{
        "$type":"pnX2qnyRfVHcAsCV",
        "Identifier":"",
        "MessageData":{"$type":"2jG/+MprLOr2TIOL"},
        "HTML":"' . $html . '",
        "$csrfToken":"CfDJ8CeKfhIfjiBCrJD4f8bsrWV3/GdLDBHSrtHuh5BJVsOn4JNaQajEBQ1DojrTQH7KYoyi13CNWd+SRW9OhKHQyKqpZIZup6Jb+sISw7LoIb4kZkZoyCmH//DVnXFhT51DR5DKH6OhxkPbwfzowCx+dMR3LkyRWvBLE+fQseqqzGP2"},
        "viewModelCacheId":"W9E2IBpMxfGtYEy3HCJT4mjEGZUPkcQBUySSTPAOWF4="
    }';
    $header = array(
        'content-type: application/json',
        'Accept: application/json',
        'Origin: https://grabz.it',
        // 'Referer: https://htmlcsstoimage.com/',
        // 'Cookie: GrabzIt_a=1; GrabzIt_TimeZone=Asia/Jakarta; __Host-dotvvm_sid_grabz.it=CfDJ8FI0mwRVLyZDhlXHyl5v6mcpAwgSly1Auvsh9f60GxVyMinK6ZCXnCyTMdFeoerAIQtGkcm0PO5Ajvu1%2BLlAweM8IxShWJLnZY5G0panl0FBE1fajbmNvRWVmzObce4l40wCC7TIzswn92vKdf4ISXzHb5mELWHDxJwi8vhahtKM; _ga=GA1.1.564480216.1727351936; _ga_M2GZQ92JPZ=GS1.1.1727351936.1.1.1727352210.0.0.0; _dd_s=logs=1&id=4e7a651e-a654-462f-ac37-401108ceebfb&created=1727351935916&expire=1727353444357',
        // 'Cookie: auth-token=HTN1J8XP5M; sessionid=k22k174q4o0b0w8crltaiks5jv90xt3x; ph_phc_3ecva80rtrdIJiDyYVwsqjy2YI7CbhbAydPApERhNtU_posthog=%7B%22distinct_id%22%3A%220190ac97-27bc-77aa-a49e-bd729c4865bd%22%2C%22%24sesid%22%3A%5B1721400722933%2C%220190cb7a-42e6-7357-a827-e552e3256733%22%2C1721400705766%5D%7D',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_RESOLVE, [$url]);
    curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_TCP_FASTOPEN, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    $info = curl_getinfo($ch);
    curl_close($ch);

    $res = json_decode($response);

    print_r($res);
    die();

    return ("https://api.grabz.it/services/getjspicture?suppresserrors=1&isAttachment=1&id=" . $res->viewModelDiff->Identifier);
}

function htmlToImageY($html)
{
    $css = <<<EOD
    .box { 
      border: 4px solid #03B875; 
      padding: 20px; 
      font-family: 'Roboto'; 
    }
    EOD;

    $google_fonts = "Roboto";

    $data = array(
        'html' => $html,
        'css' => $css,
        'google_fonts' => $google_fonts
    );

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://hcti.io/v1/image");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 20000);
    // Retrieve your user_id and api_key from https://htmlcsstoimage.com/dashboard
    curl_setopt($ch, CURLOPT_USERPWD, getenv("HTML_TO_IMAGE_ID") . ":" . getenv("HTML_TO_IMAGE_API_KEY"));

    $headers = array();
    $headers[] = "Content-Type: application/x-www-form-urlencoded";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    $res = json_decode($result, true);

    return $res['url'] . '?filename=image.png';
}

function htmlToImageX($html)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.pictify.io/image',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
    "html": ' . $html . '
}',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . getenv('PICTIFY_TOKEN'),
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $image = json_decode($response, true);

    return $image['url'];
}

function htmlToImage0($html)
{
    $url = 'https://api.pictify.io/image/public';
    // $url = 'https://api.pictify.io/image';
    $data = json_encode(array('html' => $html));
    $header = array(
        'content-type: application/json',
        'Accept: application/json',
        // ':authority:: api.pictify.io',
        // ':method:: POST',
        // ':path:: /image/public',
        // ':scheme:: https',
        'priority: u=1, i',
        'sec-fetch-mode: cors',
        'sec-fetch-site: same-site',
        'Origin: https://pictify.io',
        'Referer: https://pictify.io/',
        // 'Cookie: sessionid=9ok9ihjyodxisbk1duqbpa1on7gzxx7h; ph_phc_3ecva80rtrdIJiDyYVwsqjy2YI7CbhbAydPApERhNtU_posthog=%7B%22distinct_id%22%3A%220190ac97-27bc-77aa-a49e-bd729c4865bd%22%2C%22%24sesid%22%3A%5B1727353819541%2C%2201922e2b-309c-7275-82c2-1099622336c7%22%2C1727351435419%5D%7D',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_RESOLVE, [$url]);
    curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_TCP_FASTOPEN, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    // print_r($response);
    // die();

    $info = curl_getinfo($ch);
    // print_r($info);
    curl_close($ch);

    // $res = curl( $url, true, $data, $header, true);

    // print_r($response);
    // die();

    $res = json_decode($response);

    return ($res->image->url);
}

function htmlToImage2($html)
{
    $url = 'https://api.pictify.io/image/public';
    // $url = 'https://api.pictify.io/image';
    $data = json_encode(array('html' => $html));
    $header = array(
        'content-type: application/json',
        'Accept: application/json',
        'Origin: https://pictify.io',
        'Referer: https://pictify.io/',
        // 'Cookie: auth-token=HTN1J8XP5M; sessionid=k22k174q4o0b0w8crltaiks5jv90xt3x; ph_phc_3ecva80rtrdIJiDyYVwsqjy2YI7CbhbAydPApERhNtU_posthog=%7B%22distinct_id%22%3A%220190ac97-27bc-77aa-a49e-bd729c4865bd%22%2C%22%24sesid%22%3A%5B1721400722933%2C%220190cb7a-42e6-7357-a827-e552e3256733%22%2C1721400705766%5D%7D',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_RESOLVE, [$url]);
    curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_TCP_FASTOPEN, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    // print_r($response);
    // die();

    $info = curl_getinfo($ch);
    // print_r($info);
    curl_close($ch);

    // $res = curl( $url, true, $data, $header, true);

    // print_r($response);
    // die();

    $res = json_decode($response);

    return ($res->image->url);
}

function grab_image($url, $saveto)
{
    $header = array(
        'content-type: application/json',
        'Accept: application/json',
        'Origin: https://hcti.io',
        'Referer: https://hcti.io/',
        // 'Cookie: auth-token=HTN1J8XP5M; sessionid=k22k174q4o0b0w8crltaiks5jv90xt3x; ph_phc_3ecva80rtrdIJiDyYVwsqjy2YI7CbhbAydPApERhNtU_posthog=%7B%22distinct_id%22%3A%220190ac97-27bc-77aa-a49e-bd729c4865bd%22%2C%22%24sesid%22%3A%5B1721400722933%2C%220190cb7a-42e6-7357-a827-e552e3256733%22%2C1721400705766%5D%7D',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
    );
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
    $raw = curl_exec($ch);
    // print_r($raw);
    // die()
    curl_close($ch);
    if (file_exists($saveto)) {
        unlink($saveto);
    }
    $fp = fopen($saveto, 'x');
    fwrite($fp, $raw);
    fclose($fp);
}
