<?php

namespace App\Controllers;

class Callbacks extends BaseController
{
    public function index()
    {
        return view('welcome_message');
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

            $updateJournalAdmin['status'] = 2;
            $updateJournalAdmin['updated_at'] = date('Y-m-d H:i:s');
            $db->table('admin_journal_finance')->where('invoice_number', $dt['reff_id'])->update($updateJournalAdmin);
        }

        $db->close();
    }
}
