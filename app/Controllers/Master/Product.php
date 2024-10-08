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
            }
        } else {

            if ((int)$user->id_user_parent > 0) {
                $builder = $db->table('app_product_' . $user->id_user_parent)->where('product_status', 1)->get()->getResult();
            } else {
                $builder = $db->table('app_product_' . $user->id_user)->where('product_status', 1)->get()->getResult();
            }
        }

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
}
