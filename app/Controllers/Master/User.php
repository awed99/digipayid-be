<?php

namespace App\Controllers\Master;

use Config\Services;
use CodeIgniter\Files\File;

date_default_timezone_set("Asia/Bangkok");

class User extends BaseController
{
    public function index()
    {
        echo ('welcome!');
    }

    public function postSetting()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/master/user/setting');
        $db = db_connect();
        $builder = $db->table('app_users au')->join('app_user_privilege aup', 'aup.id_user_privilege = au.user_privilege')->where('au.id_user', $user->id_user)->orWhere('au.id_user_parent', $user->id_user)->orderBy('id_user', 'asc')->limit(1)->get()->getRow();
        $db->close();
        $finalData = json_encode($builder);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }

    public function postUpdate_setting()
    {
        $request = request();
        $dataPost = $request->getJSON(true);
        $user = cekValidation('/master/user/update_setting');
        $db = db_connect();
        $builder = $db->table('app_users au');
        $query = $builder->where('au.id_user', $dataPost['id_user'])->orWhere('au.id_user_parent', $user->id_user);
        $update['merchant_name'] = $dataPost['merchant_name'];
        $update['merchant_address'] = $dataPost['merchant_address'];
        $update['merchant_wa'] = $dataPost['merchant_wa'];
        $update['tax_percentage'] = $dataPost['tax_percentage'];
        $query->update($update);
        $dataFinal = $query->join('app_user_privilege aup', 'aup.id_user_privilege = au.user_privilege')->where('au.id_user', $user->id_user)->orWhere('au.id_user_parent', $user->id_user)->orderBy('id_user', 'asc')->limit(1)->get()->getRow();
        $db->close();
        $finalData = json_encode($dataFinal);
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
        $user = cekValidation('/master/user/list');
        $db = db_connect();
        $builder = $db->table('app_users au')->join('app_user_privilege aup', 'aup.id_user_privilege = au.user_privilege')->where('au.id_user', $user->id_user)->orWhere('au.id_user_parent', $user->id_user)->get()->getResult();
        $db->close();
        $finalData = json_encode($builder);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }

    public function postCreate()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/master/user/create');
        $db = db_connect();
        $builder = $db->table('app_users au');
        if (isset($dataPost->password)) {
            $dataPost->password = hash('sha256', $dataPost->password);
        }
        $dataPost->id_user_parent = $user->id_user;
        $dataPost->merchant_wa = '0' . $dataPost->telp;

        if (
            $db->table('app_users')->where('telp', $dataPost->telp)->orWhere('email', $dataPost->email)->get()->getRow()
        ) {
            $data = '{
                "code": 1,
                "error": "Email or Telp/WA is already exists!",
                "message": "Email or Telp/WA is already exists!",
                "data": []
            }';
            return $this->response->setStatusCode(200)->setBody($data);
        }

        $query = $builder->insert($dataPost);
        $dataFinal = $builder->join('app_user_privilege aup', 'aup.id_user_privilege = au.user_privilege')->where('au.id_user', $user->id_user)->orWhere('au.id_user_parent', $user->id_user)->get()->getResult();
        $dataUser = $db->table('app_users au')->where('id_user', $dataPost['id_user'])->get()->getRow();
        $db->close();
        $finalData = json_encode($dataFinal);

        $waMessage = "*INFO DIGIPAYID* 
Pemilik Merchant DIGIPAYID menambahkan email anda *" . $dataPost->email . "* ke Merchant (*" . $dataUser->merchant_name . "*).";
        sendWA($dataPost->merchant_wa, $waMessage);

        echo '{
            "code": 0,
            "error": "",
            "message": "Data created successfully!",
            "data": ' . $finalData . '
        }';
    }

    public function postUpdate()
    {
        $request = request();
        $dataPost = $request->getJSON(true);
        $user = cekValidation('/master/user/update');
        $db = db_connect();
        $builder = $db->table('app_users au');
        $query = $builder->where('id_user', $dataPost['id_user']);
        if (isset($dataPost['password'])) {
            $dataPost['password'] = hash('sha256', $dataPost['password']);
        }
        $query->update($dataPost);
        $dataFinal = $query->join('app_user_privilege aup', 'aup.id_user_privilege = au.user_privilege')->where('au.id_user', $user->id_user)->orWhere('au.id_user_parent', $user->id_user)->get()->getResult();
        $db->close();
        $finalData = json_encode($dataFinal);

        $waMessage = "*INFO DIGIPAYID* 
Pemilik Merchant *" . $dataPost->merchant_name . "* melakukan perubahan akun anda.";
        sendWA('0' . $dataPost['telp'], $waMessage);

        echo '{
            "code": 0,
            "error": "",
            "message": "Data updated successfully!",
            "data": ' . $finalData . '
        }';
    }

    public function postDelete()
    {
        $request = request();
        $dataPost = $request->getJSON(true);
        $user = cekValidation('/master/user/delete');
        $db = db_connect();
        $builder = $db->table('app_users au');
        $query = $builder->where('id_user', $dataPost['id_user']);
        $query->delete();
        $dataFinal = $query->join('app_user_privilege aup', 'aup.id_user_privilege = au.user_privilege')->where('au.id_user', $user->id_user)->orWhere('au.id_user_parent', $user->id_user)->get()->getResult();
        $db->close();
        $finalData = json_encode($dataFinal);

        $waMessage = "*INFO DIGIPAYID* 
Pemilik Merchant *" . $dataPost->merchant_name . "* menghapus akun anda (*" . $dataPost['email'] . "*).";
        sendWA('0' . $dataPost['telp'], $waMessage);

        echo '{
            "code": 0,
            "error": "",
            "message": "Data deleted successfully!",
            "data": ' . $finalData . '
        }';
    }

    public function postPrivilege_lists()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/master/user/privilege_lists');
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

    public function postPrivilege_list()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/master/user/privilege_list');
        $db = db_connect();
        $builder = $db->table('app_user_privilege')->where('user_privilege_type', 2)->get()->getResult();
        $db->close();
        $finalData = json_encode($builder);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }

    public function postPrivilege_create()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/master/user/privilege_create');
        $db = db_connect();
        $builder = $db->table('app_user_privilege');
        $query = $builder->insert($dataPost);
        $dataFinal = $builder->get()->getResult();
        $db->close();
        $finalData = json_encode($dataFinal);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }

    public function postPrivilege_update()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/master/user/privilege_update');
        $db = db_connect();
        $builder = $db->table('app_user_privilege');
        $query = $builder->where('id_user_privilege', $dataPost['id_user_privilege']);
        $query->update($dataPost);
        $dataFinal = $query->get()->getResult();
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
