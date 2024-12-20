<?php

namespace App\Controllers\Master;

use Config\Services;
use CodeIgniter\Files\File;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

date_default_timezone_set("Asia/Bangkok");

class Satuan extends ResourceController
{

    use ResponseTrait;

    public function index()
    {
        echo ('welcome!');
    }

    public function postList()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/master/satuan/list');
        $db = db_connect();
        $builder = $db->table('master_satuan')->where('status', 1)->get()->getResult();
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
