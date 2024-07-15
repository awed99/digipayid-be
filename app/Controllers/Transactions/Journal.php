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

        if ((int)$user->id_user_parent > 0) {
            $builder = $db->table('app_journal_finance_' . $user->id_user_parent);
            if (isset($dataPost->start_date)) {
                $builder->where('created_at >=', $dataPost->start_date . ' 00:00:00');
            }
            if (isset($dataPost->end_date)) {
                $builder->where('created_at <=', $dataPost->end_date . ' 23:59:59');
            }
            $result = $builder->orderBy('id', 'desc')->get()->getResult();
        } else {
            $builder = $db->table('app_journal_finance_' . $user->id_user);
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


        $dataBankUser = $db->table('app_users')->where('id_user', $user->id_user)->orWhere('id_user_parent', $user->id_user)->get()->getRow();

        if ((int)$user->id_user_parent > 0) {
            $builder = $db->table('app_journal_finance_' . $user->id_user_parent);
            // ->groupStart()
            // ->where('id_payment_method = 0 AND accounting_type = 101')
            // ->orWhere('id_payment_method > 0 AND accounting_type > 1')
            // ->groupEnd();

            if (isset($dataPost->start_date)) {
                $builder->where('created_at >=', $dataPost->start_date . ' 00:00:00');
            }
            if (isset($dataPost->end_date)) {
                $builder->where('created_at <=', $dataPost->end_date . ' 23:59:59');
            }

            $result = $builder->orderBy('id', 'desc')->get()->getResult();

            // $saldo = $db->query("SELECT (SELECT SUM(amount_credit) FROM `app_journal_finance_" . $user->id_user_parent . "` where status = 1) - (SELECT SUM(amount_debet) FROM `app_journal_finance_" . $user->id_user_parent . "` where status = 1) as saldo")->getRow()->saldo;
        } else {
            $builder = $db->table('app_journal_finance_' . $user->id_user);
            // ->groupStart()
            // ->where('id_payment_method = 0 AND accounting_type = 101')
            // ->orWhere('id_payment_method > 0 AND accounting_type > 1')
            // ->groupEnd();

            if (isset($dataPost->start_date)) {
                $builder->where('created_at >=', $dataPost->start_date . ' 00:00:00');
            }
            if (isset($dataPost->end_date)) {
                $builder->where('created_at <=', $dataPost->end_date . ' 23:59:59');
            }

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
                ->where('payment_method_id_pg', 1)->where('payment_method_name', $dataPost['payment_method_name'])
                ->get()->getRowArray();
        } else {
            $dataPaymentMethod = $db->table('app_payment_method_' . $user->id_user)
                ->join('master_payment_method', 'master_payment_method.id_payment_method = app_payment_method_' . $user->id_user . '.id_payment_method')
                ->where('payment_method_id_pg', 1)->where('payment_method_name', $dataPost['payment_method_name'])
                ->get()->getRowArray();
        }

        $dataPost['invoice_number'] = isset($dataPost['invoice_number']) ? $dataPost['invoice_number'] : 'DEPOSIT-' . $user->id_user . '-' . strtoupper(substr(md5(Date('YmdHis')), 5, 8));
        $payment = json_encode(tokopay_generate_qris((int)$dataPost['amount'], $dataPost['payment_method'], $dataPost['invoice_number']));

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
                ->where('created_at <=', date("Y-m-t", strtotime(date("Y-m-d"))) . ' 23:59:59');
            $result = $builderX->orderBy('id', 'desc')->get()->getResult();
        } else {
            $builderX = $db->table('app_journal_finance_' . $user->id_user)
                ->where('created_at >=', date("Y-m-01", strtotime(date("Y-m-d"))) . ' 00:00:00')
                ->where('created_at <=', date("Y-m-t", strtotime(date("Y-m-d"))) . ' 23:59:59');
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
                ->where('accounting_type', 3)->where('status', 0)
                ->get()->getRow();
        } else {
            $isExistTrx = $db->table('app_journal_finance_' . $user->id_user)
                ->where('accounting_type', 3)->where('status', 0)
                ->get()->getRow();
        }
        if ($isExistTrx) {
            echo '{
                "code": 1,
                "error": "Transaksi Withdraw anda masih berlangsung!",
                "message": "Transaksi Withdraw anda masih berlangsung!",
                "data": []
            }';

            die();
        }

        if ((int)$user->id_user_parent > 0) {
            $dataPaymentMethod = $db->table('app_payment_method_' . $user->id_user_parent)
                ->join('master_payment_method', 'master_payment_method.id_payment_method = app_payment_method_' . $user->id_user_parent . '.id_payment_method')
                ->where('payment_method_id_pg', 1)->where('payment_method_name', $dataPost['payment_method_name'])
                ->get()->getRowArray();
        } else {
            $dataPaymentMethod = $db->table('app_payment_method_' . $user->id_user)
                ->join('master_payment_method', 'master_payment_method.id_payment_method = app_payment_method_' . $user->id_user . '.id_payment_method')
                ->where('payment_method_id_pg', 1)->where('payment_method_name', $dataPost['payment_method_name'])
                ->get()->getRowArray();
        }

        $dataPost['invoice_number'] = isset($dataPost['invoice_number']) ? $dataPost['invoice_number'] : 'WITHDRAW-' . $user->id_user . '-' . strtoupper(substr(md5(Date('YmdHis')), 5, 8));
        // $payment = json_encode(tokopay_generate_qris((int)$dataPost['amount'], $dataPost['payment_method'], $dataPost['invoice_number']));

        $journal_insert = array();
        $journal_insert_admin = array();

        $journal_insert0['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert0['amount_credit'] = 0;
        $journal_insert0['amount_debet'] = $dataPost['amount'];
        $journal_insert0['accounting_type'] = 3;
        $journal_insert0['status'] = 0;
        $journal_insert0['id_payment_method'] = (int)$dataPaymentMethod['id_payment_method'];
        $journal_insert0['description'] = '' . $dataPost['invoice_number'] . ' (' . $dataPost['payment_method_name'] . ')';
        array_push($journal_insert, $journal_insert0);

        $journal_insert1['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert1['amount_credit'] = 0;
        $journal_insert1['amount_debet'] = (int)$dataPost['fee'];
        $journal_insert1['accounting_type'] = 301;
        $journal_insert1['status'] = 0;
        $journal_insert1['id_payment_method'] = (int)$dataPaymentMethod['id_payment_method'];
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
        $journal_insert_admin0['id_payment_method'] = (int)$dataPaymentMethod['id_payment_method'];
        $journal_insert_admin0['description'] = 'User ' . $dataPost['invoice_number'];
        array_push($journal_insert_admin, $journal_insert_admin0);

        $journal_insert_admin1['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert_admin1['id_user'] = $user->id_user;
        $journal_insert_admin1['id_user_parent'] = $user->id_user_parent;
        $journal_insert_admin1['amount_credit'] = (int)$dataPost['fee'];
        $journal_insert_admin1['amount_debet'] = 0;
        $journal_insert_admin1['accounting_type'] = 3001;
        $journal_insert_admin1['status'] = 0;
        $journal_insert_admin1['id_payment_method'] = (int)$dataPaymentMethod['id_payment_method'];
        $journal_insert_admin1['description'] = 'Fee ' . $dataPost['invoice_number'] . ' (Keuntungan)';
        array_push($journal_insert_admin, $journal_insert_admin1);

        // if (((int)$dataPaymentMethod['id_payment_method'] > 0)) {
        //     $journal_insert_admin2['invoice_number'] = $dataPost['invoice_number'];
        //     $journal_insert_admin2['id_user'] = $user->id_user;
        //     $journal_insert_admin2['id_user_parent'] = $user->id_user_parent;
        //     $journal_insert_admin2['amount_credit'] = 0;
        //     $journal_insert_admin2['amount_debet'] = $fee_pg;
        //     $journal_insert_admin2['accounting_type'] = 3002;
        //     $journal_insert_admin2['status'] = 0;
        //     $journal_insert_admin1['id_payment_method'] = (int)$dataPaymentMethod['id_payment_method'];
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
                ->where('created_at <=', date("Y-m-t", strtotime(date("Y-m-d"))) . ' 23:59:59');
            $result = $builderX->orderBy('id', 'desc')->get()->getResult();
        } else {
            $builderX = $db->table('app_journal_finance_' . $user->id_user)
                ->where('created_at >=', date("Y-m-01", strtotime(date("Y-m-d"))) . ' 00:00:00')
                ->where('created_at <=', date("Y-m-t", strtotime(date("Y-m-d"))) . ' 23:59:59');
            $result = $builderX->orderBy('id', 'desc')->get()->getResult();
        }

        // echo $db->getLastQuery();
        // die();

        $db->close();
        $finalData = json_encode($result);
        echo '{
            "code": 0,
            "error": "",
            "message": "Withdraw sedang diproses dalam 1x24 jam.",
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
        $payment = json_encode(tokopay_generate_qris((int)$dataPost['amount'], $dataPost['payment_method'], $dataPost['invoice_number']));


        $dataBankUser = $db->table('app_users')->where('id_user', $user->id_user)->orWhere('id_user_parent', $user->id_user)->get()->getRow();

        if ((int)$user->id_user_parent > 0) {
            $builderX = $db->table('app_journal_finance_' . $user->id_user_parent)
                ->where('created_at >=', date("Y-m-01", strtotime(date("Y-m-d"))) . ' 00:00:00')
                ->where('created_at <=', date("Y-m-t", strtotime(date("Y-m-d"))) . ' 23:59:59');
            $result = $builderX->orderBy('id', 'desc')->get()->getResult();
        } else {
            $builderX = $db->table('app_journal_finance_' . $user->id_user)
                ->where('created_at >=', date("Y-m-01", strtotime(date("Y-m-d"))) . ' 00:00:00')
                ->where('created_at <=', date("Y-m-t", strtotime(date("Y-m-d"))) . ' 23:59:59');
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
}
