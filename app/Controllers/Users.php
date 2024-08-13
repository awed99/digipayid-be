<?php

namespace App\Controllers;

use Config\Services;
use CodeIgniter\Files\File;

class Users extends BaseController
{
    public function index()
    {
        echo ('welcome!');
    }

    public function postLogout()
    {
        $session = session();
        $session->remove('login');
        $session->remove('token_login');
    }

    public function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public function postLogin()
    {
        cekValidation('users/login');
        $request = request();
        $response = response();
        $db = db_connect();
        $json = $request->getJSON();
        $email = strtolower($json->email);
        // $user_role = 2;
        $password = hash('sha256', $json->password);
        $builder = $db->table('app_users')->where('email', $email)->where('password', $password)->where('is_active', 1);
        $query   = $builder->get();
        $dataFinal = $query->getRow();

        if (!$dataFinal) {
            echo '{
                "code": 1,
                "error": "Email or Password is incorrect!",
                "message": "Email or Password is incorrect!",
                "data": null
            }';
            $db->close();
            die();
        }

        if ((int)$dataFinal->is_verified < 1) {
            $update0['otp_email'] = random_int(100000, 999999);
            $update0['otp_wa'] = random_int(100000, 999999);

            $db->table('app_users')->where('email', $email)->update($update0);

            $htmlBody = template_email_otp($update0['otp_email']);
            sendMail($email, 'DIGIPAY OTP Register', $htmlBody);
        } else {
            $otp_login = random_int(100000, 999999);
            $update['otp_email'] = $otp_login;
            $update['otp_wa'] = $otp_login;

            $waMessage = "*DIGIPAYID* Kode OTP *Login Merchant " . $dataFinal->merchant_name . "* Adalah *" . $otp_login . "*";
            sendWhatsapp($dataFinal->merchant_wa, $waMessage);

            $htmlBody = template_email_otp($otp_login);
            sendMail($email, 'DIGIPAY OTP Register', $htmlBody);
        }

        if ($dataFinal) {
            // $session = session();

            // $loc = json_decode(curl(getenv('API_LOGS').'logs/create_log_login', 1, 'ip='.$this->get_client_ip()));
            $update["token_login"] = hash('sha256', $email . date('YmdHis'));
            $update["last_login"] = date('Y-m-d H:i:s');

            $postData['ip_address'] = $this->get_client_ip();
            $postData['id_user'] = $dataFinal->id_user;
            $postData['user_role'] = $dataFinal->user_role;
            $postData['token_login'] = $update["token_login"];
            $postData['token_api'] = $dataFinal->token_api;
            $builder0 = $db->table('log_login')->insert($postData);

            $update["last_ip_address"] = $this->get_client_ip();
            if (isset($loc['city'])) {
                $update["last_ip_location"] = $loc['city'] . ', ' . $loc['region_name'] . ', ' . $loc['country_name'];
            }
            // $session->set('login', $dataFinal);
            // $session->set('last_login', date('Y-m-d H:i:s'));
            // $session->set('token_login', $update["token_login"]);
            $builder->where('email', $email);
            $builder->update($update);
            $builder->where('email', $email);
            $query   = $builder->get();
            $dataFinal2 = $query->getRow();

            // $builder3 = $db->table('app_operators');
            // $builder3->select('app_operators.*, base_countries.country');
            // $builder3->join('base_countries', 'base_countries.id = app_operators.id_country', 'left');
            // $builder3->where('app_operators.operator_name <> \'\'');
            // $dataFinal3 = $builder3->get()->getResult();

            // $builder4 = $db->table('setting_banner')->where('is_active', '1')->orderBy('id', 'desc');
            // $query4   = $builder4->get();
            // $dataFinal4 = $query4->getResult();

            if ($dataFinal->user_role === 1) {
                $api_key = $db->table('token_api')->orderBy('id', 'DESC')->limit(1)->get()->getRow()->api_key;
                $dataFinal2->api_key = $api_key;
            }

            $dataOwner = $db->table('app_users')->where('id_user', $dataFinal->id_user_parent)->get()->getRow();
            $db->close();


            //             $waMessage = "*INFO DIGIPAYID* 
            // User *" . $dataFinal->email . "* telah login ke Akun DIGIPAYID Merchant *" . $dataFinal->merchant_name . "*";
            //             sendWhatsapp($dataFinal->merchant_wa, $waMessage);

            //             if ($dataOwner) {
            //                 $waMessage = "*INFO DIGIPAYID* 
            // User *" . $dataFinal->email . "* telah login ke Akun DIGIPAYID Merchant *" . $dataOwner->merchant_name . "*";
            //                 sendWhatsapp($dataOwner->merchant_wa, $waMessage);
            //             }

            $finalData = json_encode($dataFinal2);
            // $finalData3 = json_encode($dataFinal3);
            // $finalData4 = json_encode($dataFinal4);
            echo '{
                "code": 0,
                "error": "",
                "message": "Login successful.",
                "data": ' . $finalData . '
            }';
            // curl(getenv('API_LOGS').'logs/create_log_login', 1, 'id_user='.$dataFinal2->id_user.'&ip_address='.$this->get_client_ip().'&user_role='.$dataFinal2->user_role.'&token_login='.$update["token_login"].'&token_api='.$dataFinal2->token_api);
            // echo curl(getenv('API_TRANSACTIONS').'auth/get_token', 1, 'token_login='.$update["token_login"].'&login='.$finalData);
        } else {
            echo '{
                "code": 1,
                "error": "Email or Password is incorrect!",
                "message": "Email or Password is incorrect!",
                "data": null
            }';
        }
        $db->close();
    }

    public function postChange_password0()
    {
        cekValidation('users/change_password');
        $request = request();
        $dataRequest = $request->getJSON(true);
        $email = $dataRequest['email'];
        $update["token_login"] = hash('sha256', $email . date('YmdHis'));
        $db = db_connect();
        $db->table('app_users')->where('email', $dataRequest['email'])->update($update);
        $db->close();

        $htmlBody = template_forgot_password($update["token_login"]);
        sendMail($dataRequest['email'], 'DIGIPAYID Change Password Request', $htmlBody);

        echo '{
            "code": 0,
            "error": "",
            "message": "We have sent a reset password link to your email address."
        }';
    }

    public function postCheck_valid_token()
    {
        cekValidation('users/check_valid_token');
        $request = request();
        $db = db_connect();
        $res = $db->table('app_users')->where('token_login', $request->header('Authorization')->getValue())->get()->getNumRows();
        $db->close();
        if ($res) {
            echo '{
                "code": 0,
                "error": "",
                "message": "Token is valid."
            }';
        } else {
            echo '{
                "code": 1,
                "error": "Error token!",
                "message": "Your token is not valid!"
            }';
        }
    }

    public function postNew_password()
    {
        cekValidation('users/new_password');
        $request = request();
        $dataPost = $request->getJSON(true);
        $update["password"] = hash('sha256', $dataPost['password']);
        $updateX["token_login"] = hash('sha256', $update["password"] . date('YmdHis'));

        $db = db_connect();
        $db->table('app_users')->where('token_login', $request->header('Authorization')->getValue())->update($update);
        $db->close();

        echo '{
            "code": 0,
            "error": "",
            "message": "Your password has been changed."
        }';
    }

    public function postChange_password_user()
    {
        cekValidation('users/change_password_user');
        $request = request();
        $db = db_connect();
        $dataPost = $request->getJSON();
        $dataRequest = cek_token_login($dataPost);
        $id_user = $db->table('app_users')->where('token_login', $dataPost['token_login'])->limit(1)->get()->getRow()->id_user;
        $password = hash('sha256', $dataRequest['password']);
        $update["password"] = $password;
        $db->table('app_users')->where('id_user', $id_user)->update($update);
        $db->close();
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": "' . $update["password"] . '"
        }';
    }

    public function postGenerate_api_key()
    {
        cekValidation('users/generate_api_key');
        $request = request();
        $update["token_api"] = hash('sha256', getenv('SECRET_KEY') . date('YmdHis'));
        $db = db_connect();
        $db->table('app_users')->where('token_login', $request->header('Authorization')->getValue())->update($update);
        $db->close();

        echo '{
            "code": 0,
            "error": "",
            "message": "Your API key has been generated.",
            "data": "' . $update["token_api"] . '"
        }';
    }

    public function postSave_webhook()
    {
        cekValidation('users/save_webhook');
        $request = request();
        $json = $request->getJSON(true);
        $update["webhook_url"] = $json['webhook_url'];
        $db = db_connect();
        $db->table('app_users')->where('token_login', $request->header('Authorization')->getValue())->update($update);
        $db->close();

        echo '{
            "code": 0,
            "error": "",
            "message": "Webhook has been saved.",
            "data": "' . $update["webhook_url"] . '"
        }';
    }

    public function postIs_exist()
    {
        $request = request();
        $db = db_connect();
        $json = $request->getJSON();
        $type = $json->type;
        $value = $json->value;
        $user_role = 2;
        $dataFinal = $db->table('app_users')->where($type, $value)->get()->getRow();

        if ($dataFinal) {
            echo '{
                "code": 1,
                "error": "' . $type . ' as ' . $value . ' has been registered!",
                "message": "' . $type . ' as ' . $value . ' has been registered!",
                "data": null
            }';
        } else {
            echo '{
                "code": 0,
                "error": "",
                "message": "Successful.",
                "data": null
            }';
        }
        $db->close();
    }

    public function postRegister_user()
    {
        $request = request();
        $response = response();
        $db = db_connect();
        $insert = $request->getJSON(true);
        $insert['email'] = strtolower($insert['email']);
        $insert['password'] = hash('sha256', $insert['password']);
        $insert["token_login"] = hash('sha256', $insert['email'] . date('YmdHis'));


        $isExistMerchant = $db->table('app_users')->where('merchant_name', $insert['merchant_name'])->get()->getRow();
        if ($isExistMerchant) {
            echo '{
                "code": 1,
                "error": "Merchant Sudah Terdaftar!",
                "message": "Merchant Sudah Terdaftar!",
                "data": null
            }';
            die();
        }

        $isExistMerchantWa = $db->table('app_users')->where('merchant_wa', $insert['merchant_wa'])->get()->getRow();
        if ($isExistMerchantWa) {
            echo '{
                "code": 1,
                "error": "No. Whatsapp Sudah Terdaftar!",
                "message": "No. Whatsapp Sudah Terdaftar!",
                "data": null
            }';
            die();
        }

        $isExistEmail = $db->table('app_users')->where('email', $insert['email'])->get()->getRow();
        if ($isExistEmail) {
            echo '{
                "code": 1,
                "error": "Email Sudah Terdaftar!",
                "message": "Email Sudah Terdaftar!",
                "data": null
            }';
            die();
        }

        $builder = $db->table('app_users');
        $builder->insert($insert);
        $builder = $db->table('app_users')->where('email', $insert['email'])->where('password', $insert['password'])->where('user_status', 'ACTIVE')->where('is_active', 1);
        $query   = $builder->get();
        $dataFinal = $query->getRow();

        if ($dataFinal) {
            if ($dataFinal->user_role === 1) {
                $api_key = $db->table('token_api')->orderBy('id', 'DESC')->limit(1)->get()->getRow()->api_key;
                $dataFinal->api_key = $api_key;
            }

            $finalData = json_encode($dataFinal);
            echo '{
                "code": 0,
                "error": "",
                "message": "Register successful.",
                "data": ' . $finalData . '
            }';
            // curl(getenv('API_LOGS').'logs/create_log_login', 1, 'id_user='.$dataFinal->id_user.'&user_role='.$dataFinal->user_role.'&token_login='.$insert["token_login"].'&token_api='.$dataFinal->token_api);
            // echo curl(getenv('API_TRANSACTIONS').'auth/get_token', 1, 'token_login='.$update["token_login"].'&login='.$finalData);
        }
        $db->close();
    }

    public function postGet_token_api()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $dataRequest = cek_token_login($dataPost);
        $db = db_connect();
        $json = $request->getJSON();
        $email = $json->email;
        $token_login = $json->token_login;
        $type = $json->type;
        $update["token_api"] = hash('sha256', $email . $token_login . date('YmdHis'));
        $builder = $db->table('app_users')->where('email', $email)->where('token_login', $token_login)->where('user_role', $type);
        $builder->update($update);
        $db->close();
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $update["token_api"] . '
        }';
    }

    public function postGet_user_data_from_token_api()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $dataRequest = cek_token_login($dataPost);
        $request = request();
        $db = db_connect();
        $json = $request->getJSON();
        $token_api = $json->token_api;
        $builder = $db->table('app_users')->where('token_api', $token_api);
        $dataFinal = $builder->get()->getResult();
        $db->close();
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $dataFinal[0] . '
        }';
    }

    public function postCreate_captcha()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $dataRequest = cek_token_login($dataPost);
        $rand = create_random_captcha();
        $db = db_connect();
        $insert = [];
        $insert['captcha_code'] = $rand;
        $insert['ip_address'] = getUserIP();
        $builder = $db->table('setting_captcha');
        $builder->insert($insert);
        $sig = hash_hmac('sha256', $rand, getenv('SECRET_KEY'));
        $db->close();
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $sig . '
        }';
    }

    public function postRegister()
    {
        cekValidation('users/register');
        $request = request();
        $json = $request->getJSON(true);
        $db = db_connect();
        $insert = $request->getJSON(true);
        $insert['email'] = strtolower($insert['email']);
        $insert['password'] = hash('sha256', $insert['password']);
        $insert['email'] = strtolower($insert['email']);


        $isExistMerchant = $db->table('app_users')->where('merchant_name', $insert['merchant_name'])->get()->getRow();
        if ($isExistMerchant) {
            echo '{
                "code": 1,
                "error": "Merchant Sudah Terdaftar!",
                "message": "Merchant Sudah Terdaftar!",
                "data": null
            }';
            die();
        }

        $isExistMerchantWa = $db->table('app_users')->where('merchant_wa', $insert['merchant_wa'])->get()->getRow();
        if ($isExistMerchantWa) {
            echo '{
                "code": 1,
                "error": "No. Whatsapp Sudah Terdaftar!",
                "message": "No. Whatsapp Sudah Terdaftar!",
                "data": null
            }';
            die();
        }

        $isExistEmail = $db->table('app_users')->where('email', $insert['email'])->get()->getRow();
        if ($isExistEmail) {
            echo '{
                "code": 1,
                "error": "Email Sudah Terdaftar!",
                "message": "Email Sudah Terdaftar!",
                "data": null
            }';
            die();
        }

        $insert["otp_email"] = random_int(100000, 999999);
        $insert["otp_wa"] = random_int(100000, 999999);
        $insert['telp'] = preg_replace('/^0/', '', $insert['merchant_wa']);
        $insert['telp_country_code'] = '62';
        $insert['user_role'] = 2;
        $insert['user_privilege'] = 5;
        $insert['is_active'] = 1;
        $insert['is_verified'] = 0;
        $insert['user_status'] = 'ACTIVE';
        // $insert['token_login'] = hash('sha256', $json->email.date('YmdHis'));
        $builder = $db->table('app_users');
        $builder->insert($insert);
        if ($db->error()['code'] > 0) {
            log_message('error', json_encode($db->error()));

            // RETURN RESPONSE
            $res["code"]     = $db->error()['code'];
            $res["error"] = $db->error()['message'];
            $res["data"] = null;

            $db->close();
            echo json_encode($res);
            die();
        }
        if ($db->affectedRows() == 1) {
            $newUser = $db->table('app_users')->where('email', $insert['email'])->orderBy('id_user', 'DESC')->get()->getRow();
            $db->query("CREATE TABLE app_journal_finance_" . $newUser->id_user . " LIKE master_journal_finance");
            $db->query("CREATE TABLE app_payment_method_" . $newUser->id_user . " LIKE master_payment_method_user");
            $db->query("INSERT INTO app_payment_method_" . $newUser->id_user . " (SELECT * FROM master_payment_method_user where id_payment_method > 0)");
            $db->query("INSERT INTO app_payment_method_" . $newUser->id_user . " (id_payment_method, fee_app) values(0, 500)");
            $db->query("CREATE TABLE app_product_" . $newUser->id_user . " LIKE master_product");
            $db->query("CREATE TABLE app_product_category_" . $newUser->id_user . " LIKE master_product_category");
            $db->query("CREATE TABLE app_transactions_" . $newUser->id_user . " LIKE master_transactions");
            $db->query("CREATE TABLE app_transaction_products_" . $newUser->id_user . " LIKE master_transaction_products");
            $db->query("CREATE TABLE app_transaction_products_temp_" . $newUser->id_user . " LIKE master_transaction_products_temp");


            $waMessage = "*INFO DIGIPAYID* 
Merchant *" . $insert['merchant_name'] . "* berhasil terdaftar.";
            sendWhatsapp($insert['merchant_wa'], $waMessage);

            echo '{
                "code": 0,
                "error": "",
                "message": "Anda berhasil terdaftar!",
                "data": ' . json_encode((object)$insert) . '
            }';


            $htmlBody = template_email_otp($insert["otp_email"]);
            sendMail($insert['email'], 'DIGIPAY OTP Register', $htmlBody);
        } else {
            echo '{
                "code": 1,
                "error": "User sudah terdaftar!",
                "message": "User sudah terdaftar!",
                "data": null
            }';
        }
        $db->close();
    }

    public function postResend_otp()
    {
        cekValidation('users/resend_otp');
        $request = request();
        $json = $request->getJSON(true);
        $db = db_connect();
        $type = $json['type'];
        unset($json['type']);

        $res = $db->table('app_users')->where($json)->get()->getRowArray();

        $db->close();
        if ($res) {
            if (($type) === 'register_otp_email') {
                $htmlBody = template_email_otp($res["otp_email"]);
                sendMail($res['email'], 'DIGIPAY OTP Register', $htmlBody);
            } elseif (($type) === 'register_otp_wa') {
                $waMessage = "*DIGIPAYID* Kode OTP *" . $res["merchant_name"] . "* Adalah *" . $res["otp_wa"] . "*";
                sendWhatsapp($res['merchant_wa'], $waMessage);
            }
            echo '{
                "code": 0,
                "error": "",
                "message": "OTP sudah terkirim."
            }';
        } else {
            echo '{
                "code": 1,
                "error": "Gagal mengirim OTP!",
                "message": "Gagal mengirim OTP!"
            }';
        }
    }

    public function postCheck_valid_otp()
    {
        cekValidation('users/check_valid_otp');
        $request = request();
        $json = $request->getJSON(true);
        $db = db_connect();
        $type = $json['type'];
        unset($json['type']);

        $res = $db->table('app_users')->where($json)->get()->getRowArray();
        $dataOwner = $db->table('app_users')->where('id_user', $res['id_user_parent'])->get()->getRow();

        $db->close();
        if ($res) {
            if (($type) === 'register_otp_email') {
                $waMessage = "*DIGIPAYID* Kode OTP *" . $res["merchant_name"] . "* Adalah *" . $res["otp_wa"] . "*";
                sendWhatsapp($res['merchant_wa'], $waMessage);
            } elseif (($type) === 'register_otp_wa') {
                // $htmlBody = template_email_otp($insert["otp_email"]);
                //         sendMail($res['email'], 'DIGIPAY Change Password Request', $htmlBody);
                $update['is_verified'] = 1;
                $update['is_active'] = 1;
                $update['user_status'] = 'ACTIVE';
                $update['otp_email'] = null;
                $update['otp_wa'] = null;

                $db->table('app_users')->where($json)->update($update);
            } elseif (($type) === 'otp_login') {
                $waMessage = "*INFO DIGIPAYID* 
User *" . $res['email'] . "* telah login ke Akun DIGIPAYID Merchant *" . $res['merchant_name'] . "*";
                sendWhatsapp($res['merchant_wa'], $waMessage);

                if ($dataOwner) {
                    $waMessage = "*INFO DIGIPAYID* 
User *" . $res['email'] . "* telah login ke Akun DIGIPAYID Merchant *" . $dataOwner->merchant_name . "*";
                    sendWhatsapp($dataOwner->merchant_wa, $waMessage);
                }

                echo '{
                    "code": 0,
                    "error": "",
                    "message": "OTP anda valid."
                }';
                die();
            }
            echo '{
                "code": 0,
                "error": "",
                "message": "OTP anda valid."
            }';
        } else {
            echo '{
                "code": 1,
                "error": "OTP anda tidak valid!",
                "message": "OTP anda tidak valid!"
            }';
        }
    }

    public function postGet_valid_otp_forgot_password()
    {
        cekValidation('users/get_valid_otp_forgot_password');
        $request = request();
        $json = $request->getJSON(true);
        $db = db_connect();
        $update0['otp_email'] = random_int(100000, 999999);
        $update0['otp_wa'] = random_int(100000, 999999);

        $db->table('app_users')->where($json)->update($update0);
        $res = $db->table('app_users')->where($json)->get()->getRowArray();

        if ($res) {
            $htmlBody = template_email_otp($update0['otp_email']);
            sendMail($json['email'], 'DIGIPAY OTP Forgot Password', $htmlBody);

            $waMessage = "*DIGIPAYID* Kode OTP Ubah Password *" . $res["merchant_name"] . "* Adalah *" . $res["otp_wa"] . "*";
            sendWhatsapp($res['merchant_wa'], $waMessage);
        }

        $db->close();
        if ($res) {
            echo '{
                "code": 0,
                "error": "",
                "message": "Data anda valid, Silakan masukkan OTP."
            }';
        } else {
            echo '{
                "code": 1,
                "error": "Data anda tidak valid!",
                "message": "Data anda tidak valid!"
            }';
        }
    }

    public function postCheck_valid_otp_forgot_password()
    {
        cekValidation('users/check_valid_otp_forgot_password');
        $request = request();
        $json = $request->getJSON(true);
        $db = db_connect();

        $res = $db->table('app_users')->where($json)->get()->getRowArray();

        $db->close();
        if ($res) {
            $update['otp_email'] = null;
            $update['otp_wa'] = null;
            $update["token_login"] = hash('sha256', $res['email'] . date('YmdHis'));

            $db->table('app_users')->where($json)->update($update);
            echo '{
                "code": 0,
                "error": "",
                "message": "OTP anda valid.",
                "token": "' . $update["token_login"] . '"
            }';
        } else {
            echo '{
                "code": 1,
                "error": "OTP anda tidak valid!",
                "message": "OTP anda tidak valid!"
            }';
        }
    }

    public function postChange_password()
    {
        cekValidation('users/change_password');
        $request = request();
        $dataRequest = $request->getJSON(true);
        $token = $dataRequest['token_login'];
        // $update["token_login"] = hash('sha256', $token.date('YmdHis'));
        $update['password'] = hash('sha256', $dataRequest['password']);
        $db = db_connect();
        $db->table('app_users')->where('token_login', $token)->update($update);
        $res = $db->table('app_users')->where('token_login', $token)->get()->getRowArray();
        $db->close();

        $waMessage = "*DIGIPAYID*
Berhasil Ubah Password *" . $res["merchant_name"] . "*.

Jika anda tidak merasa mengubah password, segera amankan akun anda!";
        sendWhatsapp($res['merchant_wa'], $waMessage);

        echo '{
            "code": 0,
            "error": "",
            "message": "We have sent a reset password link to your email address."
        }';
    }

    public function postTop5()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $dataRequest = cek_token_login($dataPost);
        $db = db_connect();
        $query = $db->query('SELECT 
            appu.*, bto.id_app_service, 
            COUNT(*) AS total_order, COALESCE(ROUND(SUM(bto.price_profit), 2), 0) AS total_profit, 
            COALESCE(ROUND(SUM(bto.price_user), 2), 0) AS total_amount FROM app_users appu 
            LEFT JOIN orders bto ON bto.id_user = appu.id_user 
            where appu.is_active = 1 and appu.user_role = 2 and bto.status = \'Complete\'
            GROUP BY appu.id_user 
            ORDER BY total_order DESC 
            limit 5;');
        $dataFinal = $query->getResult();

        $query2 = $db->query('SELECT 
            id_user, COUNT(id_user) as total_register, email, username, last_ip_address, last_ip_location, created_date, is_active 
            from app_users 
            where is_active = 1 and user_role = 2 
            GROUP BY DATE(created_date) 
            ORDER BY created_date ASC 
            limit 30;');
        $dataFinal2 = $query2->getResult();

        $query3 = $db->query('SELECT 
        appu.id_user, appu.email, appu.username, appu.last_ip_address, appu.last_ip_location, appu.created_date, appu.is_active,
        (select count(bto.id) from orders bto where bto.id_user = appu.id_user) total_order,
        (select count(bto.id) from orders bto where bto.created_date >= DATE(NOW() - INTERVAL 7 DAY) and bto.id_user = appu.id_user and bto.status = \'Complete\') weekly_order,
        (select count(bto.id) from orders bto where bto.created_date >= DATE(NOW() - INTERVAL 7 DAY) and bto.id_user = appu.id_user and bto.status = \'Complete\') weekly_order,
        COALESCE(ROUND((
            (select sum(bft.amount) from otpus_finance.topup_users bft where bft.id_user = appu.id_user and bft.status = \'success\') -
            (select sum(bto.price_user) from orders bto where bto.id_user = appu.id_user and bto.status = \'Complete\')
        ), 2), 0) user_saldo
        from app_users appu
                where is_active = 1 and user_role = 2 
                GROUP BY (appu.id_user)
                ORDER BY appu.created_date DESC
            limit 30;');
        $dataFinal3 = $query3->getResult();
        $db->close();
        $finalData = json_encode($dataFinal);
        $finalData2 = json_encode($dataFinal2);
        $finalData3 = json_encode($dataFinal3);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": {
                "top": ' . $finalData . ',
                "graph": ' . $finalData2 . ',
                "users": ' . $finalData3 . '
            }
        }';
    }

    public function postGet_user_saldo()
    {
        cekValidation('users/get_user_saldo');
        $request = request();
        $dataPost = $request->getJSON();
        $dataRequest = cek_token_login($dataPost);
        $db = db_connect();
        $query = $db->query('SELECT COALESCE(ROUND((
            (select sum(bft.amount) from otpus_finance.topup_users bft where bft.id_user = ' . $dataRequest['id_user'] . ' and bft.status = \'success\') -
            (select sum(bto.price_user) from orders bto where bto.id_user = ' . $dataRequest['id_user'] . ' and bto.status = \'Complete\')
        ), 2), 0) user_saldo;');
        $dataFinal = $query->getRow()->user_saldo;
        $db->close();
        $finalData = json_encode($dataFinal);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }
}
