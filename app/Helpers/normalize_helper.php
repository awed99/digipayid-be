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
