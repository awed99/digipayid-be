<?php

namespace App\Controllers\Master;

class Product extends BaseController
{
    public function index()
    {
        echo ('welcome!');
    }

    public function postCategories()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/master/product/categories');
        $db = db_connect();
        $builder = $db->table('app_product_category_' . $user->id_user)->get()->getResult();
        $db->close();
        $finalData = json_encode($builder);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }

    public function getList0()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation0('/master/product/lists0');
        $db = db_connect();
        $builder = $db->table('app_product_' . $user->id_user)->get()->getResult();
        $builder2 = $db->table('app_payment_method_' . $user->id_user)
            ->join('master_payment_method', 'master_payment_method.id_payment_method = app_payment_method_' . $user->id_user . '.id_payment_method')
            ->where('(payment_method_id_pg = 3 or payment_method_id_pg = 1)')
            ->where('status', 1)
            ->where('status_admin', 1)
            ->get()->getResult();
        $db->close();
        $finalData = json_encode($builder);
        $finalData2 = json_encode($builder2);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . ',
            "pc": ' . $finalData2 . '
            
        }';
    }

    public function getList()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/master/product/lists');
        $db = db_connect();
        $builder = $db->table('app_product_' . $user->id_user)->get()->getResult();
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
        $user = cekValidation('/master/product/lists');
        $db = db_connect();
        $builder = $db->table('app_product_' . $user->id_user)->get()->getResult();
        $categories = $db->table('app_product_category_' . $user->id_user)->get()->getResult();
        $db->close();
        $finalData = json_encode($builder);
        $categories = json_encode($categories);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . ',
            "categories": ' . $categories . '
        }';
    }

    public function postList0()
    {
        $request = request();
        $dataPost = $request->getJSON(true);
        $user = cekValidation0('/master/product/list0');
        $db = db_connect();

        if ($dataPost['search']) {

            if ((int)$user->id_user_parent > 0) {

                // print_r(1);
                // print_r(json_encode($user));
                $builder = $db->table('app_product_' . $user->id_user_parent)
                    ->groupStart()
                    ->where('product_status', 1)
                    ->where('1 = 1')
                    ->groupEnd()
                    ->groupStart()
                    ->orLike('product_code', $dataPost['search'])
                    ->orLike('product_barcode', $dataPost['search'])
                    ->orLike('product_name', $dataPost['search'])
                    ->groupEnd()
                    ->get()->getResult();
                
                $pc = $db->table('app_payment_method_' . $user->id_user_parent)
                    ->join('master_payment_method', 'master_payment_method.id_payment_method = app_payment_method_' . $user->id_user . '.id_payment_method')
                    ->where('(payment_method_id_pg = 3 or payment_method_id_pg = 1)')
                    ->where('master_payment_method.payment_method_type >', 0)
                    ->where('status', 1)
                    ->where('status_admin', 1)
                    ->get()->getResult();
                    
                $categories = $db->table('app_product_category_' . $user->id_user_parent)->get()->getResult();
            } else {

                // print_r(2);
                // print_r(json_encode($user));
                $builder = $db->table('app_product_' . $user->id_user)
                    ->groupStart()
                    ->where('product_status', 1)
                    ->where('1 = 1')
                    ->groupEnd()
                    ->groupStart()
                    ->orLike('product_code', $dataPost['search'])
                    ->orLike('product_barcode', $dataPost['search'])
                    ->orLike('product_name', $dataPost['search'])
                    ->groupEnd()
                    ->get()->getResult();
                
                $pc = $db->table('app_payment_method_' . $user->id_user)
                    ->join('master_payment_method', 'master_payment_method.id_payment_method = app_payment_method_' . $user->id_user . '.id_payment_method')
                    ->where('(payment_method_id_pg = 3 or payment_method_id_pg = 1)')
                    ->where('master_payment_method.payment_method_type >', 0)
                    ->where('status', 1)
                    ->where('status_admin', 1)
                    ->get()->getResult();
                    
                $categories = $db->table('app_product_category_' . $user->id_user)->get()->getResult();
            }
        } else {

            if ((int)$user->id_user_parent > 0) {
                $builder = $db->table('app_product_' . $user->id_user_parent)->where('product_status', 1)->get()->getResult();
                $categories = $db->table('app_product_category_' . $user->id_user_parent)->get()->getResult();
                $pc = $db->table('app_payment_method_' . $user->id_user_parent)
                    ->join('master_payment_method', 'master_payment_method.id_payment_method = app_payment_method_' . $user->id_user . '.id_payment_method')
                    ->where('(payment_method_id_pg = 3 or payment_method_id_pg = 1)')
                    ->where('master_payment_method.payment_method_type >', 0)
                    ->where('status', 1)
                    ->where('status_admin', 1)
                    ->get()->getResult();
            } else {
                $builder = $db->table('app_product_' . $user->id_user)->where('product_status', 1)->get()->getResult();
                $categories = $db->table('app_product_category_' . $user->id_user)->get()->getResult();
                $pc = $db->table('app_payment_method_' . $user->id_user)
                    ->join('master_payment_method', 'master_payment_method.id_payment_method = app_payment_method_' . $user->id_user . '.id_payment_method')
                    ->where('(payment_method_id_pg = 3 or payment_method_id_pg = 1)')
                    ->where('master_payment_method.payment_method_type >', 0)
                    ->where('status', 1)
                    ->where('status_admin', 1)
                    ->get()->getResult();
            }
        }

        $db->close();
        $finalData = json_encode($builder);
        $categories = json_encode($categories);
        $pc = json_encode($pc);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . ',
            "categories": ' . $categories . ',
            "payment_channels": ' . $pc . '
        }';
    }

    public function postList()
    {
        $request = request();
        $dataPost = $request->getJSON(true);
        $user = cekValidation('/master/product/list');
        $db = db_connect();

        if ($dataPost['search']) {

            if ((int)$user->id_user_parent > 0) {

                // print_r(1);
                // print_r(json_encode($user));
                $builder = $db->table('app_product_' . $user->id_user_parent)
                    ->groupStart()
                    ->where('product_status', 1)
                    ->where('1 = 1')
                    ->groupEnd()
                    ->groupStart()
                    ->orLike('product_code', $dataPost['search'])
                    ->orLike('product_barcode', $dataPost['search'])
                    ->orLike('product_name', $dataPost['search'])
                    ->groupEnd()
                    ->get()->getResult();
                $categories = $db->table('app_product_category_' . $user->id_user_parent)->get()->getResult();
            } else {

                // print_r(2);
                // print_r(json_encode($user));
                $builder = $db->table('app_product_' . $user->id_user)
                    ->groupStart()
                    ->where('product_status', 1)
                    ->where('1 = 1')
                    ->groupEnd()
                    ->groupStart()
                    ->orLike('product_code', $dataPost['search'])
                    ->orLike('product_barcode', $dataPost['search'])
                    ->orLike('product_name', $dataPost['search'])
                    ->groupEnd()
                    ->get()->getResult();
                $categories = $db->table('app_product_category_' . $user->id_user)->get()->getResult();
            }
        } else {

            if ((int)$user->id_user_parent > 0) {
                $builder = $db->table('app_product_' . $user->id_user_parent)->where('product_status', 1)->get()->getResult();
                $categories = $db->table('app_product_category_' . $user->id_user_parent)->get()->getResult();
            } else {
                $builder = $db->table('app_product_' . $user->id_user)->where('product_status', 1)->get()->getResult();
                $categories = $db->table('app_product_category_' . $user->id_user)->get()->getResult();
            }
        }

        $db->close();
        $finalData = json_encode($builder);
        $categories = json_encode($categories);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . ',
            "categories": ' . $categories . '
        }';
    }

    public function postCreate()
    {
        $request = request();
        $dataPost = $request->getPost();
        $dataPost['product_image_url'] = upload_file($request);
        $user = cekValidation('/master/product/create');
        $db = db_connect();
        $builder = $db->table('app_product_' . $user->id_user);
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

    public function postUpdate()
    {
        $request = request();
        $dataPost = $request->getPost();
        $dataPost['product_image_url'] = upload_file($request);
        if (isset($dataPost['product_image_url']['errors'])) {
            echo '{
                "code": 0,
                "error": "'.$dataPost['product_image_url']['errors'].'",
                "message": "'.$dataPost['product_image_url']['errors'].'",
                "data": null
            }';
            die();
        }
        $user = cekValidation('/master/product/update');
        $db = db_connect();
        $builder = $db->table('app_product_' . $user->id_user);
        $query = $builder->where('id_product', $dataPost['id_product']);
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

    public function postDelete()
    {
        $request = request();
        $dataPost = $request->getPost() ?? $request->getJSON();
        $user = cekValidation('/master/product/delete');
        $db = db_connect();
        $builder = $db->table('app_product_' . $user->id_user);
        $query = $builder->where('id_product', $dataPost->id_product ?? $dataPost['id_product']);
        $query->delete();
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

    public function postCategory_create()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/master/product/category_create');
        $db = db_connect();
        $builder = $db->table('app_product_category_' . $user->id_user);
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

    public function postCategory_update()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/master/product/category_update');
        $db = db_connect();
        $builder = $db->table('app_product_category_' . $user->id_user);
        $query = $builder->where('id_product_category', $dataPost->id_product_category);
        $query->update($dataPost);
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

    public function postCategory_delete()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/master/product/category_delete');
        $db = db_connect();
        $builder = $db->table('app_product_category_' . $user->id_user);
        $query = $builder->where('id_product_category', $dataPost->id_product_category);
        $query->delete();
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
    
    public function postList_racik($internal = false)
    {
        $request = request();
        $dataPost = $request->getJSON(true);
        $user = cekValidation('/master/product/list_racik');
        $table = ((int)$user->id_user_parent > 0) ? 'racik_' . $user->id_user_parent : 'racik_' . $user->id_user;
        $tableIngredient = ((int)$user->id_user_parent > 0) ? 'ingredient_' . $user->id_user_parent : 'ingredient_' . $user->id_user;
      
        // unset($dataPost["harga"], $dataPost["stok"]);
        
        
        
        $db = db_connect();
        $builder = $db->table($table.' r')->select('*, (select nama_bahan from '.$tableIngredient.' i where code_bahan = r.code_bahan limit 1) nama_bahan, (select satuan from master_satuan where id_satuan = r.id_satuan) satuan, ((select AVG(modal_avg) as modal from '.$tableIngredient.' where stok_debet = 0 and code_bahan = r.code_bahan)*takaran) modal')
        ->where('kode_racik', $dataPost['kode_racik'])->get()->getResult();
        $db->close();
        $finalData = json_encode($builder);
        
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }
    
    public function postCreate_racik()
    {
        $request = request();
        $dataPost = $request->getJSON(true);
        $user = cekValidation('/master/product/create_racik');
        $table = ((int)$user->id_user_parent > 0) ? 'racik_' . $user->id_user_parent : 'racik_' . $user->id_user;
      
        unset($dataPost["nama_bahan"]);
        
        $db = db_connect();
        
        $where['kode_racik'] = $dataPost['kode_racik'];
        $where['code_bahan'] = $dataPost['code_bahan'];
        $isset = $db->table($table)->where($where)->get()->getRowArray();
        
        if ($isset) {
            $builder = $db->table($table)->where($where)->update($dataPost);
        } else {
            $builder = $db->table($table)->insert($dataPost);
        }
        
        $db->close();
        // $finalData = ($this->postList(true));
        
        $this->postList_racik(true);
        // echo '{
        //     "code": 0,
        //     "error": "",
        //     "message": "",
        //     "data": ' . $finalData[0] . ',
        //     "data_total": ' . $finalData[2] . ',
        //     "satuan": ' . $finalData[1] . '
        // }';
    }

    public function postDelete_racik()
    {
        $request = request();
        $dataPost = $request->getJSON(true);
        $user = cekValidation('/master/product/delete_racik');
        $table = ((int)$user->id_user_parent > 0) ? 'racik_' . $user->id_user_parent : 'racik_' . $user->id_user;
            
        $db = db_connect();
        $builder = $db->table($table)->where('id_racik', $dataPost['id_racik'])->delete();
        $db->close();
        
        $this->postList_racik(true);
        
    }

    // public function postIngredient()
    // {
    //     $request = request();
    //     $dataPost = $request->getJSON();
    //     $user = cekValidation('/master/product/ingredient');
    //     $db = db_connect();
    //     $builder = $db->table('app_ingredient_' . $user->id_user)->get()->getResult();
    //     $db->close();
    //     $finalData = json_encode($builder);
    //     echo '{
    //         "code": 0,
    //         "error": "",
    //         "message": "",
    //         "data": ' . $finalData . '
    //     }';
    // }

    // public function postIngredient_create()
    // {
    //     $request = request();
    //     $dataPost = $request->getJSON();
    //     $user = cekValidation('/master/product/ingredient_create');
    //     $db = db_connect();
    //     $builder = $db->table('app_ingredient_' . $user->id_user);
    //     $query = $builder->insert($dataPost);
    //     $dataFinal = $builder->get()->getResult();
    //     $db->close();
    //     $finalData = json_encode($dataFinal);
    //     echo '{
    //         "code": 0,
    //         "error": "",
    //         "message": "",
    //         "data": ' . $finalData . '
    //     }';
    // }

    // public function postIngredient_update()
    // {
    //     $request = request();
    //     $dataPost = $request->getJSON();
    //     $user = cekValidation('/master/product/ingredient_update');
    //     $db = db_connect();
    //     $builder = $db->table('app_ingredient_' . $user->id_user);
    //     $query = $builder->where('id_bahan', $dataPost->id_bahan);
    //     $query->update($dataPost);
    //     $dataFinal = $builder->get()->getResult();
    //     $db->close();
    //     $finalData = json_encode($dataFinal);
    //     echo '{
    //         "code": 0,
    //         "error": "",
    //         "message": "",
    //         "data": ' . $finalData . '
    //     }';
    // }

    // public function postIngredient_delete()
    // {
    //     $request = request();
    //     $dataPost = $request->getJSON();
    //     $user = cekValidation('/master/product/ingredient_delete');
    //     $db = db_connect();
    //     $builder = $db->table('app_ingredient_' . $user->id_user);
    //     $query = $builder->where('id_bahan', $dataPost->id_bahan);
    //     $query->delete();
    //     $dataFinal = $builder->get()->getResult();
    //     $db->close();
    //     $finalData = json_encode($dataFinal);
    //     echo '{
    //         "code": 0,
    //         "error": "",
    //         "message": "",
    //         "data": ' . $finalData . '
    //     }';
    // }
}
