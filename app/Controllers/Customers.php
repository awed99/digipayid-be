<?php

namespace App\Controllers;

// use Config\Services;


class Customers extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }

    public function postCheck_nik()
    {
        cekValidation0('customers/check_nik');
        $request = request();
        $req = $request->getJSON(true);

        if (isset($req['nik'])) {
            $db = db_connect();
            $data = $db->table('app_customers')->where('nik', $req['nik'])->get()->getRow();
            // echo ($db->getLastQuery());
            $db->close();
        }

        $res['code'] = (isset($data) && $data) ? 0 : 1;
        $res['data'] = $data ?? (object)array();
        $res['message'] = (isset($data) && $data) ? 'NIK sudah terdaftar.' : 'NIK belum terdaftar.';
        return response()->setStatusCode(200)->setJSON($res);
    }

    public function postCheck_trx()
    {
        cekValidation0('customers/check_trx');
        $request = request();
        $req = $request->getJSON(true);

        $idUser = explode('-', $req['invoice_number'])[1] ?? '0';

        if (isset($req['invoice_number'])) {
            $db = db_connect();
            $data = $db->table('app_transactions_' . $idUser)->where('invoice_number', $req['invoice_number'])
                ->where('status_transaction', 0)->where('status_payment', 0)
                ->get()->getRow();
            $db->close();
        }

        $res['code'] = (isset($data) && $data) ? 0 : 1;
        $res['data'] = $data ?? 'app_transactions_' . $idUser;
        $res['message'] = (isset($data) && $data) ? 'Halaman transaksi valid.' : 'Halaman transaksi tidak valid !';
        return response()->setStatusCode(200)->setJSON($res);
    }

    public function postCreate()
    {
        // cekValidation0('customers/create');
        $request = request();
        $req = $request->getJSON(true);


        $db = db_connect();
        $customer_id = $db->table('app_customers')->where('nik', $req['nik'])->get()->getRow()->customer_id ?? null;
        $db->close();

        $insert['ip_address'] = getUserIP();
        $insert['nama_depan'] = $req['nama_depan'];
        $insert['nama_belakang'] = $req['nama_belakang'];
        $insert['nik'] = $req['nik'];
        $insert['email'] = $req['email'];
        $insert['telp'] = $req['telp'];
        $insert['alamat'] = $req['alamat'];
        $insert['kota'] = $req['kota'];
        $insert['kode_pos'] = $req['kode_pos'];

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'api-version: 2020-05-19',
            'Authorization: Basic ' . base64_encode((strtolower(getenv('XENDIT_ENV')) === 'production') ? getenv('XENDIT_API_KEY') . ':' : getenv('XENDIT_SD_API_KEY') . ':'),
        ];
        // $bodyJSON = json_encode([
        //     "reference_id"      => md5(date('Y-m-d H:i:s')),
        //     "client_reference"  => 'DIGIPAYID',
        //     "type"              => 'INDIVIDUAL',
        //     "mobile_number"     => str_replace(' ', '', str_replace('-', '', preg_replace('/^0/', '+62', (preg_replace('/^\+/', '', $req['telp']))))),
        //     "email"             => $req['email'],
        //     "given_names"        => $req['nama_depan'],
        //     "surname"           => $req['nama_belakang'],
        //     "individual_detail" => [
        //         "given_name"        => $req['nama_depan'],
        //         "surname"           => $req['nama_belakang'],
        //         "email"             => $req['email'],
        //         "mobile_number"     => str_replace(' ', '', str_replace('-', '', preg_replace('/^0/', '+62', (preg_replace('/^\+/', '', $req['telp']))))),
        //         "nationality"       => 'ID',
        //     ],
        //     "identity_accounts" => [
        //         "type"          => 'PAY_LATER',
        //         "country"       => 'ID',
        //         "properties"    => [
        //             "account_id"            => $req['email'],
        //             "account_holder_name"   => $req['nama_depan'] . ' ' . $req['nama_belakang'],
        //             "currency"              => 'IDR',
        //         ],
        //     ],
        //     "addresses" => [
        //         "country"       => 'ID',
        //         "street_line1"  => $req['alamat'],
        //         "city"          => $req['kota'],
        //         "postal_code"   => $req['kode_pos'],
        //     ]

        // ]);

        $bodyJSON = '{
    "reference_id": "' . md5(date('Y-m-d H:i:s')) . '",
    "type": "INDIVIDUAL",
    "mobile_number": "' . str_replace(' ', '', str_replace('-', '', preg_replace('/^0/', '+62', (preg_replace('/^\+/', '', $req['telp']))))) . '",
    "email": "' . $req['email'] . '",
    "given_names": "' . $req['nama_depan'] . '",
    "surname": "' . $req['nama_belakang'] . '",
    "addresses":[{
        "country":"ID",
        "street_line1":"' . str_replace("\n", ', ', $req['alamat']) . '",
        "city":"' . $req['kota'] . '",
        "postal_code":"' . $req['kode_pos'] . '"
    }]
}';

        // return response()->setStatusCode(200)->setBody($bodyJSON);
        // die;

        // print_r(getenv('XENDIT_API_DOMAIN') . 'customers/' . $customer_id);
        // die;
        if ($customer_id) {
            $xendit = curl(getenv('XENDIT_API_DOMAIN') . 'customers/' . $customer_id, 'PATCH', $bodyJSON, $headers);
        } else {
            $xendit = curl(getenv('XENDIT_API_DOMAIN') . 'customers', true, $bodyJSON, $headers);
        }

        // return response()->setStatusCode(200)->setJSON($xendit);
        $xenditRes = json_decode($xendit);
        // return response()->setStatusCode(200)->setBody(print_r($xenditRes));
        // die;
        $insert['customer_id'] = $xenditRes->id;
        $db = db_connect();
        $db->table('app_customers')->upsert($insert);
        $db->close();

        $res['code'] = 0;
        $res['data_customer'] = $xenditRes;
        $res['data'] = $insert;
        $res['message'] = 'Data sukses tersimpan.';
        return response()->setStatusCode(200)->setJSON($res);
        // echo json_encode($res);
        // $session->close();
    }

    public function postInitiate_paylater()
    {
        // cekValidation0('customers/initiate_paylater');
        $request = request();
        $req = $request->getJSON(true);


        $db = db_connect();
        $customer_id = $db->table('app_customers')->where('nik', $req['nik'])->get()->getRow()->customer_id ?? null;
        $db->close();

        $insert['ip_address'] = getUserIP();
        $insert['nama_depan'] = $req['nama_depan'];
        $insert['nama_belakang'] = $req['nama_belakang'];
        $insert['nik'] = $req['nik'];
        $insert['email'] = $req['email'];
        $insert['telp'] = $req['telp'];
        $insert['alamat'] = $req['alamat'];
        $insert['kota'] = $req['kota'];
        $insert['kode_pos'] = $req['kode_pos'];

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'api-version: 2020-05-19',
            'Authorization: Basic ' . base64_encode((strtolower(getenv('XENDIT_ENV')) === 'production') ? getenv('XENDIT_API_KEY') . ':' : getenv('XENDIT_SD_API_KEY') . ':'),
        ];
        // $bodyJSON = json_encode([
        //     "reference_id"      => md5(date('Y-m-d H:i:s')),
        //     "client_reference"  => 'DIGIPAYID',
        //     "type"              => 'INDIVIDUAL',
        //     "mobile_number"     => str_replace(' ', '', str_replace('-', '', preg_replace('/^0/', '+62', (preg_replace('/^\+/', '', $req['telp']))))),
        //     "email"             => $req['email'],
        //     "given_names"        => $req['nama_depan'],
        //     "surname"           => $req['nama_belakang'],
        //     "individual_detail" => [
        //         "given_name"        => $req['nama_depan'],
        //         "surname"           => $req['nama_belakang'],
        //         "email"             => $req['email'],
        //         "mobile_number"     => str_replace(' ', '', str_replace('-', '', preg_replace('/^0/', '+62', (preg_replace('/^\+/', '', $req['telp']))))),
        //         "nationality"       => 'ID',
        //     ],
        //     "identity_accounts" => [
        //         "type"          => 'PAY_LATER',
        //         "country"       => 'ID',
        //         "properties"    => [
        //             "account_id"            => $req['email'],
        //             "account_holder_name"   => $req['nama_depan'] . ' ' . $req['nama_belakang'],
        //             "currency"              => 'IDR',
        //         ],
        //     ],
        //     "addresses" => [
        //         "country"       => 'ID',
        //         "street_line1"  => $req['alamat'],
        //         "city"          => $req['kota'],
        //         "postal_code"   => $req['kode_pos'],
        //     ]

        // ]);

        $bodyJSON = '{
"reference_id": "' . md5(date('Y-m-d H:i:s')) . '",
"type": "INDIVIDUAL",
"mobile_number": "' . str_replace(' ', '', str_replace('-', '', preg_replace('/^0/', '+62', (preg_replace('/^\+/', '', $req['telp']))))) . '",
"email": "' . $req['email'] . '",
"given_names": "' . $req['nama_depan'] . '",
"surname": "' . $req['nama_belakang'] . '",
"addresses":[{
    "country":"ID",
    "street_line1":"' . $req['alamat'] . '",
    "city":"' . $req['kota'] . '",
    "postal_code":"' . $req['kode_pos'] . '"
}]
}';

        // print_r($bodyJSON);
        // die;

        // print_r('https://api.xendit.co/customers/' . $customer_id);
        // die;
        if ($customer_id) {
            $xendit = curl('https://api.xendit.co/customers/' . $customer_id, 'PATCH', $bodyJSON, $headers);
        } else {
            $xendit = curl('https://api.xendit.co/customers', true, $bodyJSON, $headers);
        }

        // print_r($xendit);
        $xenditRes = json_decode($xendit);
        // print_r($xenditRes);
        // die;
        $insert['customer_id'] = $xenditRes->id;
        $db = db_connect();
        $db->table('app_customers')->upsert($insert);
        $db->close();

        $res['code'] = 0;
        $res['data_customer'] = $xenditRes;
        $res['data'] = $insert;
        $res['message'] = 'Data sukses tersimpan.';
        return response()->setStatusCode(200)->setJSON($res);
        // echo json_encode($res);
        // $session->close();
    }
}
