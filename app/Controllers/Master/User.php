<?php

namespace App\Controllers\Master;
use Config\Services;
use CodeIgniter\Files\File;

date_default_timezone_set("Asia/Bangkok");

class User extends BaseController
{
    public function index()
    {
        echo('welcome!');
    }

    public function postList()
    {   
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/master/user/list');
        $db = db_connect();
        $builder = $db->table('app_users')->where('id_user', $user->id_user)->orWhere('id_user', $user->id_user)->get()->getResult();
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
        $user = cekValidation('/master/user/create');
        $db = db_connect();
        $builder = $db->table('app_users');
        $query = $builder->insert($dataPost);
        $dataFinal = $builder->where('id_user', $user->id_user)->orWhere('id_user', $user->id_user)->get()->getResult();
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
        $user = cekValidation('/master/user/update');
        $db = db_connect();
        $builder = $db->table('app_users');
        $query = $builder->where('id_user', $dataPost['id_user']);
        $query->update($dataPost);
        $dataFinal = $query->where('id_user', $user->id_user)->orWhere('id_user', $user->id_user)->get()->getResult();
        $db->close();
        $finalData = json_encode($dataFinal);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": '.$finalData.'
        }';
    }

    public function postPrivilege_list()
    {   
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/master/user/privilege_list');
        $db = db_connect();
        $builder = $db->table('app_user_privilege')->get()->getResult();
        $db->close();
        $finalData = json_encode($builder);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": '.$finalData.'
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
            "data": '.$finalData.'
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
            "data": '.$finalData.'
        }';
    }

}
