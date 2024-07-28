<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }

    public function postData()
    {
        $user = cekValidation('/dashboard/data');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();

        if ((int)$user->id_user_parent > 0) {
            $statistics = $db
                ->table('app_transactions_' . $user->id_user_parent)
                ->join('app_transaction_products_' . $user->id_user_parent, 'app_transaction_products_' . $user->id_user_parent . '.invoice_number = app_transactions_' . $user->id_user_parent . '.invoice_number', 'right')
                ->select('
                    COUNT(id_transaction) as pembelian,
                    SUM(total_product) as products,
                    (SUM(total_product) * SUM(product_qty)) as items,
                    SUM(amount_to_receive) as pendapatan
                ')
                ->where('time_transaction >=', date('Y-01-01') . ' 00:00:00')
                ->where('time_transaction <=', date('Y-12-t') . ' 23:59:59')
                ->where('(status_transaction = 1 OR status_transaction = 2)')
                ->get()->getRow();

            $topProducts = $db
                ->table('app_transactions_' . $user->id_user_parent)
                ->join('app_transaction_products_' . $user->id_user_parent, 'app_transaction_products_' . $user->id_user_parent . '.invoice_number = app_transactions_' . $user->id_user_parent . '.invoice_number', 'right')
                ->select('
                        ' . ($statistics->products ?? 0) . ' as pembelian,
                        product_name as product,
                        product_code as code,
                        product_image_url as image,
                        COALESCE(SUM(product_qty), 0) as qty
                    ')
                ->where('time_transaction >=', date('Y-01-01') . ' 00:00:00')
                ->where('time_transaction <=', date('Y-12-t') . ' 23:59:59')
                ->where('(status_transaction = 1 OR status_transaction = 2)')
                // ->groupBy('product_name')
                ->groupBy('product_name, product_code, product_image_url')
                ->orderBy('qty', 'DESC')
                ->limit(3)->get()->getResult();

            $trends = $db
                ->table('app_transactions_' . $user->id_user_parent)
                ->join('app_transaction_products_' . $user->id_user_parent, 'app_transaction_products_' . $user->id_user_parent . '.invoice_number = app_transactions_' . $user->id_user_parent . '.invoice_number', 'right')
                ->select('
                    COUNT(id_transaction) as pembelian,
                    SUM(total_product) as products,
                    (SUM(total_product) * SUM(product_qty)) as items,
                    SUM(amount_to_receive) as pendapatan
                ')
                ->where('time_transaction >=', date('Y-m-01') . ' 00:00:00')
                ->where('time_transaction <=', date('Y-m-t') . ' 23:59:59')
                ->where('(status_transaction = 1 OR status_transaction = 2)')
                ->get()->getRow();
            $trends->withdraw = $db->table('app_journal_finance_' . $user->id_user_parent)
                ->selectSum('amount_debet')
                ->where('accounting_type', 3)
                ->where('(status = 1 OR status = 2)')
                ->where('created_at >=', date('Y-m-01') . ' 00:00:00')
                ->where('created_at <=', date('Y-m-t') . ' 23:59:59')->get()->getRow()->amount_debet;
            $trends->deposit = $db->table('app_journal_finance_' . $user->id_user_parent)
                ->selectSum('amount_credit')
                ->where('accounting_type', 2)
                ->where('(status = 1 OR status = 2)')
                ->where('created_at >=', date('Y-m-01') . ' 00:00:00')
                ->where('created_at <=', date('Y-m-t') . ' 23:59:59')->get()->getRow()->amount_credit;

            $grafikMingguan = $db->query("SELECT DAYNAME(time_transaction) AS weekDay, SUM(total_product) AS totalCount FROM app_transactions_" . $user->id_user_parent . " GROUP BY DAYNAME(time_transaction) ORDER BY FIELD(weekDay, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")->getResult();

            $users = $db->table('app_users')->join('app_user_privilege', 'app_user_privilege.id_user_privilege = app_users.user_privilege')->where('id_user', $user->id_user_parent)->orWhere('id_user_parent', $user->id_user_parent)->get()->getResult();
        } else {
            $statistics = $db
                ->table('app_transactions_' . $user->id_user)
                ->join('app_transaction_products_' . $user->id_user, 'app_transaction_products_' . $user->id_user . '.invoice_number = app_transactions_' . $user->id_user . '.invoice_number', 'right')
                ->select('
                    COUNT(id_transaction) as pembelian,
                    SUM(total_product) as products,
                    (SUM(total_product) * SUM(product_qty)) as items,
                    SUM(amount_to_receive) as pendapatan
                ')
                ->where('time_transaction >=', date('Y-01-01') . ' 00:00:00')
                ->where('time_transaction <=', date('Y-12-t') . ' 23:59:59')
                ->where('(status_transaction = 1 OR status_transaction = 2)')
                ->get()->getRow();

            $topProducts = $db
                ->table('app_transactions_' . $user->id_user)
                ->join('app_transaction_products_' . $user->id_user, 'app_transaction_products_' . $user->id_user . '.invoice_number = app_transactions_' . $user->id_user . '.invoice_number', 'right')
                ->select('
                        ' . ($statistics->products ?? 0) . ' as pembelian,
                        product_name as product,
                        product_code as code,
                        product_image_url as image,
                        COALESCE(SUM(product_qty), 0) as qty
                    ')
                ->where('time_transaction >=', date('Y-01-01') . ' 00:00:00')
                ->where('time_transaction <=', date('Y-12-t') . ' 23:59:59')
                ->where('(status_transaction = 1 OR status_transaction = 2)')
                // ->groupBy('product_name')
                ->groupBy('product_name, product_code, product_image_url')
                ->orderBy('qty', 'DESC')
                ->limit(3)->get()->getResult();

            $trends = $db
                ->table('app_transactions_' . $user->id_user)
                ->join('app_transaction_products_' . $user->id_user, 'app_transaction_products_' . $user->id_user . '.invoice_number = app_transactions_' . $user->id_user . '.invoice_number', 'right')
                ->select('
                COUNT(id_transaction) as pembelian,
                SUM(total_product) as products,
                (SUM(total_product) * SUM(product_qty)) as items,
                SUM(amount_to_receive) as pendapatan
            ')
                ->where('time_transaction >=', date('Y-m-01') . ' 00:00:00')
                ->where('time_transaction <=', date('Y-m-t') . ' 23:59:59')
                ->where('(status_transaction = 1 OR status_transaction = 2)')
                ->get()->getRow();
            $trends->withdraw = $db->table('app_journal_finance_' . $user->id_user)
                ->selectSum('amount_debet')
                ->where('accounting_type', 3)
                ->where('(status = 1 OR status = 2)')
                ->where('created_at >=', date('Y-m-01') . ' 00:00:00')
                ->where('created_at <=', date('Y-m-t') . ' 23:59:59')->get()->getRow()->amount_debet;
            $trends->deposit = $db->table('app_journal_finance_' . $user->id_user)
                ->selectSum('amount_credit')
                ->where('accounting_type', 2)
                ->where('(status = 1 OR status = 2)')
                ->where('created_at >=', date('Y-m-01') . ' 00:00:00')
                ->where('created_at <=', date('Y-m-t') . ' 23:59:59')->get()->getRow()->amount_credit;

            $grafikMingguan = $db->query("SELECT DAYNAME(time_transaction) AS weekDay, SUM(total_product) AS totalCount FROM app_transactions_" . $user->id_user . " GROUP BY DAYNAME(time_transaction) ORDER BY FIELD(weekDay, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")->getResult();

            $users = $db->table('app_users')->join('app_user_privilege', 'app_user_privilege.id_user_privilege = app_users.user_privilege')->where('id_user', $user->id_user)->orWhere('id_user_parent', $user->id_user)->get()->getResult();
        }

        $db->close();

        $data['saldo'] = $user->saldo;
        $data['statistics'] = $statistics;
        $data['trends'] = $trends;
        $data['grafik_mingguan'] = $grafikMingguan;
        $data['top_products'] = $topProducts;
        $data['users'] = $users;

        $dataResponse = json_encode($data);

        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $dataResponse . '
        }';
    }
}
