<?php

namespace App\Controllers\Master;

class Withdraw_method extends BaseController
{
    public function index()
    {
        echo ('welcome!');
    }

    public function postList()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/master/withdraw_method/list');
        // $dataRequest = cek_token_login($dataPost);
        $db = db_connect();
        if ((int)$user->id_user > 1) {
            $builder = $db->table('app_payment_method_' . $user->id_user)
                ->join('master_payment_method', 'master_payment_method.id_payment_method = app_payment_method_' . $user->id_user . '.id_payment_method')
                ->where('payment_method_id_pg', 3)
                ->where('status', 1)
                ->where('status_admin', 1)
                ->get()->getResult();
        } else {
            $builder = null;
        }
        $builder2 = $db->table('master_bank_payouts')->get()->getResult();
        $db->close();

        // return  response()->setStatusCode(200)->setBody(
        //     getenv('XENDIT_API_DOMAIN') . 'available_disbursements_banks'
        // );

        // $headers = [
        //     'Accept: application/json',
        //     'Content-Type: application/json',
        //     'Authorization: Basic ' . base64_encode((strtolower(getenv('XENDIT_ENV')) === 'production') ? getenv('XENDIT_API_KEY') . ':' : getenv('XENDIT_SD_API_KEY') . ':'),
        // ];
        // $xendit = curl(getenv('XENDIT_API_DOMAIN') . 'available_disbursements_banks', false, false, $headers);
        // // return response()->setStatusCode(200)->setJSON($xendit);
        // $xenditRes = json_decode($xendit);
        // $wdMethod = array_filter($xenditRes,  function ($item) {
        //     if ($item->can_disburse == true) {
        //         $item->payment_method_code = $item->code;
        //         $item->payment_method_name = $item->name;
        //         $item->bank_short_name = $item->code;
        //         $item->bank_name = $item->name;
        //         return $item;
        //     }
        // });

        //  return response()->setStatusCode(200)->setJSON($wdMethod);

        $wdMethod = array_filter($builder2,  function ($item) {
            $item->payment_method_code = $item->channel_code;
            $item->payment_method_name = $item->channel_name;
            $item->bank_short_name = $item->channel_code;
            $item->bank_name = $item->channel_name;
            return $item;
        });

        // foreach ($builder2 as $key => $value) {
        //     $builder[$key]->payment_method_code = $value->channel_code;
        //     $builder[$key]->payment_method_name = $value->channel_name;
        //     $builder[$key]->bank_short_name = $value->channel_name;
        //     $builder[$key]->bank_name = $value->channel_name;
        // }

        $payment = array(
            "payment_methods" => $builder,
            "withdraw_methods" => $wdMethod,
        );
        $finalData = json_encode($payment);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }
}
