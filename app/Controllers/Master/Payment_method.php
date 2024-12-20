<?php

namespace App\Controllers\Master;

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
        $user = cekValidation('/master/product/lists');
        // $dataRequest = cek_token_login($dataPost);
        $db = db_connect();
        $builder = $db->table('app_payment_method_' . $user->id_user)->join('master_payment_method', 'master_payment_method.id_payment_method = app_payment_method_' . $user->id_user . '.id_payment_method')->where('status_admin', 1)->get()->getResult();
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
        $user = cekValidation('/master/payment_method/list');
        $db = db_connect();
        $builder = $db->table('app_payment_method_' . $user->id_user)->join('master_payment_method', 'master_payment_method.id_payment_method = app_payment_method_' . $user->id_user . '.id_payment_method')->where('payment_method_id_pg', 1)->where('status', 1)->where('is_active', 1)->where('status_admin', 1)->get()->getResult();
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
        $user = cekValidation('/master/payment_method/lists');
        $db = db_connect();
        $builder = $db->table('app_payment_method_' . $user->id_user)->join('master_payment_method', 'master_payment_method.id_payment_method = app_payment_method_' . $user->id_user . '.id_payment_method')->where('payment_method_id_pg', 1)->where('status', 1)->where('status_admin', 1)->get()->getResult();
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
        $dataUpdate['fee_on_merchant'] = $dataPost->fee_on_merchant;
        $dataUpdate['is_active'] = $dataPost->is_active;
        $user = cekValidation('/master/payment_method/update');
        $db = db_connect();
        $builder = $db->table('app_payment_method_' . $user->id_user);
        $query = $builder->where('id_payment_method', $dataPost->id_payment_method);
        $query->update($dataUpdate);
        $dataFinal = $query->join('master_payment_method', 'master_payment_method.id_payment_method = app_payment_method_' . $user->id_user . '.id_payment_method')->where('payment_method_id_pg', 1)->where('status_admin', 1)->get()->getResult();
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
