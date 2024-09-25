<?php

namespace App\Controllers\Affiliator\Master;

use Config\Services;
use CodeIgniter\Files\File;

date_default_timezone_set("Asia/Bangkok");

class User extends BaseController
{
    public function index()
    {
        echo ('welcome!');
    }

    public function postList_merchant()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/affiliator/master/user/list_merchant');
        $db = db_connect();

        $builder = $db->table('app_users au')->join('app_user_privilege aup', 'aup.id_user_privilege = au.user_privilege')->where('au.user_role', 2)->where('au.id_user_parent', 0)->where('au.reff_code', $user->reff_code)->get()->getResult();

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
        $user = cekValidation('/affiliator/master/user/lists');
        $db = db_connect();
        $builder = $db->table('app_users au')->join('app_user_privilege aup', 'aup.id_user_privilege = au.user_privilege')->where('au.user_role', 2)->where('au.id_user_parent', 0)->where('au.reff_code', $user->reff_code)->orderBy('au.merchant_name')->get()->getResult();
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
        $user = cekValidation('/affiliator/master/user/list');
        $db = db_connect();
        $builder = $db->table('app_users au')->join('app_user_privilege aup', 'aup.id_user_privilege = au.user_privilege')->where('au.user_role', 1)->get()->getResult();
        $db->close();
        $finalData = json_encode($builder);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }

    public function postPrivilege_list()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/affiliator/master/user/privilege_list');
        $db = db_connect();
        $builder = $db->table('app_user_privilege')->get()->getResult();
        $db->close();
        $finalData = json_encode($builder);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }
}
