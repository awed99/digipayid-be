<?php

namespace App\Controllers\Admin\Transactions;

use Config\Services;
use CodeIgniter\Files\File;

date_default_timezone_set("Asia/Bangkok");

class Journal extends BaseController
{
    public function index()
    {
        echo ('welcome!');
    }

    public function postGet_admin_saldo()
    {
        $user = cekValidation('/transactions/journal/get_admin_saldo');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();

        $saldo = $db->query("SELECT (SELECT SUM(amount_credit) FROM `admin_journal_finance` where status = 1) - (SELECT SUM(amount_debet) FROM `admin_journal_finance` where status = 1) as saldo")->getRow()->saldo;

        $db->close();
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $saldo . '
        }';
    }

    public function postList_admin()
    {
        $user = cekValidation('/admin/transactions/journal/list_admin');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();

        $builder = $db->table('admin_journal_finance');
        if (isset($dataPost->start_date)) {
            $builder->where('created_at >=', $dataPost->start_date . ' 00:00:00');
        }
        if (isset($dataPost->end_date)) {
            $builder->where('created_at <=', $dataPost->end_date . ' 23:59:59');
        }
        $result = $builder->orderBy('id', 'desc')->get()->getResult();

        // $saldo = $db->query("SELECT (SELECT SUM(amount_credit) FROM `admin_journal_finance` where status = 1) - (SELECT SUM(amount_debet) FROM `admin_journal_finance` where status = 1) as saldo")->getRow()->saldo;

        $db->close();
        $finalData = json_encode($result);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . ',
            "saldo": ' . $user->saldo . '
        }';
    }

    public function postWithdraw_admin()
    {
        $user = cekValidation('/admin/transactions/journal/withdraw_admin');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();

        $builder = $db->table('admin_journal_finance');
        if (isset($dataPost->start_date)) {
            $builder
                ->groupStart()
                ->where('created_at >=', $dataPost->start_date . ' 00:00:00')
                ->groupEnd();
        }
        if (isset($dataPost->end_date)) {
            $builder
                ->groupStart()
                ->where('created_at <=', $dataPost->end_date . ' 23:59:59')
                ->groupEnd();
        }
        $builder
            ->groupStart()
            ->where('accounting_type', 1001)
            ->orWhere('accounting_type', 2001)
            ->orWhere('accounting_type', 3001)
            ->orWhere('accounting_type', 4)
            ->orWhere('accounting_type', 4002)
            ->groupEnd();
        $result = $builder->orderBy('id', 'desc')->get()->getResult();

        $db->close();
        $finalData = json_encode($result);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . ',
            "saldo": ' . $user->real_saldo . '
        }';
    }

    public function postCreate_withdraw_admin()
    {
        $user = cekValidation('/admin/transactions/journal/create_withdraw_admin');
        $request = request();
        $dataPost = $request->getJSON(true);
        $db = db_connect();

        $inv_no = isset($dataPost['invoice_number']) ? $dataPost['invoice_number'] : 'WITHDRAW-' . $user->id_user . '-' . strtoupper(substr(md5(Date('YmdHis')), 5, 8));

        $insertJournal = [];

        $insertJournal0['status'] = 2;
        $insertJournal0['accounting_type'] = 4;
        $insertJournal0['id_payment_method'] = 0;
        $insertJournal0['id_user'] = $user->id_user;
        $insertJournal0['id_user_parent'] = $user->id_user_parent;
        $insertJournal0['amount_debet'] = (int)$dataPost['amount'] - (int)$dataPost['fee'];
        $insertJournal0['invoice_number'] = $inv_no;
        $insertJournal0['description'] = $inv_no;
        array_push($insertJournal, $insertJournal0);

        $insertJournal1['status'] = 2;
        $insertJournal1['accounting_type'] = 4002;
        $insertJournal1['id_payment_method'] = 0;
        $insertJournal1['id_user'] = $user->id_user;
        $insertJournal1['id_user_parent'] = $user->id_user_parent;
        $insertJournal1['amount_debet'] = (int)$dataPost['fee'];
        $insertJournal1['invoice_number'] = $inv_no;
        $insertJournal1['description'] = 'Fee PG ' . $inv_no;
        array_push($insertJournal, $insertJournal1);

        $db->table('admin_journal_finance')->insertBatch($insertJournal);

        $builder = $db->table('admin_journal_finance');
        if (isset($dataPost['start_date'])) {
            $builder
                ->groupStart()
                ->where('created_at >=', $dataPost['start_date'] . ' 00:00:00')
                ->groupEnd();
        }
        if (isset($dataPost['end_date'])) {
            $builder
                ->groupStart()
                ->where('created_at <=', $dataPost['end_date'] . ' 23:59:59')
                ->groupEnd();
        }
        $builder
            ->groupStart()
            ->where('accounting_type', 1001)
            ->orWhere('accounting_type', 2001)
            ->orWhere('accounting_type', 3001)
            ->orWhere('accounting_type', 4)
            ->orWhere('accounting_type', 4002)
            ->groupEnd();
        $result = $builder->orderBy('id', 'desc')->get()->getResult();

        $db->close();
        $finalData = json_encode($result);
        echo '{
            "code": 0,
            "error": "",
            "message": "Penarikan dana berhasil tercatat.",
            "data": ' . $finalData . ',
            "saldo": ' . $user->real_saldo - (int)$dataPost['amount'] . '
        }';
    }

    public function postList()
    {
        $user = cekValidation('/admin/transactions/journal/list');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();

        $builder = $db->table('app_journal_finance_' . $dataPost->id_merchant);
        if (isset($dataPost->start_date)) {
            $builder->where('created_at >=', $dataPost->start_date . ' 00:00:00');
        }
        if (isset($dataPost->end_date)) {
            $builder->where('created_at <=', $dataPost->end_date . ' 23:59:59');
        }
        $result = $builder->orderBy('id', 'desc')->get()->getResult();

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
        $user = cekValidation('/admin/transactions/journal/list_settlement');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();

        $data = [];
        $users = $db->table('app_users')->where('id_user_parent', 0)->where('user_role', 2)->where('is_verified', 1)->where('is_active', 1)->get()->getResult();
        foreach ($users as $userX) {
            $builder = $db->table("app_journal_finance_" . $userX->id_user)
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
            $trx = $builder->orderBy('id', 'desc')->get()->getResult();
            if ($trx) {
                foreach ($trx as $wd) {
                    $wd->id_user = $userX->id_user;
                    $wd->bank_short_name = $userX->bank_short_name;
                    $wd->bank_name = $userX->bank_name;
                    $wd->bank_account = $userX->bank_account;
                    $wd->bank_account_name = $userX->bank_account_name;
                    $wd->merchant_wa = $userX->merchant_wa;
                    $wd->email  = $userX->email;
                    $wd->merchant_name  = $userX->merchant_name;
                    array_push($data, $wd);
                }
            }
            // print_r($wd);
        }

        $db->close();
        $finalData = json_encode($data);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }

    public function postList_ewallet()
    {
        $user = cekValidation('/admin/transactions/journal/list_ewallet');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();

        $builder = $db->table('app_journal_finance_' . $dataPost->id_merchant)->where('(id_payment_method > 0 AND accounting_type > 1)');
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
        $saldo = $db->query("SELECT (SELECT SUM(amount_credit) FROM `app_journal_finance_" . $dataPost->id_merchant . "` where status > 1) - (SELECT SUM(amount_debet) FROM `app_journal_finance_" . $dataPost->id_merchant . "` where status > 1) as saldo")->getRow()->saldo ?? 0;

        $db->close();
        $finalData = json_encode($result);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . ',
            "saldo": ' . ($saldo) . '
        }';
    }

    public function postList_topup_users()
    {
        $user = cekValidation('/admin/transactions/journal/list_topup_users');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();

        $builder = $db->table('app_journal_finance_' . $dataPost->id_merchant)
            ->groupStart()
            ->where('accounting_type', 2)
            ->groupEnd();

        if (isset($dataPost->start_date)) {
            $builder->where('created_at >=', $dataPost->start_date . ' 00:00:00');
        }
        if (isset($dataPost->end_date)) {
            $builder->where('created_at <=', $dataPost->end_date . ' 23:59:59');
        }

        $result = $builder->orderBy('id', 'desc')->get()->getResult();

        $db->close();
        $finalData = json_encode($result);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }

    public function postList_withdraw_users()
    {
        $user = cekValidation('/admin/transactions/journal/list_withdraw_users');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();

        $builder = $db->table('app_journal_finance_' . $dataPost->id_merchant)
            ->groupStart()
            ->where('accounting_type', 3)
            ->groupEnd();

        if (isset($dataPost->start_date)) {
            $builder->where('created_at >=', $dataPost->start_date . ' 00:00:00');
        }
        if (isset($dataPost->end_date)) {
            $builder->where('created_at <=', $dataPost->end_date . ' 23:59:59');
        }

        $result = $builder->orderBy('id', 'desc')->get()->getResult();

        $db->close();
        $finalData = json_encode($result);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }

    public function postCheck_deposit_ewallet()
    {
        $user = cekValidation('/admin/transactions/journal/check_deposit_ewallet');
        $request = request();
        $dataPost = $request->getJSON(true);
        $db = db_connect();

        $dataPost['invoice_number'] = isset($dataPost['invoice_number']) ? $dataPost['invoice_number'] : 'DEPOSIT-' . $dataPost['id_merchant'] . '-' . strtoupper(substr(md5(Date('YmdHis')), 5, 8));
        $payment = json_encode(tokopay_generate_qris((int)$dataPost['amount'], $dataPost['payment_method'], $dataPost['invoice_number']));


        $dataBankUser = $db->table('app_users')->where('id_user', $dataPost['id_merchant'])->orWhere('id_user_parent', $dataPost['id_merchant'])->get()->getRow();

        $builder = $db->table('app_journal_finance_' . $dataPost['id_merchant'])
            ->where('(id_payment_method > 0 AND accounting_type > 1)')
            ->where('created_at >=', date("Y-m-01", strtotime(date("Y-m-d"))) . ' 00:00:00')
            ->where('created_at <=', date("Y-m-t", strtotime(date("Y-m-d"))) . ' 23:59:59')
            ->orderBy('id', 'desc')->get()->getResult();

        $db->close();
        $finalData = json_encode($builder);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . ',
            "payment": ' . $payment . ',
            "data_bank": ' . json_encode($dataBankUser) . '
        }';
    }

    public function postList_withdraw_request()
    {
        $user = cekValidation('/admin/transactions/journal/list_withdraw_request');
        $request = request();
        $dataPost = $request->getJSON(true);
        $db = db_connect();

        $data = [];
        $users = $db->table('app_users')->where('id_user_parent', 0)->where('user_role', 2)->where('is_verified', 1)->where('is_active', 1)->get()->getResult();
        foreach ($users as $userX) {
            $trx = $db->table("app_journal_finance_" . $userX->id_user)->where('accounting_type', 3)->orderBy('id', 'desc')->get()->getResult();
            if ($trx) {
                foreach ($trx as $wd) {
                    $wd->id_user = $userX->id_user;
                    $wd->bank_short_name = $userX->bank_short_name;
                    $wd->bank_name = $userX->bank_name;
                    $wd->bank_account = $userX->bank_account;
                    $wd->bank_account_name = $userX->bank_account_name;
                    $wd->merchant_wa = $userX->merchant_wa;
                    $wd->email  = $userX->email;
                    $wd->merchant_name  = $userX->merchant_name;
                    array_push($data, $wd);
                }
            }
            // print_r($wd);
        }
        // print_r($data);
        // die();

        $db->close();
        $finalData = json_encode($data);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . ',
            "saldo": ' . $user->saldo . '
        }';
    }

    public function postUpdate_withdraw_request()
    {
        $user = cekValidation('/admin/transactions/journal/update_withdraw_request');
        $request = request();
        $dataPost = $request->getJSON(true);
        $db = db_connect();

        $status['status'] = $dataPost['status'];
        $status['updated_at'] = date('Y-m-d H:i:s');
        $db->table("admin_journal_finance")->where('(status = 0 OR status = 1)')->where('invoice_number', $dataPost['invoice_number'])->update($status);
        $db->table("app_journal_finance_" . $dataPost['id_user'])->where('(status = 0 OR status = 1)')->where('invoice_number', $dataPost['invoice_number'])->update($status);

        $data = [];
        $users = $db->table('app_users')->where('id_user_parent', 0)->where('user_role', 2)->where('is_verified', 1)->where('is_active', 1)->get()->getResult();
        foreach ($users as $userX) {
            $trx = $db->table("app_journal_finance_" . $userX->id_user)->where('accounting_type', 3)->orderBy('id', 'desc')->get()->getResult();
            if ($trx) {
                foreach ($trx as $wd) {
                    $wd->id_user = $userX->id_user;
                    $wd->bank_short_name = $userX->bank_short_name;
                    $wd->bank_name = $userX->bank_name;
                    $wd->bank_account = $userX->bank_account;
                    $wd->bank_account_name = $userX->bank_account_name;
                    $wd->merchant_wa = $userX->merchant_wa;
                    $wd->email  = $userX->email;
                    $wd->merchant_name  = $userX->merchant_name;
                    array_push($data, $wd);
                }
            }
            // print_r($wd);
        }
        // print_r($data);
        // die();

        if ((int)$dataPost['status'] === 2) {
            $message = '*INFO DIGIPAYID*

Penarikan Dana anda sudah berhasil. silakan cek.
No Invoice : *' . $dataPost['invoice_number'] . '*
Nominal : *IDR ' . format_rupiah($dataPost['amount']) . '*
Status : *Berhasil*';
            sendWhatsapp($dataPost['merchant_wa'], $message);
        }

        $db->close();
        $finalData = json_encode($data);
        $saldo = ((int)$dataPost['status'] === 2) ? $user->saldo - (int)$dataPost['amount'] + (int)$dataPost['fee'] : $user->saldo;
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . ',
            "saldo": ' . $saldo . '
        }';
    }
}
