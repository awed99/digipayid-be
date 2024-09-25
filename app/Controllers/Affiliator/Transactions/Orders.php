<?php


namespace App\Controllers\affiliator\Transactions;

use Config\Services;
use CodeIgniter\Files\File;

date_default_timezone_set("Asia/Bangkok");

class Orders extends BaseController
{
    public function index()
    {
        echo ('welcome!');
    }

    public function postList()
    {
        $user = cekValidation('/affiliator/transactions/orders/list');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();

        if ((int)$dataPost->id_merchant > 0) {
            $builder = $db->table('app_transactions_' . $dataPost->id_merchant);
            if (isset($dataPost->start_date)) {
                $builder->where('time_transaction >=', $dataPost->start_date . ' 00:00:00');
            }
            if (isset($dataPost->end_date)) {
                $builder->where('time_transaction <=', $dataPost->end_date . ' 23:59:59');
            }
            $result = $builder->where('status_transaction', 2)->orWhere('status_transaction', 1)->orderBy('id_transaction ', 'desc')->get()->getResult();

            $db->close();
            $finalData = json_encode($result);
        } else {
            $finalData = '[]';
        }

        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }
}
