<?php

namespace App\Controllers;

class Callbacks extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }

    public function postXendit_qris()
    {
        // $db = db_connect();
        $request = request();
        $dtx = $request->getJSON(true);
        $dt = $dtx['data'];
        // print_r($dt);

        // $sampleJSON = '{
        //     "event": "qr.payment",
        //     "api_version": "2022-07-31",
        //     "business_id": "58cd618ba0464eb64acdb246",
        //     "created": "2022-10-22T06:30:05.86474Z", 
        //     "data": {
        //         "id": "qrpy_8182837te-87st-49ing-8696-1239bd4d759c",
        //         "business_id": "58cd618ba0464eb64acdb246",
        //         "currency": "IDR",
        //         "amount": 10000,
        //         "status": "SUCCEEDED",
        //         "created": "2022-10-22T06:30:05.86474Z",
        //         "qr_id": "qr_61cb3576-3a25-4d35-8d15-0e8e3bdba4f2",
        //         "qr_string": "0002010102##########CO.XENDIT.WWW011893600#######14220002152#####414220010303TTT####015CO.XENDIT.WWW02180000000000000000000TTT52045######ID5911XenditQRIS6007Jakarta6105121606##########3k1mOnF73h11111111#3k1mOnF73h6v53033605401163040BDB",
        //         "reference_id": "order-id-1666420204",
        //         "type": "DYNAMIC",
        //         "channel_code": "ID_DANA",
        //         "expires_at": "2022-10-23T09:56:43.60445Z",
        //         "description": "",
        //         "basket": null,
        //         "metadata": null,
        //         "payment_detail": {
        //             "receipt_id": "000111666",
        //             "source": "GOPAY",
        //             "name": null,
        //             "account_details": null
        //         }
        //     }
        // }';
        // $dt = json_decode($sampleJSON, true);
        // print_r($dt);

        $dt['payment_detail_name'] = isset($dt['payment_detail']['name']) ? $dt['payment_detail']['name'] : null;
        $dt['payment_detail_source'] = isset($dt['payment_detail']['source']) ? $dt['payment_detail']['source'] : null;
        $dt['payment_detail_receipt_id'] = isset($dt['payment_detail']['receipt_id']) ? $dt['payment_detail']['receipt_id'] : null;
        $dt['payment_detail_account_details'] = isset($dt['payment_detail']['account_details']) ? $dt['payment_detail']['account_details'] : null;
        unset($dt['payment_detail']);

        // $rawRequestInput = file_get_contents("php://input");

        $db = db_connect();

        $db->table('pg_qris_callback')->insert($dt);

        if ($dt['status'] === 'SUCCEEDED') {
            if ($dt['type'] === 'STATIC') {
                $data = $db->table('view_open_qris_paylater')->where('amount', $dt['amount'])->get()->getRowArray();
                if ($data) {
                    $this->sendNotif_xendit($dt, $data);
                }
            } else {
                $this->sendNotif_xendit($dt);
            }
        }

        $db->close();

        $myfile = fopen("callbacks/QRIS_" . $dt['reference_id'] . "_" . date("YmdHis") . ".txt", "w") or $this->response->setStatusCode(500)->setBody('Unable to open file!');
        $txt = json_encode($dt);
        fwrite($myfile, $txt);
        fclose($myfile);


        header('Content-type: application/json');
        ob_end_clean();
        ignore_user_abort(true); // just to be safe
        ob_start();

        ///////////////////////
        return response()->setStatusCode(200)->setJSON($dt);
        echo '{"status": true}';
        ///////////////////////

        header("Content-Encoding: none"); //send header to avoid the browser side to take content as gzip format
        $size = ob_get_length();
        header("Content-Length: $size");
        header("Connection: close");
        ob_end_flush(); // Strange behaviour, will not work
        flush(); // Unless both are called !

        ignore_user_abort(true); // just to be safe
        session_write_close(); //close session file on server side to avoid blocking other requests

        response()->setJSON($dt);
    }



    private function sendNotif_xendit($dt, $dtx = false)
    {

        $db = db_connect();


        $status = 0;
        if (strtoupper($dt['status']) === 'SUCCEEDED') {
            $status = 1;
        } elseif (strtoupper($dt['status']) === 'COMPLETED') {
            $status = 2;
        }

        if ($dtx) {
            $idUser = $dtx['id_user'] ?? '0';
        } else {
            $idUser = explode('-', $dt['reference_id'])[1] ?? '0';
        }
        $tblTrx = 'app_transactions_' . $idUser;
        // print_r($status);
        // print_r($dt);
        // print_r($idUser);
        // print_r($tblTrx);
        // die;

        $updateTrxUser['status_transaction'] = $status;
        $updateTrxUser['status_payment'] = $status;
        $updateTrxUser['time_transaction_success'] = date('Y-m-d H:i:s');
        $user = $db->table('app_users')->where('id_user', $idUser)->get()->getRow();
        $updated = $db->table($tblTrx)->where('invoice_number', $dt['reference_id'])->get()->getRow();
        // echo ($db->getLastQuery());
        // print_r($updated);
        // die;

        if ($status === 1 && $updated) {
            $db->table($tblTrx)->where('invoice_number', $dt['reference_id'])->update($updateTrxUser);
            $builder = $db->table($tblTrx)->where('invoice_number', $dt['reference_id'])->get();
            $builder1 = $db->table('app_transaction_products_' . $idUser)->where('invoice_number', $dt['reference_id'])->get();
            $dataTRX = $db->table($tblTrx)->where('invoice_number', $dt['reference_id'])->get()->getRowArray();
            print_r($dataTRX);
            die;

            $payment = ((int)$dataTRX['id_payment_method'] === 0) ? null : json_encode(tokopay_generate_qris((int)$dataTRX['amount_to_pay'], $dataTRX['payment_method_code'], $dataTRX['invoice_number'], $user));
            $paymentJSON = str_replace('"{', '{', str_replace('}"', '}', str_replace('""', '', str_replace('\\', '', json_encode($payment)))));

            if (($dataTRX['email_customer'] != '')) {
                sendReceipt('email', $dataTRX, $builder->getRow(), $builder1->getResult(), $user, json_decode($paymentJSON));
            }

            if (($dataTRX['wa_customer'] != '')) {
                sendReceipt('whatsapp', $dataTRX, $builder->getRow(), $builder1->getResult(), $user, json_decode($paymentJSON));
            }

            $updateJournalUser['status'] = $status;
            $updateJournalUser['updated_at'] = date('Y-m-d H:i:s');
            $db->table('app_journal_finance_' . $idUser)->where('invoice_number', $dt['reference_id'])->update($updateJournalUser);

            $updateJournalAdmin['status'] = $status;
            $updateJournalAdmin['updated_at'] = date('Y-m-d H:i:s');
            $db->table('admin_journal_finance')->where('invoice_number', $dt['reference_id'])->update($updateJournalAdmin);

            $id_affiliator = $db->table('app_users')
                ->where('reff_code', $user->reff_code)->where('is_active', 1)->where('is_verified', 1)
                ->where('user_role', 3)->where('user_privilege', 8)
                ->get()->getRow()->id_user;
            $tbl_affiliator = "app_journal_finance_" . $id_affiliator;
            $updateJournalAffiliator['status'] = $status;
            $updateJournalAffiliator['updated_at'] = date('Y-m-d H:i:s');
            $db->table($tbl_affiliator)->where('invoice_number', $dt['reference_id'])->update($updateJournalAffiliator);
        } else {
            $builder = $db->table('app_journal_finance_' . $idUser)->where('invoice_number', $dt['reference_id'])->where('amount_debet', 0)->get();
            $amountDebet = $db->table('app_journal_finance_' . $idUser)->where('invoice_number', $dt['reference_id'])->where('amount_credit', 0)->get()->getRow()->amount_debet;
            // $payment = json_encode(tokopay_generate_qris((int)$dt['data']['total_dibayar'], $dt['data']['payment_channel'], $dt['reference_id']));
            // $paymentJSON = str_replace('"{', '{', str_replace('}"', '}', str_replace('""', '', str_replace('\\', '', json_encode($payment)))));
            if (($user->email != '')) {
                sendReceiptTopup('email', $dt['reference_id'], $builder->getRow(), $amountDebet, $user, $dt);
            }

            if (($user->merchant_wa != '')) {
                sendReceiptTopup('whatsapp', $dt['reference_id'], $builder->getRow(), $amountDebet, $user, $dt);
            }

            $updateJournalUser['status'] = 2;
            $updateJournalUser['updated_at'] = date('Y-m-d H:i:s');
            $db->table('app_journal_finance_' . $idUser)->where('invoice_number', $dt['reference_id'])->update($updateJournalUser);

            $updateJournalAdmin['status'] = $status;
            $updateJournalAdmin['updated_at'] = date('Y-m-d H:i:s');
            $db->table('admin_journal_finance')->where('invoice_number', $dt['reference_id'])->update($updateJournalAdmin);

            $id_affiliator = $db->table('app_users')
                ->where('reff_code', $user->reff_code)->where('is_active', 1)->where('is_verified', 1)
                ->where('user_role', 3)->where('user_privilege', 8)
                ->get()->getRow()->id_user;
            $tbl_affiliator = "app_journal_finance_" . $id_affiliator;
            $updateJournalAffiliator['status'] = $status;
            $updateJournalAffiliator['updated_at'] = date('Y-m-d H:i:s');
            $db->table($tbl_affiliator)->where('invoice_number', $dt['reference_id'])->update($updateJournalAffiliator);
        }

        $db->close();
    }




    public function postTokopay()
    {
        // $db = db_connect();
        $request = request();
        $dt = $request->getJSON(true);
        // print_r($dt);

        // $sampleJSON = '{
        //     "data": {
        //         "created_at": "2023-10-05 14:38:47",
        //         "customer_email": "bintangaul@gmail.com",
        //         "customer_name": "Customer",
        //         "customer_phone": "082217784294",
        //         "merchant_id": "M230906SQFGQ527",
        //         "payment_channel": "QRISREALTIME",
        //         "total_dibayar": 15600,
        //         "total_diterima": 14554,
        //         "updated_at": "2024-10-05 14:38:47"
        //     },
        //     "reference": "TP231005NPNX005088",
        //     "reff_id": "DIGIPAYID-40-F5A7CF6B",
        //     "signature": "f7ab1cca0f6919efd3c9a4868a75ba60",
        //     "status": "Success"
        // }';
        // $dt = json_decode($sampleJSON, true);
        // print_r($dt);

        // $rawRequestInput = file_get_contents("php://input");

        $myfile = fopen("callbacks/" . $dt['reff_id'] . ".txt", "w") or $this->response->setStatusCode(500)->setBody('Unable to open file!');
        $txt = json_encode($dt);
        fwrite($myfile, $txt);
        fclose($myfile);


        header('Content-type: application/json');
        ob_end_clean();
        ignore_user_abort(true); // just to be safe
        ob_start();

        ///////////////////////
        echo '{"status": true}';
        ///////////////////////

        header("Content-Encoding: none"); //send header to avoid the browser side to take content as gzip format
        $size = ob_get_length();
        header("Content-Length: $size");
        header("Connection: close");
        ob_end_flush(); // Strange behaviour, will not work
        flush(); // Unless both are called !

        ignore_user_abort(true); // just to be safe
        session_write_close(); //close session file on server side to avoid blocking other requests

        $this->sendNotif($dt);

        // header('Content-type: application/json');
        // echo '{"status": true}';


        // $idUser = explode('-', $dt['reff_id'])[1] ?? '0';
        // // $user = $db->table('users')->where('id', $idUser)->get()->getRow();

        // $status = 0;
        // if (strtolower($dt['status']) === 'success') {
        //     $status = 1;
        // } elseif (strtolower($dt['status']) === 'completed') {
        //     $status = 2;
        // }

        // $updateTrxUser['status_transaction'] = $status;
        // $updateTrxUser['status_payment'] = $status;
        // $updateTrxUser['time_transaction_success'] = date('Y-m-d H:i:s');
        // $db->table('app_transactions_' . $idUser)->where('invoice_number', $dt['reff_id'])->update($updateTrxUser);

        // if ($status === 1) {
        //     $user = $db->table('app_users')->where('id_user', $idUser)->get()->getRow();
        //     $builder = $db->table('app_transactions_' . $idUser)->where('invoice_number', $dt['reff_id'])->get();
        //     $builder1 = $db->table('app_transaction_products_' . $idUser)->where('invoice_number', $dt['reff_id'])->get();
        //     $dataTRX = $db->table('app_transactions_' . $idUser)->where('invoice_number', $dt['reff_id'])->get()->getRowArray();

        //     $payment = ((int)$dataTRX['id_payment_method'] === 0) ? null : json_encode(tokopay_generate_qris((int)$dataTRX['amount_to_pay'], $dataTRX['payment_method_code'], $dataTRX['invoice_number']));
        //     $paymentJSON = str_replace('"{', '{', str_replace('}"', '}', str_replace('""', '', str_replace('\\', '', json_encode($payment)))));

        //     if (($dataTRX['email_customer'] != '')) {
        //         sendReceipt('email', $dataTRX, $builder->getRow(), $builder1->getResult(), $user, json_decode($paymentJSON));
        //     }

        //     if (($dataTRX['wa_customer'] != '')) {
        //         sendReceipt('whatsapp', $dataTRX, $builder->getRow(), $builder1->getResult(), $user, json_decode($paymentJSON));
        //     }
        // }

        // $updateJournalUser['status'] = $status;
        // $updateJournalUser['updated_at'] = date('Y-m-d H:i:s');
        // $db->table('app_journal_finance_' . $idUser)->where('invoice_number', $dt['reff_id'])->update($updateJournalUser);

        // $updateJournalAdmin['status'] = $status;
        // $updateJournalAdmin['updated_at'] = date('Y-m-d H:i:s');
        // $db->table('admin_journal_finance')->where('invoice_number', $dt['reff_id'])->update($updateJournalAdmin);

        // $db->close();
    }

    public function getTokopay()
    {
        // $db = db_connect();
        $request = request();
        $dt = $request->getJSON(true);
        // print_r($dt);

        // $sampleJSON = '{
        //     "data": {
        //         "created_at": "2023-10-05 14:38:47",
        //         "customer_email": "bintangaul@gmail.com",
        //         "customer_name": "Customer",
        //         "customer_phone": "082217784294",
        //         "merchant_id": "M230906SQFGQ527",
        //         "payment_channel": "QRISREALTIME",
        //         "total_dibayar": 15600,
        //         "total_diterima": 14554,
        //         "updated_at": "2024-10-05 14:38:47"
        //     },
        //     "reference": "TP231005NPNX005088",
        //     "reff_id": "DEPOSIT-40-CF0C0A65",
        //     "signature": "f7ab1cca0f6919efd3c9a4868a75ba60",
        //     "status": "Completed"
        // }';
        // $dt = json_decode($sampleJSON, true);
        // print_r($dt);

        // $rawRequestInput = file_get_contents("php://input");

        $myfile = fopen("callbacks/" . $dt['reff_id'] . ".txt", "w") or $this->response->setStatusCode(500)->setBody('Unable to open file!');
        $txt = json_encode($dt);
        fwrite($myfile, $txt);
        fclose($myfile);


        header('Content-type: application/json');
        ob_end_clean();
        ignore_user_abort(true); // just to be safe
        ob_start();

        ///////////////////////
        echo '{"status": true}';
        ///////////////////////

        header("Content-Encoding: none"); //send header to avoid the browser side to take content as gzip format
        $size = ob_get_length();
        header("Content-Length: $size");
        header("Connection: close");
        ob_end_flush(); // Strange behaviour, will not work
        flush(); // Unless both are called !

        ignore_user_abort(true); // just to be safe
        session_write_close(); //close session file on server side to avoid blocking other requests

        $this->sendNotif($dt);
    }

    public function getTokopay_sample()
    {
        // set_time_limit(2);
        // echo \CodeIgniter\CodeIgniter::CI_VERSION;
        // die();;
        $request = request();
        $dtx = $request->getGetPost();
        $dtx['reff_id'] = $dtx['reff_id'] ?? 'DIGIPAYID-40-AA5E9639';
        $dtx['status'] = $dtx['status'] ?? 'completed';
        // print_r($dtx);
        // die();

        $sampleJSON = '{
            "data": {
                "created_at": "2023-10-05 14:38:47",
                "customer_email": "bintangaul@gmail.com",
                "customer_name": "Customer",
                "customer_phone": "082217784294",
                "merchant_id": "M230906SQFGQ527",
                "payment_channel": "QRISREALTIME",
                "total_dibayar": 15600,
                "total_diterima": 14554,
                "updated_at": "2024-10-05 14:38:47"
            },
            "reference": "TP231005NPNX005088",
            "reff_id": "' . $dtx['reff_id'] . '",
            "signature": "f7ab1cca0f6919efd3c9a4868a75ba60",
            "status": "' . $dtx['status'] . '"
        }';
        $dt = json_decode($sampleJSON, true);
        // print_r($dt);

        // $status = 'success';
        // $status = 'completed';

        // $rawRequestInput = file_get_contents("php://input");

        $myfile = fopen("callbacks/" . $dt['reff_id'] . ".txt", "w") or die("Unable to open file!");
        $txt = json_encode($dt);
        fwrite($myfile, $txt);
        fclose($myfile);


        header('Content-type: application/json');
        ob_end_clean();
        ignore_user_abort(true); // just to be safe
        ob_start();

        ///////////////////////
        echo '{"status": true}';
        ///////////////////////

        header("Content-Encoding: none"); //send header to avoid the browser side to take content as gzip format
        $size = ob_get_length();
        header("Content-Length: $size");
        header("Connection: close");
        ob_end_flush(); // Strange behaviour, will not work
        flush(); // Unless both are called !

        ignore_user_abort(true); // just to be safe
        session_write_close(); //close session file on server side to avoid blocking other requests

        $this->sendNotif($dt);
    }

    private function sendNotif($dt)
    {

        $db = db_connect();

        $idUser = explode('-', $dt['reff_id'])[1] ?? '0';

        $status = 0;
        if (strtolower($dt['status']) === 'success') {
            $status = 1;
        } elseif (strtolower($dt['status']) === 'completed') {
            $status = 2;
        }

        $updateTrxUser['status_transaction'] = $status;
        $updateTrxUser['status_payment'] = $status;
        $updateTrxUser['time_transaction_success'] = date('Y-m-d H:i:s');
        $user = $db->table('app_users')->where('id_user', $idUser)->get()->getRow();
        $updated = $db->table('app_transactions_' . $idUser)->where('invoice_number', $dt['reff_id'])->get()->getRow();

        if ($status === 1 && $updated) {
            $db->table('app_transactions_' . $idUser)->where('invoice_number', $dt['reff_id'])->update($updateTrxUser);
            $builder = $db->table('app_transactions_' . $idUser)->where('invoice_number', $dt['reff_id'])->get();
            $builder1 = $db->table('app_transaction_products_' . $idUser)->where('invoice_number', $dt['reff_id'])->get();
            $dataTRX = $db->table('app_transactions_' . $idUser)->where('invoice_number', $dt['reff_id'])->get()->getRowArray();

            $payment = ((int)$dataTRX['id_payment_method'] === 0) ? null : json_encode(tokopay_generate_qris((int)$dataTRX['amount_to_pay'], $dataTRX['payment_method_code'], $dataTRX['invoice_number'], $user));
            $paymentJSON = str_replace('"{', '{', str_replace('}"', '}', str_replace('""', '', str_replace('\\', '', json_encode($payment)))));

            if (($dataTRX['email_customer'] != '')) {
                sendReceipt('email', $dataTRX, $builder->getRow(), $builder1->getResult(), $user, json_decode($paymentJSON));
            }

            if (($dataTRX['wa_customer'] != '')) {
                sendReceipt('whatsapp', $dataTRX, $builder->getRow(), $builder1->getResult(), $user, json_decode($paymentJSON));
            }

            $updateJournalUser['status'] = $status;
            $updateJournalUser['updated_at'] = date('Y-m-d H:i:s');
            $db->table('app_journal_finance_' . $idUser)->where('invoice_number', $dt['reff_id'])->update($updateJournalUser);

            $updateJournalAdmin['status'] = $status;
            $updateJournalAdmin['updated_at'] = date('Y-m-d H:i:s');
            $db->table('admin_journal_finance')->where('invoice_number', $dt['reff_id'])->update($updateJournalAdmin);


            $tbl_affiliator = "app_journal_finance_" . $db->table('app_users')
                ->where('reff_code', $user->reff_code)->where('is_active', 1)->where('is_verified', 1)
                ->where('user_role', 3)->where('user_privilege', 8)
                ->get()->getRow()->id_user;
            $updateJournalAffiliator['status'] = $status;
            $updateJournalAffiliator['updated_at'] = date('Y-m-d H:i:s');
            $db->table($tbl_affiliator)->where('invoice_number', $dt['reff_id'])->update($updateJournalAffiliator);
        } else {
            $builder = $db->table('app_journal_finance_' . $idUser)->where('invoice_number', $dt['reff_id'])->where('amount_debet', 0)->get();
            $amountDebet = $db->table('app_journal_finance_' . $idUser)->where('invoice_number', $dt['reff_id'])->where('amount_credit', 0)->get()->getRow()->amount_debet;
            // $payment = json_encode(tokopay_generate_qris((int)$dt['data']['total_dibayar'], $dt['data']['payment_channel'], $dt['reff_id']));
            // $paymentJSON = str_replace('"{', '{', str_replace('}"', '}', str_replace('""', '', str_replace('\\', '', json_encode($payment)))));
            if (($user->email != '')) {
                sendReceiptTopup('email', $dt['reff_id'], $builder->getRow(), $amountDebet, $user, $dt);
            }

            if (($user->merchant_wa != '')) {
                sendReceiptTopup('whatsapp', $dt['reff_id'], $builder->getRow(), $amountDebet, $user, $dt);
            }

            $updateJournalUser['status'] = 2;
            $updateJournalUser['updated_at'] = date('Y-m-d H:i:s');
            $db->table('app_journal_finance_' . $idUser)->where('invoice_number', $dt['reff_id'])->update($updateJournalUser);

            $updateJournalAdmin['status'] = $status;
            $updateJournalAdmin['updated_at'] = date('Y-m-d H:i:s');
            $db->table('admin_journal_finance')->where('invoice_number', $dt['reff_id'])->update($updateJournalAdmin);

            $tbl_affiliator = "app_journal_finance_" . $db->table('app_users')
                ->where('reff_code', $user->reff_code)->where('is_active', 1)->where('is_verified', 1)
                ->where('user_role', 3)->where('user_privilege', 8)
                ->get()->getRow()->id_user;
            $updateJournalAffiliator['status'] = $status;
            $updateJournalAffiliator['updated_at'] = date('Y-m-d H:i:s');
            $db->table($tbl_affiliator)->where('invoice_number', $dt['reff_id'])->update($updateJournalAffiliator);
        }

        $db->close();
    }
}
