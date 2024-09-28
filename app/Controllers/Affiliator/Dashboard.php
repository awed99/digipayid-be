<?php

namespace App\Controllers\Affiliator;

class Dashboard extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }

    public function postData()
    {
        $user = cekValidation('/affiliator/dashboard/data');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();
        $data['saldo'] = $user->saldo;
        $data['saldo_real'] = $user->real_saldo;


        $users = $db->table('app_users')->where('au.id_user', $user->id_user)->get()->getResult();

        $merchants = $db->table('app_users')->where('user_role', 2)->where('id_user_parent', 0)->where('reff_code', $user->reff_code)->where('is_verified', 1)->get()->getResult();


        $topMerchants = $db
            ->table('admin_journal_finance ajf')
            ->join('app_users au', 'au.id_user =  ajf.id_user', 'left')
            ->select('au.*, SUM(amount_credit) as keuntungan, ' . $user->real_saldo . ' as saldo_real')
            ->where('(ajf.accounting_type = 1001 OR ajf.accounting_type = 2001 OR ajf.accounting_type = 3001)')
            // ->where('(ajf.status = 1 OR ajf.status = 2)')
            ->where('ajf.status', 2)
            ->where('au.user_role', 2)
            ->groupBy('au.id_user')
            ->orderBy('keuntungan', 'DESC')
            // ->where('time_transaction >=', date('Y-m-01') . ' 00:00:00')
            // ->where('time_transaction <=', date('Y-m-t') . ' 23:59:59')
            ->get()->getResult();

        // $topMerchants = [];
        // $topMerchants = $db->table('app_journal_finance_' . $merchant[0]->id_user)->select('
        //                     ' . (0) . ' as merchant_name,
        //                     ' . (0) . ' as merchant_owner,
        //                     product_name as product,
        //                     product_code as code,
        //                     product_image_url as image,
        //                     COALESCE(SUM(product_qty), 0) as qty
        //                 ')
        //     ->where('created_at >=', date('Y-01-01') . ' 00:00:00')
        //     ->where('created_at <=', date('Y-12-t') . ' 23:59:59')
        //     ->where('(status = 1 OR status = 2)');
        // $loop = 0;
        // foreach ($merchants as $merchant) {
        //     if ($loop > 0) {
        //         $union = $db->table('app_journal_finance_' . $merchant->id_user)->select('
        //                             ' . (0) . ' as merchant_name,
        //                             ' . (0) . ' as merchant_owner,
        //                             product_name as product,
        //                             product_code as code,
        //                             product_image_url as image,
        //                             COALESCE(SUM(product_qty), 0) as qty
        //                         ')
        //             ->where('created_at >=', date('Y-01-01') . ' 00:00:00')
        //             ->where('created_at <=', date('Y-12-t') . ' 23:59:59')
        //             ->where('(status = 1 OR status = 2)');

        //         $topMerchants->union($union)->get();
        //     }

        //     $loop++;
        //     // $topMerchants = array_merge($topMerchants, $_topMerchants);
        // }
        // ->groupBy('product_name')
        // ->orderBy('qty', 'DESC')
        // ->limit(3)->get()->getResult();

        $trends = (object)[];
        $trends->keuntungan = $db
            ->table('admin_journal_finance')
            ->selectSum('amount_credit')
            ->where('(accounting_type = 1001 OR accounting_type = 2001 OR accounting_type = 3001)')
            ->where('(status = 1 OR status = 2)')
            // ->where('time_transaction >=', date('Y-m-01') . ' 00:00:00')
            // ->where('time_transaction <=', date('Y-m-t') . ' 23:59:59')
            ->get()->getRow()->amount_credit;
        $trends->withdraw = $db->table('admin_journal_finance')
            ->selectSum('amount_debet')
            ->where('accounting_type', 4)
            ->where('(status = 1 OR status = 2)')
            // ->where('created_at >=', date('Y-m-01') . ' 00:00:00')
            // ->where('created_at <=', date('Y-m-t') . ' 23:59:59')
            ->get()->getRow()->amount_debet;
        $trends->deposit_merchant = $db->table('admin_journal_finance')
            ->selectSum('amount_credit')
            ->where('accounting_type', 2)
            ->where('(status = 1 OR status = 2)')
            // ->where('created_at >=', date('Y-m-01') . ' 00:00:00')
            // ->where('created_at <=', date('Y-m-t') . ' 23:59:59')
            ->get()->getRow()->amount_credit;
        $trends->withdraw_merchant = $db->table('admin_journal_finance')
            ->selectSum('amount_debet')
            ->where('accounting_type', 3)
            ->where('(status = 1 OR status = 2)')
            // ->where('created_at >=', date('Y-m-01') . ' 00:00:00')
            // ->where('created_at <=', date('Y-m-t') . ' 23:59:59')
            ->get()->getRow()->amount_debet;

        $grafikMingguan = $db->query("SELECT DAYNAME(created_at) AS weekDay, SUM(amount_credit) AS totalCount FROM admin_journal_finance  WHERE (accounting_type = 1001 OR accounting_type = 2001 OR accounting_type = 3001) AND (status = 1 OR status = 2) GROUP BY DAYNAME(created_at) ORDER BY FIELD(weekDay, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")->getResult();
        $grafikWithdraw = $db->query("SELECT DAYNAME(created_at) AS weekDay, SUM(amount_debet) AS totalCount FROM admin_journal_finance  WHERE (accounting_type = 3) GROUP BY DAYNAME(created_at) ORDER BY FIELD(weekDay, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")->getResult();



        $db->close();

        // $data['statistics'] = $statistics;
        $data['trends'] = $trends;
        $data['grafik_mingguan'] = $grafikMingguan;
        $data['grafik_withdraw'] = $grafikWithdraw;
        $data['top_merchants'] = $topMerchants;
        $data['users'] = $users;
        $data['user'] = $user;
        $data['merchants'] = $merchants;

        $dataResponse = json_encode($data);

        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $dataResponse . '
        }';
    }
}
