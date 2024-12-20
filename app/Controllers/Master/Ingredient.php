<?php

namespace App\Controllers\Master;

use Config\Services;
use CodeIgniter\Files\File;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

date_default_timezone_set("Asia/Bangkok");

class Ingredient extends ResourceController
{

    use ResponseTrait;

    public function getIndex()
    {
        echo ('welcome!');
    }

    public function postList($internal = false)
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/master/ingredient/list');
        $table = ((int)$user->id_user_parent > 0) ? 'ingredient_' . $user->id_user_parent : 'ingredient_' . $user->id_user;
            
        $db = db_connect();
        $builder = $db->table($table)->get()->getResult();
        $builder2 = $db->table('master_satuan')->where('status', 1)->get()->getResult();
        $builder3 = $db->table($table . ' tbli')->select('id_satuan, code_bahan, nama_bahan, (select AVG(modal_avg) as modal from '.$table.' where stok_debet = 0 and code_bahan = tbli.code_bahan) as modal, SUM(stok_credit) - SUM(stok_debet) as total_stok')->where('is_stok', 1)->groupBy('id_satuan, code_bahan, nama_bahan')->distinct()->get()->getResult();
        $db->close();
        $finalData = json_encode($builder);
        $finalData2 = json_encode($builder2);
        $finalData3 = json_encode($builder3);
        
        $data[0] = $finalData;
        $data[1] = $finalData2;
        $data[2] = $finalData3;
        
        if ($internal) {
            return $data;
        } else {
            echo '{
                "code": 0,
                "error": "",
                "message": "",
                "data": ' . $finalData . ',
                "data_total": ' . $finalData3 . ',
                "satuan": ' . $finalData2 . '
            }';
        }
    }

    public function postCreate()
    {
        $request = request();
        $dataPost = $request->getJSON(true);
        $user = cekValidation('/master/ingredient/create');
        $table = ((int)$user->id_user_parent > 0) ? 'ingredient_' . $user->id_user_parent : 'ingredient_' . $user->id_user;
      
        unset($dataPost["harga"], $dataPost["stok"]);
        
        
        
        $db = db_connect();
        $builder = $db->table($table)->insert($dataPost);
        $db->close();
        $finalData = ($this->postList(true));
        
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData[0] . ',
            "data_total": ' . $finalData[2] . ',
            "satuan": ' . $finalData[1] . '
        }';
    }

    public function postUpdate()
    {
        $request = request();
        $dataPost = $request->getJSON(true);
        $codeBahan = $dataPost['code_bahan'];
        $user = cekValidation('/master/ingredient/update');
        $table = ((int)$user->id_user_parent > 0) ? 'ingredient_' . $user->id_user_parent : 'ingredient_' . $user->id_user;
        unset($dataPost["harga"], $dataPost["stok"]);
            
        $db = db_connect();
        $builder = $db->table($table)->where('code_bahan', $codeBahan)->update($dataPost);
        $db->close();
        $finalData = json_encode($this->postList(true));
        
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }

    public function postDelete()
    {
        $request = request();
        $dataPost = $request->getJSON(true);
        $codeBahan = $dataPost['code_bahan'];
        $user = cekValidation('/master/ingredient/delete');
        $table = ((int)$user->id_user_parent > 0) ? 'ingredient_' . $user->id_user_parent : 'ingredient_' . $user->id_user;
            
        $db = db_connect();
        $builder = $db->table($table)->where('code_bahan', $codeBahan)->delete();
        $db->close();
        $finalData = ($this->postList(true));
        
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData[0] . ',
            "data_total": ' . $finalData[2] . ',
            "satuan": ' . $finalData[1] . '
        }';
    }
}
