<?php

namespace App\Controllers\Admin\Master;

class Payment_method extends BaseController
{
    public function index()
    {
        echo ('welcome!');
    }

    public function getList()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/admin/master/product/lists');
        // $dataRequest = cek_token_login($dataPost);
        $db = db_connect();
        $builder = $db->table('master_payment_method')->get()->getResult();
        $db->close();
        $finalData = json_encode($builder);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }

    public function postList()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/admin/master/payment_method/list');
        $db = db_connect();
        $builder = $db->table('master_payment_method')->where('payment_method_id_pg', 1)->where('status_admin', 1)->get()->getResult();
        $db->close();
        $finalData = json_encode($builder);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }

    public function postLists()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/admin/master/payment_method/lists');
        $db = db_connect();
        $builder = $db->table('master_payment_method')->where('payment_method_id_pg', 1)->where('status_admin', 1)->get()->getResult();
        $db->close();
        $finalData = json_encode($builder);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }

    public function postUpdate()
    {
        $request = request();
        $dataPost = $request->getJSON();
        // $dataUpdate['fee_on_merchant'] = $dataPost->fee_on_merchant;
        $dataUpdate['status'] = $dataPost->status;
        $user = cekValidation('/admin/master/payment_method/update');
        $db = db_connect();
        $builder = $db->table('master_payment_method');
        $query = $builder->where('id_payment_method', $dataPost->id_payment_method);
        $query->update($dataUpdate);
        $dataFinal = $query->where('payment_method_id_pg', 1)->where('status_admin', 1)->get()->getResult();
        $db->close();
        $finalData = json_encode($dataFinal);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }
}
