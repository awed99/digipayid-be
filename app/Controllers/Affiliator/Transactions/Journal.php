<?php

namespace App\Controllers\Affiliator\Transactions;

use Config\Services;
use CodeIgniter\Files\File;

date_default_timezone_set("Asia/Bangkok");

class Journal extends BaseController
{
    public function index()
    {
        echo ('welcome!');
    }

    public function postList()
    {
        $user = cekValidation('/affiliator/transactions/journal/list');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();

        $builder = $db->table('app_journal_finance_' . $user->id_user);
        if (isset($dataPost->start_date)) {
            $builder->where('created_at >=', $dataPost->start_date . ' 00:00:00');
        }
        if (isset($dataPost->end_date)) {
            $builder->where('created_at <=', $dataPost->end_date . ' 23:59:59');
        }
        $result = $builder->where('status', 2)->orderBy('id', 'desc')->get()->getResult();

        $db->close();
        $finalData = json_encode($result);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }
}
