<?php

function normalize()
{
    $db = db_connect();

    $status['status'] = 9;
    $status['updated_at'] = date('Y-m-d H:i:s');
    $db->table("admin_journal_finance")->where('status', 0)->where("(NOW() - INTERVAL 10 MINUTE) >= created_at")->update($status);

    $statusTRX['status_transaction'] = 9;
    $statusTRX['status_payment'] = 9;
    $statusTRX['time_transaction_failed'] = date('Y-m-d H:i:s');
    $users = $db->table('app_users')->where('id_user_parent', 0)->where('user_role', 2)->where('is_verified', 1)->where('is_active', 1)->get()->getResult();
    foreach ($users as $user) {
        $db->table("app_journal_finance_" . $user->id_user)->where('status', 0)->where("(NOW() - INTERVAL 10 MINUTE) >= created_at")->update($status);
        $db->table("app_transactions_" . $user->id_user)->where('status_transaction', 0)->where("(NOW() - INTERVAL 10 MINUTE) >= time_transaction")->update($statusTRX);
        $is_affiliator = $db->table('app_users')
            ->where('reff_code', $user->reff_code)->where('is_active', 1)->where('is_verified', 1)
            ->where('user_role', 3)->where('user_privilege', 8)
            ->get()->getRow();
        if (isset($is_affiliator->id_user)) {
            $id_affiliator = $is_affiliator->id_user;
            $tbl_affiliator = "app_journal_finance_" . $id_affiliator;
            $db->table($tbl_affiliator)->where('status', 0)->where("(NOW() - INTERVAL 10 MINUTE) >= created_at")->update($status);
        }
    }


    // $users = $db->table('app_users')->where('id_user_parent', 1)->where('user_role', 2)->where('is_verified', 1)->where('is_active', 1)->get()->getResult();
    foreach ($users as $user) {
        $trxs = $db->table("app_transactions_" . $user->id_user . " atx")
            ->join("app_payment_method_" . $user->id_user . " apmx", "apmx.id_payment_method = atx.id_payment_method", 'left')
            ->join("master_payment_method mpmx", "apmx.id_payment_method = mpmx.id_payment_method", 'left')
            ->where('atx.status_transaction', 1)->get()->getResult();

        foreach ($trxs as $trx) {
            if ((int)$trx->settlement_day === 0) {
                // print_r($trx);
                // die();
                $status['status'] = 2;
                $status['updated_at'] = date('Y-m-d H:i:s');
                $statusTRX['status_transaction'] = 2;
                $statusTRX['status_payment'] = 2;
                $statusTRX['time_transaction_failed'] = date('Y-m-d H:i:s');

                $db->table("admin_journal_finance")->where('invoice_number', $trx->invoice_number)->where('status', 1)->where('id_payment_method', 0)->update($status);
                $db->table("app_journal_finance_" . $user->id_user)->where('invoice_number', $trx->invoice_number)->where('status', 1)->where('id_payment_method', 0)->update($status);
                $db->table("app_transactions_" . $user->id_user)->where('invoice_number', $trx->invoice_number)->where('status_transaction', 1)->where('id_payment_method', 0)->update($statusTRX);

                $db->table("admin_journal_finance")->where('invoice_number', $trx->invoice_number)->where('status', 1)->where("(NOW() - INTERVAL 20 MINUTE) >= created_at")->update($status);
                $db->table("app_journal_finance_" . $user->id_user)->where('invoice_number', $trx->invoice_number)->where('status', 1)->where("(NOW() - INTERVAL 20 MINUTE) >= created_at")->update($status);
                $db->table("app_transactions_" . $user->id_user)->where('invoice_number', $trx->invoice_number)->where('status_transaction', 1)->where("(NOW() - INTERVAL 20 MINUTE) >= time_transaction")->update($statusTRX);
                
                $is_affiliator = $db->table('app_users')
                    ->where('reff_code', $user->reff_code)->where('is_active', 1)->where('is_verified', 1)
                    ->where('user_role', 3)->where('user_privilege', 8)
                    ->get()->getRow();
                if (isset($is_affiliator->id_user)) {
                    $id_affiliator = $is_affiliator->id_user;
                    $tbl_affiliator = "app_journal_finance_" . $id_affiliator;
                    $db->table($tbl_affiliator)->where('invoice_number', $trx->invoice_number)->where('status', 1)->where("(NOW() - INTERVAL 20 MINUTE) >= created_at")->update($status);
                }
                
            } elseif ((int)$trx->settlement_day > 0) {
                $status['status'] = 2;
                $status['updated_at'] = date('Y-m-d H:i:s');
                $statusTRX['status_transaction'] = 2;
                $statusTRX['status_payment'] = 2;
                $statusTRX['time_transaction_failed'] = date('Y-m-d H:i:s');
                
                $db->table("admin_journal_finance")->where('invoice_number', $trx->invoice_number)->where('status', 1)->where("(NOW() - INTERVAL " . $trx->settlement_day . " DAY) >= created_at")->update($status);
                $db->table("app_journal_finance_" . $user->id_user)->where('invoice_number', $trx->invoice_number)->where('status', 1)->where("(NOW() - INTERVAL " . $trx->settlement_day . " DAY) >= created_at")->update($status);
                $db->table("app_transactions_" . $user->id_user)->where('invoice_number', $trx->invoice_number)->where('status_transaction', 1)->where("(NOW() - INTERVAL " . $trx->settlement_day . " DAY) >= time_transaction")->update($statusTRX);
                
                $is_affiliator = $db->table('app_users')
                    ->where('reff_code', $user->reff_code)->where('is_active', 1)->where('is_verified', 1)
                    ->where('user_role', 3)->where('user_privilege', 8)
                    ->get()->getRow();
                if (isset($is_affiliator->id_user)) {
                    $id_affiliator = $is_affiliator->id_user;
                    $tbl_affiliator = "app_journal_finance_" . $id_affiliator;
                    $db->table($tbl_affiliator)->where('invoice_number', $trx->invoice_number)->where('status', 1)->where("(NOW() - INTERVAL " . $trx->settlement_day . " DAY) >= created_at")->update($status);
                }
            }
        }
    }

    $db->close();
}

function normalize_notifications()
{
    $db = db_connect();

    $notifs = $db->table('app_notifications')->where('status', 0)->get()->getResult();
    foreach ($notifs as $notif) {
        if ((int)$notif->type == 1) {
            sendMail($notif->destination, $notif->subject, $notif->text_message, $notif->attachment_url ?? false);
        } elseif ((int)$notif->type == 2) {
            sendWhatsapp($notif->destination, $notif->text_message, $notif->attachment_url ?? false);
        } else {
            sendWhatsapp($notif->destination, $notif->text_message, $notif->attachment_url ?? false);
        }
        $db->table("app_notifications")->where('id', $notif->id)->update(['status' => 1]);
    }

    $db->close();
}

function normalize2()
{
    $db = db_connect();

    $status['status'] = 9;
    $status['updated_at'] = date('Y-m-d H:i:s');
    $db->table("admin_journal_finance")->where("(NOW() - INTERVAL 10 MINUTE) >= created_at")->update($status);

    $statusTRX['status_transaction'] = 9;
    $statusTRX['status_payment'] = 9;
    $statusTRX['time_transaction_failed'] = date('Y-m-d H:i:s');
    $users = $db->table('app_users')->where('id_user_parent', 0)->where('user_role', 2)->where('is_verified', 1)->where('is_active', 1)->get()->getResult();
    foreach ($users as $user) {
        $db->table("app_journal_finance_" . $user->id_user)->where("(NOW() - INTERVAL 10 MINUTE) >= created_at")->update($status);
        $db->table("app_transactions_" . $user->id_user)->where("(NOW() - INTERVAL 10 MINUTE) >= time_transaction")->update($statusTRX);
    }

    $db->close();
}

function normalize3()
{
    $db = db_connect();

    $status['status'] = 9;
    $status['updated_at'] = date('Y-m-d H:i:s');
    $db->table("admin_journal_finance")->where("(NOW() - INTERVAL 10 MINUTE) >= created_at")->update($status);

    $statusTRX['status_transaction'] = 9;
    $statusTRX['status_payment'] = 9;
    $statusTRX['time_transaction_failed'] = date('Y-m-d H:i:s');
    $users = $db->table('app_users')->where('id_user_parent', 0)->where('user_role', 2)->where('is_verified', 1)->where('is_active', 1)->get()->getResult();
    foreach ($users as $user) {
        $db->table("app_journal_finance_" . $user->id_user)->where("(NOW() - INTERVAL 10 MINUTE) >= created_at")->update($status);
        $db->table("app_transactions_" . $user->id_user)->where("(NOW() - INTERVAL 10 MINUTE) >= time_transaction")->update($statusTRX);
    }

    $db->close();
}

function withdraw() {
    $db = db_connect();
        $data = [];
        $users = $db->table('app_users')->where('id_user_parent', 0)->where('user_role', 2)->where('is_verified', 1)->where('is_active', 1)->get()->getResult();
        foreach ($users as $userX) {
            $trx = $db->table("app_journal_finance_" . $userX->id_user)->where('(accounting_type = 3 or accounting_type = 9001)')->where('status', 0)->orderBy('id', 'asc')->get()->getRow();
            if ($trx) {
                $diUser = explode('-', $trx->invoice_number)[4] ?? '40';
                $where['id_user'] = $diUser;
                $user = $db->table('app_users')->where($where)->where('is_verified', 1)->where('is_active', 1)->get()->getRow();
                // foreach ($trx as $wd) {
                    // $wd->id_user = $userX->id_user;
                    // $wd->bank_short_name = $userX->bank_short_name;
                    // $wd->bank_name = $userX->bank_name;
                    // $wd->bank_account = $userX->bank_account;
                    // $wd->bank_account_name = $userX->bank_account_name;
                    // $wd->merchant_wa = $userX->merchant_wa;
                    // $wd->email  = $userX->email;
                    // $wd->merchant_name  = $userX->merchant_name;
                    // array_push($data, $wd);
                    
                    $wdMethod = $db->table('master_bank_payouts')->where('id', $trx->id_payment_method)->get()->getRow();
                    
                    $headers = [
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: Basic ' . base64_encode((strtolower(getenv('XENDIT_ENV')) === 'production') ? getenv('XENDIT_API_KEY') . ':' : getenv('XENDIT_SD_API_KEY') . ':'),
                        'idempotency-key: ' . $trx->invoice_number . '-' . date('YmdHis')
                    ];
                    $dt = json_encode([
                        'reference_id' => $trx->invoice_number,
                        'channel_code' => $wdMethod->channel_code,
                        'channel_properties' => [
                            'account_holder_name' => $user->bank_account_name,
                            'account_number' => $user->bank_account
                        ],
                        'amount' => (int)$trx->amount_debet,
                        'currency' => 'IDR'
                    ]);
                    $xendit = curl(getenv('XENDIT_API_DOMAIN') . 'v2/payouts', true, $dt, $headers);
                    // return response()->setStatusCode(200)->setJSON($xendit);
                    $xenditRes = json_decode($xendit);
                    // print_r($xenditRes);
                    $db->table("app_journal_finance_" . $userX->id_user)->where('(accounting_type = 3 or accounting_type = 301)')->where('status', 0)->update(['status' => 1]);
                    $db->table("admin_journal_finance")->where('(accounting_type = 3 or accounting_type = 3001)')->where('status', 0)->update(['status' => 1]);
                // }
            }
            // print_r($wd);
        }
        
    $db->close();
}
