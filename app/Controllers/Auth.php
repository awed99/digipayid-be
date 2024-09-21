<?php

namespace App\Controllers;

// use Config\Services;


class Auth extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }

    public function postLogout()
    {
        $session = session();
        $session->remove('login');
        $session->remove('token_login');
    }

    public function postSet_storage()
    {

        $request = request();
        $postData = $request->getJSON(true);

        $db = db_connect();
        $upsert['id'] = $postData['key'];
        $upsert['ip_address'] = getUserIP();
        $upsert['data'] = json_encode($postData['val']);
        $upsert['timestamp'] = date('Y-m-d H:i:s');
        $db->table('ci_sessions')->upsert($upsert);
        $db->close();

        $res[$postData['key']] = $postData['val'];
        $res['message'] = 'Store updated successfully.';
        echo json_encode($res);
        // $session->close();
    }

    public function postGet_storage()
    {
        $request = request();
        // $session = Services::session();
        $postData = $request->getJSON(true);

        if (isset($postData['key'])) {
            $db = db_connect();
            $data = $db->table('ci_sessions')->where('id', $postData['key'])->get()->getRow()->data;
            $db->close();
        } else {
            $data = (object)array();
        }

        $res[$postData['key']] = json_decode($data);
        $res['message'] = 'Store gotten successfully.';
        echo json_encode($res);
        // $session->close();
    }

    public function postRemove_storage()
    {
        $request = request();
        // $session = Services::session();
        // $session = session();
        $postData = $request->getJSON(true);


        if (isset($postData['email'])) {
            $db = db_connect();
            $db->table('ci_sessions')->where('id', $postData['email'])->delete();
            $db->close();
        }

        $res['message'] = 'Store removed successfully.';
        echo json_encode($res);
        // $session->close();
    }

    public function postCheck_auth()
    {
        $request = request();
        $postData = $request->getJSON(true);

        if (isset($postData['email'])) {
            $db = db_connect();
            $data = $db->table('ci_sessions')->where('id', $postData['email'])->get()->getRow()->data;
            $db->close();
        } else {
            $data = false;
        }

        $res['auth'] = ($data) ? json_decode($data) : null;
        $res['message'] = 'Store gotten successfully.';
        $res['data'] = ($postData);
        echo json_encode($res);
        // $session->close();
    }

    public function postCheck_token_login()
    {
        $request = request();
        $session = session();
        $postData = $request->getPost();
        $token_login = $postData['token_login'];
        if (!$session->get('token_login')) {
            $db = db_connect();
            $builder = $db->table('app_users')->where('token_login', $token_login);
            $dataUser = $builder->get()->getRow();
            $db->close();
            if ($dataUser) {
                $user = $dataUser;
                $session->set('login', $user);
                $session->set('token_login', $user->token_login);
                $session->set('token_api', $user->token_api);
            }
        }
        $session_token_login = $session->get('token_login');
        if ($token_login == $session_token_login) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function postCheck_token_api()
    {
        $request = request();
        $session = session();
        $postData = $request->getPost();
        $token_api = $postData['token_api'];
        if (!$session->get('token_api')) {
            $db = db_connect();
            $builder = $db->table('app_users')->where('token_api', $token_api);
            $dataUser = $builder->get()->getRow();
            $db->close();
            if ($dataUser) {
                $user = $dataUser;
                $session->set('login', $user);
                $session->set('token_login', $user->token_login);
                $session->set('token_api', $user->token_api);
            }
        }
        $session_token_api = $session->get('token_api');
        if ($token_api == $session_token_api) {
            echo 1;
        } else {
            echo 0;
        }
    }
}
