<?php

namespace App\Controllers\Transactions;

use Config\Services;
use CodeIgniter\Files\File;

date_default_timezone_set("Asia/Bangkok");

class Journal extends BaseController
{
    public function index()
    {
        echo ('welcome!');
    }

    public function postGet_user_saldo()
    {
        $user = cekValidation('/transactions/journal/get_user_saldo');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();

        if ((int)$user->id_user_parent > 0) {
            $saldo = $db->query("SELECT (SELECT SUM(amount_credit) FROM `app_journal_finance_" . $user->id_user_parent . "` where status = 1) - (SELECT SUM(amount_debet) FROM `app_journal_finance_" . $user->id_user_parent . "` where status = 1) as saldo")->getRow()->saldo;
        } else {

            $saldo = $db->query("SELECT (SELECT SUM(amount_credit) FROM `app_journal_finance_" . $user->id_user . "` where status = 1) - (SELECT SUM(amount_debet) FROM `app_journal_finance_" . $user->id_user . "` where status = 1) as saldo")->getRow()->saldo;
        }

        $db->close();
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $saldo . '
        }';
    }

    public function postList()
    {
        $user = cekValidation('/transactions/journal/list');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();
        $where = (isset($dataPost->where)) ? $dataPost->where : '1=1';

        if ((int)$user->id_user_parent > 0) {
            $builder = $db->table('app_journal_finance_' . $user->id_user_parent);
            if (isset($dataPost->start_date)) {
                $builder->where('created_at >=', $dataPost->start_date . ' 00:00:00')->where($where);
            }
            if (isset($dataPost->end_date)) {
                $builder->where('created_at <=', $dataPost->end_date . ' 23:59:59')->where($where);
            }
            $result = $builder->orderBy('id', 'desc')->get()->getResult();
        } else {
            $builder = $db->table('app_journal_finance_' . $user->id_user);
            if (isset($dataPost->start_date)) {
                $builder->where('created_at >=', $dataPost->start_date . ' 00:00:00')->where($where);
            }
            if (isset($dataPost->end_date)) {
                $builder->where('created_at <=', $dataPost->end_date . ' 23:59:59')->where($where);
            }
            $result = $builder->orderBy('id', 'desc')->get()->getResult();
        }

        $db->close();
        $finalData = json_encode($result);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }

    public function postList_settlement()
    {
        $user = cekValidation('/transactions/journal/list_settlement');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();

        if ((int)$user->id_user_parent > 0) {
            $builder = $db->table('app_journal_finance_' . $user->id_user_parent)
                ->groupStart()
                ->groupStart()
                ->where('id_payment_method > 0 AND accounting_type = 1')
                ->orWhere('id_payment_method > 0 AND accounting_type = 2')
                ->groupEnd()
                ->groupStart()
                ->where('status', 1)
                ->groupEnd()
                ->groupEnd();
            if (isset($dataPost->start_date)) {
                $builder->where('created_at >=', $dataPost->start_date . ' 00:00:00');
            }
            if (isset($dataPost->end_date)) {
                $builder->where('created_at <=', $dataPost->end_date . ' 23:59:59');
            }
            $result = $builder->orderBy('id', 'desc')->get()->getResult();
        } else {
            $builder = $db->table('app_journal_finance_' . $user->id_user)
                ->groupStart()
                ->groupStart()
                ->where('id_payment_method > 0 AND accounting_type = 1')
                ->orWhere('id_payment_method > 0 AND accounting_type = 2')
                ->groupEnd()
                ->groupStart()
                ->where('status', 1)
                ->groupEnd()
                ->groupEnd();
            if (isset($dataPost->start_date)) {
                $builder->where('created_at >=', $dataPost->start_date . ' 00:00:00');
            }
            if (isset($dataPost->end_date)) {
                $builder->where('created_at <=', $dataPost->end_date . ' 23:59:59');
            }
            $result = $builder->orderBy('id', 'desc')->get()->getResult();
        }

        $db->close();
        $finalData = json_encode($result);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }

    public function postList_ewallet()
    {
        $user = cekValidation('/transactions/journal/list_ewallet');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();
        $where = (isset($dataPost->where)) ? $dataPost->where : '1=1';


        $dataBankUser = $db->table('app_users')->where('id_user', $user->id_user)->orWhere('id_user_parent', $user->id_user)->get()->getRow();

        if ((int)$user->id_user_parent > 0) {
            $builder = $db->table('app_journal_finance_' . $user->id_user_parent);
            // ->groupStart()
            // ->where('id_payment_method = 0 AND accounting_type = 101')
            // ->orWhere('id_payment_method = 0 AND accounting_type = 1')
            // ->groupEnd();

            if (isset($dataPost->start_date)) {
                $builder->where('created_at >=', $dataPost->start_date . ' 00:00:00')->where($where);
            }
            if (isset($dataPost->end_date)) {
                $builder->where('created_at <=', $dataPost->end_date . ' 23:59:59')->where($where);
            }
            $builder->where('NOT (id_payment_method = 0 AND accounting_type = 1)');

            $result = $builder->orderBy('id', 'desc')->get()->getResult();

            // $saldo = $db->query("SELECT (SELECT SUM(amount_credit) FROM `app_journal_finance_" . $user->id_user_parent . "` where status = 1) - (SELECT SUM(amount_debet) FROM `app_journal_finance_" . $user->id_user_parent . "` where status = 1) as saldo")->getRow()->saldo;
        } else {
            $builder = $db->table('app_journal_finance_' . $user->id_user);
            // ->groupStart()
            // ->where('id_payment_method = 0 AND accounting_type = 101')
            // ->orWhere('id_payment_method = 0 AND accounting_type = 1')
            // ->groupEnd();

            if (isset($dataPost->start_date)) {
                $builder->where('created_at >=', $dataPost->start_date . ' 00:00:00')->where($where);
            }
            if (isset($dataPost->end_date)) {
                $builder->where('created_at <=', $dataPost->end_date . ' 23:59:59')->where($where);
            }
            $builder->where('NOT (id_payment_method = 0 AND accounting_type = 1)');

            $result = $builder->orderBy('id', 'desc')->get()->getResult();

            // $saldo = $db->query("SELECT (SELECT SUM(amount_credit) FROM `app_journal_finance_" . $user->id_user . "` where status = 1) - (SELECT SUM(amount_debet) FROM `app_journal_finance_" . $user->id_user . "` where status = 1) as saldo")->getRow()->saldo;
        }

        $db->close();
        $finalData = json_encode($result);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . ',
            "data_bank": ' . json_encode($dataBankUser) . ',
            "saldo": ' . ($user->saldo) . '
        }';
    }

    public function postDeposit_ewallet()
    {
        $user = cekValidation('/transactions/journal/deposit_ewallet');
        $request = request();
        $dataPost = $request->getJSON(true);
        $db = db_connect();

        if ((int)$user->id_user_parent > 0) {
            $dataPaymentMethod = $db->table('app_payment_method_' . $user->id_user_parent)
                ->join('master_payment_method', 'master_payment_method.id_payment_method = app_payment_method_' . $user->id_user_parent . '.id_payment_method')
                ->where('payment_method_id_pg', 3)->where('payment_method_code', $dataPost['payment_method_code'])
                ->get()->getRowArray();
        } else {
            $dataPaymentMethod = $db->table('app_payment_method_' . $user->id_user)
                ->join('master_payment_method', 'master_payment_method.id_payment_method = app_payment_method_' . $user->id_user . '.id_payment_method')
                ->where('payment_method_id_pg', 3)->where('payment_method_code', $dataPost['payment_method_code'])
                ->get()->getRowArray();
        }

        $dataPost['invoice_number'] = isset($dataPost['invoice_number']) ? $dataPost['invoice_number'] : 'DEPOSIT-' . $user->id_user . '-' . strtoupper(substr(md5(Date('YmdHis')), 5, 8));
        // $payment = json_encode(tokopay_generate_qris((int)$dataPost['amount'], $dataPost['payment_method_code'], $dataPost['invoice_number'], $user));

        sleep(1);

        $object = (object) array();
        $object->req = (object) array("reff_id" => $dataPost['invoice_number'], "amount" => $dataPost['amount']);
        $object->image_src = 'qris/QRIS-PAYLATER.PNG';
        $object->res = (object) array("data" => (object) array("total_bayar" => $dataPost['amount'], "pembayaran" => $dataPost['payment_method_name'], "payment_method_code" => $dataPost['payment_method_code']));
        $payment = json_encode($object);
        if (getenv('PG') === 'TOKOPAY') {
            $payment = json_encode(tokopay_generate_qris((int)$dataPost['amount'], $dataPost['payment_method_code'], $dataPost['invoice_number'], $user));

            // return response()->setJSON($payment);
            // die;
        } elseif (getenv('PG') === 'XENDIT') {
            $payLater = 'QRIS_PAYLATER';
            if ($dataPost['payment_method_code'] === 'QRIS' || $dataPost['payment_method_code'] === $payLater) {
                $payment = json_encode(xendit_generate_qris($dataPost['amount'], $dataPost['invoice_number'], $dataPost['payment_method_code'], ($dataPost['payment_method_code'] === $payLater ? true : false)));

                // return response()->setJSON($payment);
                // die;
            }
            // if ($dataPost['payment_method_code'] === 'ID_AKULAKU' || $dataPost['payment_method_code'] === 'ID_KREDIVO') {
            //     $payment = (xendit_initiate_paylater($cust_id, $builder->where('invoice_number', $dataPost['invoice_number'])->get()->getRow()));

            //     // return response()->setJSON($payment);
            //     // die;
            // }
        }

        $journal_insert = array();
        $journal_insert_admin = array();

        $journal_insert0['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert0['amount_credit'] = $dataPost['amount'];
        $journal_insert0['amount_debet'] = 0;
        $journal_insert0['accounting_type'] = 2;
        $journal_insert0['status'] = 0;
        $journal_insert0['id_payment_method'] = (int)$dataPaymentMethod['id_payment_method'];
        $journal_insert0['description'] = '' . $dataPost['invoice_number'] . ' (' . $dataPost['payment_method_name'] . ')';
        array_push($journal_insert, $journal_insert0);

        $journal_insert1['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert1['amount_credit'] = 0;
        $journal_insert1['amount_debet'] = (int)$dataPost['fee'];
        $journal_insert1['accounting_type'] = 201;
        $journal_insert1['status'] = 0;
        $journal_insert1['id_payment_method'] = (int)$dataPaymentMethod['id_payment_method'];
        $journal_insert1['description'] = 'Fee ' . $dataPost['invoice_number'] . ' (' . $dataPost['payment_method_name'] . ')';
        array_push($journal_insert, $journal_insert1);



        $fee_original = (int)$dataPaymentMethod['fee_original'];
        $fee_original_percent = (int)$dataPaymentMethod['fee_original_percent'];
        $fee_pg = $fee_original + ($fee_original_percent * (int)$dataPost['amount'] / 100);
        $fee_app = (int)$dataPost['fee'] - $fee_pg;

        $journal_insert_admin0['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert_admin0['id_user'] = $user->id_user;
        $journal_insert_admin0['id_user_parent'] = $user->id_user_parent;
        $journal_insert_admin0['amount_credit'] = (int)$dataPost['amount'];
        $journal_insert_admin0['amount_debet'] = 0;
        $journal_insert_admin0['accounting_type'] = 2;
        $journal_insert_admin0['status'] = 0;
        $journal_insert_admin0['id_payment_method'] = (int)$dataPaymentMethod['id_payment_method'];
        $journal_insert_admin0['description'] = 'User ' . $dataPost['invoice_number'];
        array_push($journal_insert_admin, $journal_insert_admin0);

        $journal_insert_admin1['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert_admin1['id_user'] = $user->id_user;
        $journal_insert_admin1['id_user_parent'] = $user->id_user_parent;
        $journal_insert_admin1['amount_credit'] = $fee_app;
        $journal_insert_admin1['amount_debet'] = 0;
        $journal_insert_admin1['accounting_type'] = 2001;
        $journal_insert_admin1['status'] = 0;
        $journal_insert_admin1['id_payment_method'] = (int)$dataPaymentMethod['id_payment_method'];
        $journal_insert_admin1['description'] = 'Fee ' . $dataPost['invoice_number'] . ' (Keuntungan)';
        array_push($journal_insert_admin, $journal_insert_admin1);

        if (((int)$dataPaymentMethod['id_payment_method'] > 0)) {
            $journal_insert_admin2['invoice_number'] = $dataPost['invoice_number'];
            $journal_insert_admin2['id_user'] = $user->id_user;
            $journal_insert_admin2['id_user_parent'] = $user->id_user_parent;
            $journal_insert_admin2['amount_credit'] = 0;
            $journal_insert_admin2['amount_debet'] = $fee_pg;
            $journal_insert_admin2['accounting_type'] = 2002;
            $journal_insert_admin2['id_payment_method'] = (int)$dataPaymentMethod['id_payment_method'];
            $journal_insert_admin2['status'] = 0;
            $journal_insert_admin2['description'] = 'Fee PG ' . $dataPost['invoice_number'];
            array_push($journal_insert_admin, $journal_insert_admin2);
        }

        // $journal_insert_admin1['id_user'] = $user->id_user;
        // $journal_insert_admin1['id_user_parent'] = $user->id_user_parent;
        // $journal_insert_admin1['amount_credit'] = 0;
        // $journal_insert_admin1['amount_debet'] = (int)$dataPost['pg_fee'];
        // $journal_insert_admin1['accounting_type'] = 102;
        // $journal_insert_admin1['description'] = 'Fee PG '.$dataPost['invoice_number'];
        // array_push($journal_insert_admin, $journal_insert_admin1);

        if ((int)$user->id_user_parent > 0) {
            $builder0 = $db->table('app_journal_finance_' . $user->id_user_parent);
        } else {
            $builder0 = $db->table('app_journal_finance_' . $user->id_user);
        }
        $builder0->insertBatch($journal_insert);
        $db->table('admin_journal_finance')->insertBatch($journal_insert_admin);


        $dataBankUser = $db->table('app_users')->where('id_user', $user->id_user)->orWhere('id_user_parent', $user->id_user)->get()->getRow();

        if ((int)$user->id_user_parent > 0) {
            $builderX = $db->table('app_journal_finance_' . $user->id_user_parent)
                ->where('created_at >=', date("Y-m-01", strtotime(date("Y-m-d"))) . ' 00:00:00')
                ->where('created_at <=', date("Y-m-t", strtotime(date("Y-m-d"))) . ' 23:59:59')
                ->where('NOT (id_payment_method = 0 AND accounting_type = 1)');
            $result = $builderX->orderBy('id', 'desc')->get()->getResult();
        } else {
            $builderX = $db->table('app_journal_finance_' . $user->id_user)
                ->where('created_at >=', date("Y-m-01", strtotime(date("Y-m-d"))) . ' 00:00:00')
                ->where('created_at <=', date("Y-m-t", strtotime(date("Y-m-d"))) . ' 23:59:59')
                ->where('NOT (id_payment_method = 0 AND accounting_type = 1)');
            $result = $builderX->orderBy('id', 'desc')->get()->getResult();
        }

        $db->close();
        $finalData = json_encode($result);
        echo '{
            "code": 0,
            "error": "",
            "message": "Silakan lakukan pembayaran.",
            "data": ' . $finalData . ',
            "payment": ' . $payment . ',
            "data_bank": ' . json_encode($dataBankUser) . ',
            "saldo": ' . ($user->saldo) . '
        }';
    }

    public function postWithdraw_ewallet()
    {
        $user = cekValidation('/transactions/journal/withdraw_ewallet');
        $request = request();
        $dataPost = $request->getJSON(true);
        $db = db_connect();

        if (((int)$dataPost['amount'] + (int)$dataPost['fee']) > $user->saldo) {
            echo '{
                "code": 1,
                "error": "Saldo anda kurang!",
                "message": "Saldo anda kurang!",
                "data": []
            }';

            die();
        }


        if ((int)$user->id_user_parent > 0) {
            $isExistTrx = $db->table('app_journal_finance_' . $user->id_user_parent)
                ->where('(accounting_type = 3 or accounting_type = 9001)')->where('(status = 0 or status = 1)')
                ->get()->getRow();
        } else {
            $isExistTrx = $db->table('app_journal_finance_' . $user->id_user)
                ->where('(accounting_type = 3 or accounting_type = 9001)')->where('(status = 0 or status = 1)')
                ->get()->getRow();
        }
        if ($isExistTrx) {
            echo '{
                "code": 1,
                "error": "Transaksi Withdraw / Transfer Gaji anda masih berlangsung!",
                "message": "Transaksi Withdraw / Transfer Gaji anda masih berlangsung!",
                "data": []
            }';

            die();
        }

        $fee = 5000;
        $feePG = 2775;
        
        $dataPaymentMethod = $db->table('master_bank_payouts')->where('channel_code', $dataPost['payment_method_code'])->get()->getRowArray();

        $dataPost['invoice_number'] = isset($dataPost['invoice_number']) ? $dataPost['invoice_number'] : 'WITHDRAW-' . $user->id_user . '-' . strtoupper(substr(md5(Date('YmdHis')), 5, 8));
        // $payment = json_encode(tokopay_generate_qris((int)$dataPost['amount'], $dataPost['payment_method'], $dataPost['invoice_number']));

        $journal_insert = array();
        $journal_insert_admin = array();

        $journal_insert0['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert0['amount_credit'] = 0;
        $journal_insert0['amount_debet'] = $dataPost['amount'];
        $journal_insert0['accounting_type'] = 3;
        $journal_insert0['status'] = 0;
        $journal_insert0['id_payment_method'] = (int)$dataPaymentMethod['id'];
        $journal_insert0['description'] = '' . $dataPost['invoice_number'] . ' (' . $dataPost['payment_method_name'] . ')';
        array_push($journal_insert, $journal_insert0);

        $journal_insert1['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert1['amount_credit'] = 0;
        $journal_insert1['amount_debet'] = $fee;
        $journal_insert1['accounting_type'] = 301;
        $journal_insert1['status'] = 0;
        $journal_insert1['id_payment_method'] = (int)$dataPaymentMethod['id'];
        $journal_insert1['description'] = 'Fee ' . $dataPost['invoice_number'] . ' (' . $dataPost['payment_method_name'] . ')';
        array_push($journal_insert, $journal_insert1);



        // $fee_original = (int)$dataPaymentMethod['fee_original'];
        // $fee_original_percent = (int)$dataPaymentMethod['fee_original_percent'];
        // $fee_app = (int)$dataPost['fee'] - $fee_original - ($fee_original_percent * (int)$dataPost['amount'] / 100);
        // $fee_pg = $fee_original - ($fee_original_percent * (int)$dataPost['amount'] / 100);

        $journal_insert_admin0['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert_admin0['id_user'] = $user->id_user;
        $journal_insert_admin0['id_user_parent'] = $user->id_user_parent;
        $journal_insert_admin0['amount_credit'] = 0;
        $journal_insert_admin0['amount_debet'] = $dataPost['amount'];
        $journal_insert_admin0['accounting_type'] = 3;
        $journal_insert_admin0['status'] = 0;
        $journal_insert_admin0['id_payment_method'] = (int)$dataPaymentMethod['id'];
        $journal_insert_admin0['description'] = 'User ' . $dataPost['invoice_number'];
        array_push($journal_insert_admin, $journal_insert_admin0);

        $journal_insert_admin1['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert_admin1['id_user'] = $user->id_user;
        $journal_insert_admin1['id_user_parent'] = $user->id_user_parent;
        $journal_insert_admin1['amount_credit'] = $fee - $feePG;
        $journal_insert_admin1['amount_debet'] = 0;
        $journal_insert_admin1['accounting_type'] = 3001;
        $journal_insert_admin1['status'] = 0;
        $journal_insert_admin1['id_payment_method'] = (int)$dataPaymentMethod['id'];
        $journal_insert_admin1['description'] = 'Fee ' . $dataPost['invoice_number'] . ' (Keuntungan)';
        array_push($journal_insert_admin, $journal_insert_admin1);
    
        $journal_insert_admin2['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert_admin2['id_user'] = $user->id_user;
        $journal_insert_admin2['id_user_parent'] = $user->id_user_parent;
        $journal_insert_admin2['amount_credit'] = 0;
        $journal_insert_admin2['amount_debet'] = $feePG;
        $journal_insert_admin2['accounting_type'] = 3001;
        $journal_insert_admin2['status'] = 0;
        $journal_insert_admin2['id_payment_method'] = (int)$dataPaymentMethod['id'];
        $journal_insert_admin2['description'] = 'Fee ' . $dataPost['invoice_number'] . ' (Fee PG)';
        array_push($journal_insert_admin, $journal_insert_admin2);

        // if (((int)$dataPaymentMethod['id'] > 0)) {
        //     $journal_insert_admin2['invoice_number'] = $dataPost['invoice_number'];
        //     $journal_insert_admin2['id_user'] = $user->id_user;
        //     $journal_insert_admin2['id_user_parent'] = $user->id_user_parent;
        //     $journal_insert_admin2['amount_credit'] = 0;
        //     $journal_insert_admin2['amount_debet'] = $fee_pg;
        //     $journal_insert_admin2['accounting_type'] = 3002;
        //     $journal_insert_admin2['status'] = 0;
        //     $journal_insert_admin1['id_payment_method'] = (int)$dataPaymentMethod['id'];
        //     $journal_insert_admin2['description'] = 'Fee PG ' . $dataPost['invoice_number'];
        //     array_push($journal_insert_admin, $journal_insert_admin2);
        // }

        if ((int)$user->id_user_parent > 0) {
            $builder0 = $db->table('app_journal_finance_' . $user->id_user_parent);
        } else {
            $builder0 = $db->table('app_journal_finance_' . $user->id_user);
        }
        $builder0->insertBatch($journal_insert);
        $db->table('admin_journal_finance')->insertBatch($journal_insert_admin);

        $dataBank = array();
        $dataBank['bank_short_name'] = $dataPost['data_bank']['bank_short_name'];
        $dataBank['bank_name'] = $dataPost['data_bank']['bank_name'];
        $dataBank['bank_account'] = $dataPost['bank_account'];
        $dataBank['bank_account_name'] = $dataPost['bank_account_name'];
        $db->table('app_users')->where('id_user', $user->id_user)->orWhere('id_user_parent', $user->id_user)->update($dataBank);

        $dataBankUser = $db->table('app_users')->where('id_user', $user->id_user)->orWhere('id_user_parent', $user->id_user)->get()->getRow();

        if ((int)$user->id_user_parent > 0) {
            $builderX = $db->table('app_journal_finance_' . $user->id_user_parent)
                ->where('created_at >=', date("Y-m-01", strtotime(date("Y-m-d"))) . ' 00:00:00')
                ->where('created_at <=', date("Y-m-t", strtotime(date("Y-m-d"))) . ' 23:59:59')
                ->where('NOT (id_payment_method = 0 AND accounting_type = 1)');
            $result = $builderX->orderBy('id', 'desc')->get()->getResult();
        } else {
            $builderX = $db->table('app_journal_finance_' . $user->id_user)
                ->where('created_at >=', date("Y-m-01", strtotime(date("Y-m-d"))) . ' 00:00:00')
                ->where('created_at <=', date("Y-m-t", strtotime(date("Y-m-d"))) . ' 23:59:59')
                ->where('NOT (id_payment_method = 0 AND accounting_type = 1)');
            $result = $builderX->orderBy('id', 'desc')->get()->getResult();
        }

        // echo $db->getLastQuery();
        // die();

        $db->close();
        $finalData = json_encode($result);
        echo '{
            "code": 0,
            "error": "",
            "message": "Withdraw sedang diproses dalam 1 jam.",
            "data": ' . $finalData . ',
            "data_bank": ' . json_encode($dataBankUser) . ',
            "saldo": ' . ($user->saldo - (int)$dataPost['amount'] - (int)$dataPost['fee']) . '
        }';
    }

    public function postCheck_deposit_ewallet()
    {
        $user = cekValidation('/transactions/journal/check_deposit_ewallet');
        $request = request();
        $dataPost = $request->getJSON(true);
        $db = db_connect();

        $dataPost['invoice_number'] = isset($dataPost['invoice_number']) ? $dataPost['invoice_number'] : 'DEPOSIT-' . $user->id_user . '-' . strtoupper(substr(md5(Date('YmdHis')), 5, 8));


        if ((int)$user->id_user_parent > 0) {
            $trx = $db->table('app_transactions_' . $user->id_user_parent)->where('invoice_number', $dataPost['invoice_number'])->get()->getRow();
        } else {
            $trx = $db->table('app_transactions_' . $user->id_user)->where('invoice_number', $dataPost['invoice_number'])->get()->getRow();
        }


        if (getenv('PG') === 'TOKOPAY') {
            $payment = json_encode(tokopay_generate_qris((int)$dataPost['amount'], $dataPost['payment_method'], $dataPost['invoice_number'], $user));
        } else if (getenv('PG') === 'XENDIT') {
            $payment = ($trx->payment_response);
        } else {
            $payment = '{}';
        }


        $dataBankUser = $db->table('app_users')->where('id_user', $user->id_user)->orWhere('id_user_parent', $user->id_user)->get()->getRow();

        if ((int)$user->id_user_parent > 0) {
            $builderX = $db->table('app_journal_finance_' . $user->id_user_parent)
                ->where('created_at >=', date("Y-m-01", strtotime(date("Y-m-d"))) . ' 00:00:00')
                ->where('created_at <=', date("Y-m-t", strtotime(date("Y-m-d"))) . ' 23:59:59')
                ->where('NOT (id_payment_method = 0 AND accounting_type = 1)');
            $result = $builderX->orderBy('id', 'desc')->get()->getResult();
        } else {
            $builderX = $db->table('app_journal_finance_' . $user->id_user)
                ->where('created_at >=', date("Y-m-01", strtotime(date("Y-m-d"))) . ' 00:00:00')
                ->where('created_at <=', date("Y-m-t", strtotime(date("Y-m-d"))) . ' 23:59:59')
                ->where('NOT (id_payment_method = 0 AND accounting_type = 1)');
            $result = $builderX->orderBy('id', 'desc')->get()->getResult();
        }

        $db->close();
        $finalData = json_encode($result);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . ',
            "payment": ' . $payment . ',
            "data_bank": ' . json_encode($dataBankUser) . '
        }';
    }

    public function postGetOtp()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/transactions/journal/getOTP');
        $db = db_connect();

        $otp = random_int(100000, 999999);
        $update0['otp_email'] = $otp;
        $update0['otp_wa'] = $otp;

        $db->table('app_users')->where('id_user', $user->id_user)->update($update0);
        $res = $db->table('app_users')->where('id_user', $user->id_user)->get()->getRowArray();
        $db->close();

        $waMessage = "*OTP DIGIPAYID (RAHASIA)* 
Kode OTP Penarikan Uang " . 'Merchant' . " *" . $res["merchant_name"] . "* Adalah *" . $otp . "*";
        sendWhatsapp($res['merchant_wa'], $waMessage);
        $htmlBody = template_email_otp($otp);
        sendMail($res['email'], 'DIGIPAY OTP Penarikan Uang', $htmlBody);

        echo '{
            "code": 0,
            "error": "",
            "message": "Kode OTP Telah Dikirim Ke Email Dan Whatsapp Anda"
        }';
    }

    public function postResend_otp()
    {
        cekValidation('/transactions/journal/resend_otp');
        $request = request();
        $json = $request->getJSON(true);
        $db = db_connect();
        $type = $json['type'];
        unset($json['type']);

        $res = $db->table('app_users')->where($json)->get()->getRowArray();

        $role = 'Merchant';

        $db->close();
        if ($res) {
            if (($type) === 'otp_email') {
                $htmlBody = template_email_otp($res["otp_email"]);
                sendMail($res['email'], 'DIGIPAY OTP Penarikan Uang', $htmlBody);
            } elseif (($type) === 'otp_wa') {
                $waMessage = "*OTP DIGIPAYID (RAHASIA)*
Kode OTP Penarikan Uang " . $role . " *" . $res["merchant_name"] . "* Adalah *" . $res["otp_wa"] . "*";
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
        cekValidation('/transactions/journal/check_valid_otp');
        $request = request();
        $json = $request->getJSON(true);
        $db = db_connect();

        $res = $db->table('app_users')->where($json)->get()->getRowArray();
        if ($res) {
            $waMessage = "*INFO DIGIPAYID* 
Anda telah melakukan Penarikan Uang sebagai Merchant *" . $res["merchant_name"] . "* (*" . $res['email'] . "*).";
            sendWhatsapp($res['merchant_wa'], $waMessage);
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

    public function postCheck_valid_otp_gaji()
    {
        $user = cekValidation('/transactions/journal/check_valid_otp_gaji');
        $request = request();
        $json = $request->getJSON(true);
        $db = db_connect();
        $otp['otp_wa'] = $json['otp'];
        $dataPost = $json['data'];

        $res = $db->table('app_users')->where($otp)->get()->getRowArray();
        if ($res) {
            
            $waMessage = "*INFO DIGIPAYID* 
Anda telah melakukan Transfer Gaji sebagai Merchant *" . $res["merchant_name"] . "* (*" . $res['email'] . "*).";
            sendWhatsapp($res['merchant_wa'], $waMessage);
            
            $data = '{
            "code": 0,
            "error": "",
            "message": "Penggajian dalam proses 1 jam kedepan!",
            "data": ' . json_encode($res) . '
        }';
            $db->close();
            $response = $this->sendGaji($dataPost, $user);
            return $this->response->setStatusCode(200)->setBody($response);
        } else {
            $db->close();
            $data = '{
                "code": 1,
                "error": "OTP anda tidak valid!",
                "message": "OTP anda tidak valid!"
            }';
            return $this->response->setStatusCode(200)->setBody($data);
        }

        $db->close();
    }

    public function sendGaji($data, $user)
    {
        // $user = cekValidation('/transactions/journal/withdraw_ewallet');
        // $request = request();
        $dataPost = $data;
        $db = db_connect();
        
        $totalSalary = 0;
        $totalFee = 0;
        $fee = 5000;
        $feePG = 2775;
        
        foreach($data as $dt) {
            $totalSalary = $totalSalary + (int)$dt['salary'];
            $totalFee = $totalFee + (int)$fee;
        }
        

        if (($totalSalary + $totalFee) > $user->saldo) {
            echo '{
                "code": 1,
                "error": "Saldo anda kurang!",
                "message": "Saldo anda kurang!",
                "data": []
            }';

            die();
        }


        if ((int)$user->id_user_parent > 0) {
            $isExistTrx = $db->table('app_journal_finance_' . $user->id_user_parent)
                ->where('(accounting_type = 3 or accounting_type = 9001)')->where('(status = 0 or status = 1)')
                ->get()->getRow();
        } else {
            $isExistTrx = $db->table('app_journal_finance_' . $user->id_user)
                ->where('(accounting_type = 3 or accounting_type = 9001)')->where('(status = 0 or status = 1)')
                ->get()->getRow();
        }
        if ($isExistTrx) {
            echo '{
                "code": 1,
                "error": "Transaksi Withdraw / Transfer Gaji anda masih berlangsung!",
                "message": "Transaksi Withdraw / Transfer Gaji anda masih berlangsung!",
                "data": []
            }';

            die();
        }

        foreach($data as $dt) {
            $dt['amount'] = (int)$dt['salary'];
            $dt['channel_category'] = $dt['bank_name'];
            $dt['channel_code'] = $dt['bank_short_name'];
            
            $dataPaymentMethod = $db->table('master_bank_payouts')->where('channel_code', $dt['bank_short_name'])->get()->getRowArray();
    
            $dt['invoice_number'] = isset($dt['invoice_number']) ? $dt['invoice_number'] : 'GAJI-' . $user->id_user . '-' . strtoupper(substr(md5(Date('YmdHis')), 5, 8)) . '-' . $dt['merchant_wa'] . '-' . $dt['id_user'];
            // $payment = json_encode(tokopay_generate_qris((int)$dataPost['amount'], $dataPost['payment_method'], $dt['invoice_number']));
    
            $journal_insert = array();
            $journal_insert_admin = array();
    
            $journal_insert0['invoice_number'] = $dt['invoice_number'];
            $journal_insert0['amount_credit'] = 0;
            $journal_insert0['amount_debet'] = $dt['salary'];
            $journal_insert0['accounting_type'] = 9001;
            $journal_insert0['status'] = 0;
            $journal_insert0['id_payment_method'] = (int)$dataPaymentMethod['id'];
            $journal_insert0['description'] =  'Transfer Gaji ' . $dt['username'] . ' ' . $dt['invoice_number'] . ' (' . $dt['bank_short_name'] . ')';
            array_push($journal_insert, $journal_insert0);
    
            $journal_insert1['invoice_number'] = $dt['invoice_number'];
            $journal_insert1['amount_credit'] = 0;
            $journal_insert1['amount_debet'] = $fee;
            $journal_insert1['accounting_type'] = 9002;
            $journal_insert1['status'] = 0;
            $journal_insert1['id_payment_method'] = (int)$dataPaymentMethod['id'];
            $journal_insert1['description'] = 'Fee ' . $dt['invoice_number'] . ' (' . $dt['bank_short_name'] . ')';
            array_push($journal_insert, $journal_insert1);
    
    
    
            // $fee_original = (int)$dataPaymentMethod['fee_original'];
            // $fee_original_percent = (int)$dataPaymentMethod['fee_original_percent'];
            // $fee_app = (int)$dt['fee'] - $fee_original - ($fee_original_percent * (int)$dt['amount'] / 100);
            // $fee_pg = $fee_original - ($fee_original_percent * (int)$dt['amount'] / 100);
    
            $journal_insert_admin0['invoice_number'] = $dt['invoice_number'];
            $journal_insert_admin0['id_user'] = $user->id_user;
            $journal_insert_admin0['id_user_parent'] = $user->id_user_parent;
            $journal_insert_admin0['amount_credit'] = 0;
            $journal_insert_admin0['amount_debet'] = $dt['salary'];
            $journal_insert_admin0['accounting_type'] = 9001;
            $journal_insert_admin0['status'] = 0;
            $journal_insert_admin0['id_payment_method'] = (int)$dataPaymentMethod['id'];
            $journal_insert_admin0['description'] = 'Transfer GAJI ' . $dt['username'] . '-' . $dt['invoice_number'];
            array_push($journal_insert_admin, $journal_insert_admin0);
    
            $journal_insert_admin1['invoice_number'] = $dt['invoice_number'];
            $journal_insert_admin1['id_user'] = $user->id_user;
            $journal_insert_admin1['id_user_parent'] = $user->id_user_parent;
            $journal_insert_admin1['amount_credit'] = $fee - $feePG;
            $journal_insert_admin1['amount_debet'] = 0;
            $journal_insert_admin1['accounting_type'] = 9002;
            $journal_insert_admin1['status'] = 0;
            $journal_insert_admin1['id_payment_method'] = (int)$dataPaymentMethod['id'];
            $journal_insert_admin1['description'] = 'Fee Transfer GAJI ' . $dt['username'] . '-' . $dt['invoice_number'] . ' (Keuntungan)';
            array_push($journal_insert_admin, $journal_insert_admin1);
    
            $journal_insert_admin2['invoice_number'] = $dt['invoice_number'];
            $journal_insert_admin2['id_user'] = $user->id_user;
            $journal_insert_admin2['id_user_parent'] = $user->id_user_parent;
            $journal_insert_admin2['amount_credit'] = 0;
            $journal_insert_admin2['amount_debet'] = $feePG;
            $journal_insert_admin2['accounting_type'] = 9002;
            $journal_insert_admin2['status'] = 0;
            $journal_insert_admin2['id_payment_method'] = (int)$dataPaymentMethod['id'];
            $journal_insert_admin2['description'] = 'Fee Transfer GAJI ' . $dt['username'] . '-' . $dt['invoice_number'] . ' (Fee PG)';
            array_push($journal_insert_admin, $journal_insert_admin2);
    
            // if (((int)$dataPaymentMethod['id'] > 0)) {
            //     $journal_insert_admin2['invoice_number'] = $dt['invoice_number'];
            //     $journal_insert_admin2['id_user'] = $user->id_user;
            //     $journal_insert_admin2['id_user_parent'] = $user->id_user_parent;
            //     $journal_insert_admin2['amount_credit'] = 0;
            //     $journal_insert_admin2['amount_debet'] = $fee_pg;
            //     $journal_insert_admin2['accounting_type'] = 3002;
            //     $journal_insert_admin2['status'] = 0;
            //     $journal_insert_admin1['id_payment_method'] = (int)$dataPaymentMethod['id'];
            //     $journal_insert_admin2['description'] = 'Fee PG ' . $dt['invoice_number'];
            //     array_push($journal_insert_admin, $journal_insert_admin2);
            // }
    
            $idUser = 1;
            if ((int)$user->id_user_parent > 0) {
                $idUser = $user->id_user_parent;
                $builder0 = $db->table('app_journal_finance_' . $user->id_user_parent);
            } else {
                $idUser = $user->id_user;
                $builder0 = $db->table('app_journal_finance_' . $user->id_user);
            }
            $builder0->insertBatch($journal_insert);
            $db->table('admin_journal_finance')->insertBatch($journal_insert_admin);
            
            $amountDebet = $db->table('app_journal_finance_' . $idUser)->where('invoice_number', $dt['invoice_number'])->where('accounting_type', 9002)->get()->getRow()->amount_debet;
            $builder = $db->table('app_journal_finance_' . $idUser)->where('invoice_number', $dt['invoice_number'])->where('amount_credit', 0)->get();
            sendReceiptWithdraw('whatsapp', $dt['invoice_number'], $builder->getRow(), $amountDebet, $user, $dt, 'DALAM PROSES 1 JAM');
    
            // $dataBank = array();`
    
            // $dataBankUser = $db->table('app_users')->where('id_user', $user->id_user)->orWhere('id_user_parent', $user->id_user)->get()->getRow();
            
    
            // if ((int)$user->id_user_parent > 0) {
            //     $builderX = $db->table('app_journal_finance_' . $user->id_user_parent)
            //         ->where('created_at >=', date("Y-m-01", strtotime(date("Y-m-d"))) . ' 00:00:00')
            //         ->where('created_at <=', date("Y-m-t", strtotime(date("Y-m-d"))) . ' 23:59:59')
            //         ->where('NOT (id_payment_method = 0 AND accounting_type = 1)');
            //     $result = $builderX->orderBy('id', 'desc')->get()->getResult();
            // } else {
            //     $builderX = $db->table('app_journal_finance_' . $user->id_user)
            //         ->where('created_at >=', date("Y-m-01", strtotime(date("Y-m-d"))) . ' 00:00:00')
            //         ->where('created_at <=', date("Y-m-t", strtotime(date("Y-m-d"))) . ' 23:59:59')
            //         ->where('NOT (id_payment_method = 0 AND accounting_type = 1)');
            //     $result = $builderX->orderBy('id', 'desc')->get()->getResult();
            // }
        }

        // echo $db->getLastQuery();
        // die();

        $db->close();
        // $finalData = json_encode($result);
        return '{
            "code": 0,
            "error": "",
            "message": "Transfer Gaji sedang diproses dalam 1 jam.",
            "data": null,
            "saldo": ' . ($user->saldo - (int)$totalSalary - (int)$totalFee) . '
        }';
    }
}
