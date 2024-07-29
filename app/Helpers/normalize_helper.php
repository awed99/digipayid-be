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
            } elseif ((int)$trx->settlement_day > 0) {
                $status['status'] = 2;
                $status['updated_at'] = date('Y-m-d H:i:s');
                $statusTRX['status_transaction'] = 2;
                $statusTRX['status_payment'] = 2;
                $statusTRX['time_transaction_failed'] = date('Y-m-d H:i:s');
                $db->table("admin_journal_finance")->where('invoice_number', $trx->invoice_number)->where('status', 1)->where("(NOW() - INTERVAL " . $trx->settlement_day . " DAY) >= created_at")->update($status);
                $db->table("app_journal_finance_" . $user->id_user)->where('invoice_number', $trx->invoice_number)->where('status', 1)->where("(NOW() - INTERVAL " . $trx->settlement_day . " DAY) >= created_at")->update($status);
                $db->table("app_transactions_" . $user->id_user)->where('invoice_number', $trx->invoice_number)->where('status_transaction', 1)->where("(NOW() - INTERVAL " . $trx->settlement_day . " DAY) >= time_transaction")->update($statusTRX);
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
