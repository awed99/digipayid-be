<?php

namespace App\Controllers\Transactions;
use Config\Services;
use CodeIgniter\Files\File;

date_default_timezone_set("Asia/Bangkok");

class Orders extends BaseController
{
    public function index()
    {
        echo('welcome!');
    }

    public function postList()
    {   
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/transactions/order/list');
        $db = db_connect();
        $builder = $db->table('app_transactions_'.$user->id_user)->get()->getResult();
        $db->close();
        $finalData = json_encode($builder);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": '.$finalData.'
        }';
    }

    public function postCreate()
    {   
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/transactions/order/create');
        $db = db_connect();
        $builder = $db->table('app_transactions_'.$user->id_user);
        $query = $builder->insert($dataPost);
        $dataFinal = $builder->get()->getResult();
        $db->close();
        $finalData = json_encode($dataFinal);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": '.$finalData.'
        }';
    }

    public function postUpdate()
    {   
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/transactions/order/update');
        $db = db_connect();
        $builder = $db->table('app_transactions_'.$user->id_user);
        $query = $builder->where('id_transaction', $dataPost->id_transaction);
        $query->update($dataPost);
        $dataFinal = $query->get()->getResult();
        $db->close();
        $finalData = json_encode($dataFinal);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": '.$finalData.'
        }';
    }

}
