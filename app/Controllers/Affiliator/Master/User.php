<?php

namespace App\Controllers\Affiliator\Master;

use Config\Services;
use CodeIgniter\Files\File;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

date_default_timezone_set("Asia/Bangkok");

class User extends ResourceController
{

    use ResponseTrait;

    public function index()
    {
        echo ('welcome!');
    }

    public function postList()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/affiliator/master/user/list');
        $db = db_connect();
        $builder = $db->table('app_users au')->where('au.id_user', $user->id_user)->get()->getResult();
        $db->close();
        $finalData = json_encode($builder);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }
    public function postList_Merchant()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/affiliator/master/user/list_merchant');
        $db = db_connect();
        $builder = $db->table('app_users')->where('reff_code', $user->reff_code)->where('id_user_parent', 0)->get()->getResult();
        $db->close();
        $finalData = json_encode($builder);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }

    public function postUpdate()
    {
        $request = request();
        $dataPost = $request->getJSON(true);
        $user = cekValidation('/affiliator/master/user/update');
        $db = db_connect();
        $builder = $db->table('app_users au');
        $query = $builder->where('id_user', $dataPost['id_user']);
        if (isset($dataPost['password'])) {
            $dataPost['password'] = hash('sha256', $dataPost['password']);
        }
        $query->update($dataPost);
        $dataFinal = $query->where('au.id_user', $user->id_user)->get()->getResult();
        $db->close();
        $finalData = json_encode($dataFinal);

        $waMessage = "*INFO DIGIPAYID* 
Affiliator *" . $dataPost['username'] . "* melakukan perubahan akun anda.";
        sendWA('0' . $dataPost['telp'], $waMessage);

        echo '{
            "code": 0,
            "error": "",
            "message": "Data Anda Berhasil Diubah!",
            "data": ' . $finalData . '
        }';
    }

    public function postGetOtp()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/affiliator/master/user/getOTP');
        $db = db_connect();

        $otp = random_int(100000, 999999);
        $update0['otp_email'] = $otp;
        $update0['otp_wa'] = $otp;

        $db->table('app_users')->where('id_user', $user->id_user)->update($update0);
        $res = $db->table('app_users')->where('id_user', $user->id_user)->get()->getRowArray();
        $db->close();

        $waMessage = "*OTP DIGIPAYID (RAHASIA)*
Kode OTP *Ubah Data " . 'Affiliator' . " " . $res["username"] . "* Adalah *" . $otp . "*";
        sendWhatsapp($res['merchant_wa'], $waMessage);
        $htmlBody = template_email_otp($otp);
        sendMail($res['email'], 'DIGIPAY OTP Ubah Data', $htmlBody);

        echo '{
            "code": 0,
            "error": "",
            "message": "Kode OTP Telah Dikirim Ke Email Dan Whatsapp Anda"
        }';
    }

    public function postResend_otp()
    {
        cekValidation('/affiliator/master/user/resend_otp');
        $request = request();
        $json = $request->getJSON(true);
        $db = db_connect();
        $type = $json['type'];
        unset($json['type']);

        $res = $db->table('app_users')->where($json)->get()->getRowArray();

        $role = 'Affiliator';

        $db->close();
        if ($res) {
            if (($type) === 'otp_email') {
                $htmlBody = template_email_otp($res["otp_email"]);
                sendMail($res['email'], 'DIGIPAY OTP Ubah Data', $htmlBody);
            } elseif (($type) === 'otp_wa') {
                $waMessage = "*OTP DIGIPAYID (RAHASIA)*
Kode OTP *Ubah Data " . $role . " " . $res["username"] . "* Adalah *" . $res["otp_wa"] . "*";
                sendWhatsapp($res['merchant_wa'], $waMessage);
            }
            $data = '{
                "code": 0,
                "error": "",
                "message": "OTP sudah terkirim."
            }';
            return $this->response->setStatusCode(200)->setBody($data);
        } else {
            $data = '{
                "code": 1,
                "error": "Gagal mengirim OTP!",
                "message": "Gagal mengirim OTP!"
            }';
            return $this->response->setStatusCode(200)->setBody($data);
        }
    }

    public function postCheck_valid_otp()
    {
        cekValidation('/affiliator/master/user/check_valid_otp');
        $request = request();
        $json = $request->getJSON(true);
        $db = db_connect();

        $res = $db->table('app_users')->where($json)->get()->getRowArray();
        if ($res) {
            $waMessage = "*INFO DIGIPAYID* 
Anda telah melakukan perubahan data akun anda (*" . $res['email'] . "*).";
            sendWA('0' . $res['telp'], $waMessage);
            $data = '{
            "code": 0,
            "error": "",
            "message": "Data updated successfully!",
            "data": ' . json_encode($res) . '
        }';
            $db->close();
            return $this->response->setStatusCode(200)->setBody($data);
        } else {
            $data = '{
                "code": 1,
                "error": "OTP anda tidak valid!",
                "message": "OTP anda tidak valid!"
            }';
            $db->close();
            return $this->response->setStatusCode(200)->setBody($data);
        }

        $db->close();
    }
}
