<?php


namespace App\Controllers\Admin\Transactions;

use Config\Services;
use CodeIgniter\Files\File;

date_default_timezone_set("Asia/Bangkok");

class Orders extends BaseController
{
    public function index()
    {
        echo ('welcome!');
    }

    public function postList()
    {
        $user = cekValidation('/admin/transactions/orders/list');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();

        if ((int)$dataPost->id_merchant > 0) {
            $builder = $db->table('app_transactions_' . $dataPost->id_merchant);
            if (isset($dataPost->start_date)) {
                $builder->where('time_transaction >=', $dataPost->start_date . ' 00:00:00');
            }
            if (isset($dataPost->end_date)) {
                $builder->where('time_transaction <=', $dataPost->end_date . ' 23:59:59');
            }
            $result = $builder->orderBy('id_transaction ', 'desc')->get()->getResult();

            $db->close();
            $finalData = json_encode($result);
        } else {
            $finalData = '[]';
        }

        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }

    public function postGet_products()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/admin/transactions/orders/get_products');
        $db = db_connect();

        $builder = $db->table('app_transaction_products_' . $dataPost->id_merchant)->where('invoice_number', $dataPost->invoice_number)->get()->getResult();

        $db->close();
        $finalData = json_encode($builder);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }

    public function postCancel_transaction()
    {
        $user = cekValidation('/admin/transactions/orders/cancel_transaction');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();

        $builder = $db->table('app_transactions_' . $dataPost->id_merchant);
        if (isset($dataPost->invoice_number)) {
            $builder->where('invoice_number', $dataPost->invoice_number)->update(['status_transaction' => 9]);
        }
        $result = $builder->orderBy('id_transaction ', 'desc')->get()->getResult();

        $db->close();
        $finalData = json_encode($result);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "data": ' . $finalData . '
        }';
    }

    public function postGet_temp_products()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/admin/transactions/orders/get_temp_products');
        $db = db_connect();

        if ((int)$user->id_user_parent > 0) {
            $builder = $db->table('app_transaction_products_temp_' . $user->id_user_parent)->get()->getResult();
        } else {
            $builder = $db->table('app_transaction_products_temp_' . $user->id_user)->get()->getResult();
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

    public function postCreate_temp_products()
    {
        $request = request();
        $dataPost = $request->getPost();
        $dataPost['product_image_url'] = upload_file($request);
        $user = cekValidation('/admin/transactions/orders/create_temp_products');
        $db = db_connect();

        if ((int)$user->id_user_parent > 0) {
            $builder = $db->table('app_transaction_products_temp_' . $user->id_user_parent);
        } else {
            $builder = $db->table('app_transaction_products_temp_' . $user->id_user);
        }

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

    public function postCreate_temp_products2()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/admin/transactions/orders/create_temp_products2');
        $db = db_connect();

        if ((int)$user->id_user_parent > 0) {
            $builder = $db->table('app_transaction_products_temp_' . $user->id_user_parent);
        } else {
            $builder = $db->table('app_transaction_products_temp_' . $user->id_user);
        }


        if ((int)$user->id_user_parent > 0) {
            $builder0 = $db->table('app_product_' . $user->id_user_parent);
        } else {
            $builder0 = $db->table('app_product_' . $user->id_user);
        }
        $q = $builder0->whereIn('id_product', ($dataPost->id_product));

        $data = array();
        foreach ($q->get()->getResultArray() as $value) {
            $_data = $value;
            $_data['product_qty'] = 1;

            array_push($data, $_data);
        }

        $query = $builder->insertBatch($data);
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

    public function postUpdate_temp_products()
    {
        $request = request();
        $dataPost = $request->getPost();
        if (isset($dataPost['userfile'])) {
            $dataPost['product_image_url'] = upload_file($request);
        }
        $user = cekValidation('/admin/transactions/orders/update_temp_products');
        $db = db_connect();

        if ((int)$user->id_user_parent > 0) {
            $builder = $db->table('app_transaction_products_temp_' . $user->id_user_parent);
        } else {
            $builder = $db->table('app_transaction_products_temp_' . $user->id_user);
        }

        $query = $builder->where('id', $dataPost['id']);
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

    public function postDelete_temp_products()
    {
        $request = request();
        $dataPost = $request->getJSON();
        $user = cekValidation('/admin/transactions/orders/delete_temp_products');
        $db = db_connect();

        if ((int)$user->id_user_parent > 0) {
            $builder = $db->table('app_transaction_products_temp_' . $user->id_user_parent);
        } else {
            $builder = $db->table('app_transaction_products_temp_' . $user->id_user);
        }

        $query = $builder->where('id', $dataPost->id);
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

    public function postCreate()
    {
        $user = cekValidation('/admin/transactions/orders/create');
        $request = request();
        $dataPost = $request->getJSON(true);
        $dataPost['invoice_number'] = 'DIGIPAYID-' . $user->id_user . '-' . strtoupper(substr(md5(Date('YmdHis')), 5, 8));
        $dataPost['external_id'] = $dataPost['invoice_number'];
        $dataPost['id_user'] = $user->id_user;
        $dataPost['amount_to_back'] = (int)$dataPost['amount_to_pay'] - (int)$dataPost['amount'];
        $dataPost['amount_to_receive'] = (int)$dataPost['amount_to_pay'] - (int)$dataPost['amount_to_back'] - (int)$dataPost['fee'];
        if ((int)$dataPost['id_payment_method'] == 0) {
            $dataPost['status_transaction'] = 1;
            $dataPost['status_payment'] = 1;
            // $dataPost['payment_method_code'] = 'CASH';
            // $dataPost['payment_method_name'] = 'TUNAI (CASH)';
        }
        $db = db_connect();

        if ((int)$user->id_user_parent > 0) {
            $builder = $db->table('app_transactions_' . $user->id_user_parent);
            $builder0 = $db->table('app_transaction_products_temp_' . $user->id_user_parent);
            $builder1 = $db->table('app_transaction_products_' . $user->id_user_parent);
            $builder2 = $db->table('app_journal_finance_' . $user->id_user_parent);
        } else {
            $builder = $db->table('app_transactions_' . $user->id_user);
            $builder0 = $db->table('app_transaction_products_temp_' . $user->id_user);
            $builder1 = $db->table('app_transaction_products_' . $user->id_user);
            $builder2 = $db->table('app_journal_finance_' . $user->id_user);
        }

        $data = array();
        foreach ($builder0->get()->getResultArray() as $value) {
            $_data = $value;
            $_data['id'] = null;
            $_data['invoice_number'] = $dataPost['invoice_number'];

            array_push($data, $_data);
        }

        $payment = ((int)$dataPost['id_payment_method'] === 0) ? null : json_encode(tokopay_generate_qris((int)$dataPost['amount_to_pay'], $dataPost['payment_method_code'], $dataPost['invoice_number'], $user));

        $paymentJSON = str_replace('"{', '{', str_replace('}"', '}', str_replace('""', '', str_replace('\\', '', json_encode($payment)))));
        $dataPost['payment_response'] = ((int)$dataPost['id_payment_method'] === 0) ? null : $paymentJSON;

        $builder->insert($dataPost);
        $builder1->insertBatch($data);


        $journal_insert = array();
        $journal_insert_admin = array();

        $journal_insert0['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert0['amount_credit'] = $dataPost['amount_to_receive'];
        $journal_insert0['amount_debet'] = 0;
        $journal_insert0['accounting_type'] = 1;
        $journal_insert0['id_payment_method'] = (int)$dataPost['id_payment_method'];
        $journal_insert0['status'] = ((int)$dataPost['id_payment_method'] > 0) ? 0 : 1;
        $journal_insert0['description'] = 'Penjualan ' . $dataPost['invoice_number'];
        array_push($journal_insert, $journal_insert0);

        $journal_insert1['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert1['amount_credit'] = 0;
        $journal_insert1['amount_debet'] = (float)$dataPost['app_fee'] + (float)$dataPost['pg_fee'];
        $journal_insert1['accounting_type'] = 101;
        $journal_insert1['id_payment_method'] = (int)$dataPost['id_payment_method'];
        $journal_insert1['status'] = ((int)$dataPost['id_payment_method'] > 0) ? 0 : 1;
        $journal_insert1['description'] = 'Fee ' . $dataPost['invoice_number'];
        array_push($journal_insert, $journal_insert1);


        // $journal_insert_admin0['invoice_number'] = $dataPost['invoice_number'];
        // $journal_insert_admin0['id_user'] = $user->id_user;
        // $journal_insert_admin0['id_user_parent'] = $user->id_user_parent;
        // $journal_insert_admin0['amount_credit'] = (float)$dataPost['app_fee'];
        // $journal_insert_admin0['amount_debet'] = 0;
        // $journal_insert_admin0['accounting_type'] = 101;
        // $journal_insert_admin0['status'] = ((int)$dataPost['id_payment_method'] > 0) ? 1 : 0;
        // $journal_insert_admin0['description'] = 'Fee App '.$dataPost['invoice_number'].' (Keuntungan)';
        // array_push($journal_insert_admin, $journal_insert_admin0);

        $journal_insert_admin0['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert_admin0['id_user'] = $user->id_user;
        $journal_insert_admin0['id_user_parent'] = $user->id_user_parent;
        $journal_insert_admin0['amount_credit'] = (float)$dataPost['amount_to_receive'];
        $journal_insert_admin0['amount_debet'] = 0;
        $journal_insert_admin0['accounting_type'] = 1;
        $journal_insert_admin0['id_payment_method'] = (int)$dataPost['id_payment_method'];
        $journal_insert_admin0['status'] = ((int)$dataPost['id_payment_method'] > 0) ? 0 : 1;
        $journal_insert_admin0['description'] = 'Penjualan ' . $dataPost['invoice_number'];
        array_push($journal_insert_admin, $journal_insert_admin0);

        $journal_insert_admin1['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert_admin1['id_user'] = $user->id_user;
        $journal_insert_admin1['id_user_parent'] = $user->id_user_parent;
        $journal_insert_admin1['amount_credit'] = 0;
        $journal_insert_admin1['amount_debet'] = (float)$dataPost['app_fee'];
        $journal_insert_admin1['accounting_type'] = 1001;
        $journal_insert_admin1['id_payment_method'] = (int)$dataPost['id_payment_method'];
        $journal_insert_admin1['status'] = ((int)$dataPost['id_payment_method'] > 0) ? 0 : 1;
        $journal_insert_admin1['description'] = 'Fee App ' . $dataPost['invoice_number'];
        array_push($journal_insert_admin, $journal_insert_admin1);

        if (((int)$dataPost['id_payment_method'] > 0)) {
            $journal_insert_admin2['invoice_number'] = $dataPost['invoice_number'];
            $journal_insert_admin2['id_user'] = $user->id_user;
            $journal_insert_admin2['id_user_parent'] = $user->id_user_parent;
            $journal_insert_admin2['amount_credit'] = 0;
            $journal_insert_admin2['amount_debet'] = (float)$dataPost['pg_fee'];
            $journal_insert_admin2['accounting_type'] = 1002;
            $journal_insert_admin2['id_payment_method'] = (int)$dataPost['id_payment_method'];
            $journal_insert_admin2['status'] = ((int)$dataPost['id_payment_method'] > 0) ? 0 : 1;
            $journal_insert_admin2['description'] = 'Fee PG ' . $dataPost['invoice_number'];
            array_push($journal_insert_admin, $journal_insert_admin2);
        }

        $builder2->insertBatch($journal_insert);
        $db->table('admin_journal_finance')->insertBatch($journal_insert_admin);

        // if ((int)$user->id_user_parent > 0) {
        //     $db->table('app_transaction_products_temp_'.$user->id_user_parent)->truncate();
        // } else {
        //     $db->table('app_transaction_products_temp_'.$user->id_user)->truncate();
        //     //app_journal_finance_40
        // }
        // }

        if ((int)$user->id_user_parent > 0) {
            $db->table('app_transaction_products_temp_' . $user->id_user_parent)->truncate();
        } else {
            $db->table('app_transaction_products_temp_' . $user->id_user)->truncate();
            //app_journal_finance_40
        }


        $dataFinal = $builder0->get()->getResult();
        $db->close();
        $finalData = json_encode($dataFinal);

        $code = ($dataPost['id_payment_method'] === 0) ? 0 : 1;
        echo '{
            "code": ' . $code . ',
            "error": "",
            "message": "",
            "data": ' . $finalData . ',
            "payment": ' . $paymentJSON . '
        }';


        if (($dataPost['email_customer'] != '')) {
            sendReceipt('email', $dataPost, $builder->where('invoice_number', $dataPost['invoice_number'])->orderBy('id_transaction', 'DESC')->get()->getRow(), $builder1->where('invoice_number', $dataPost['invoice_number'])->get()->getResult(), $user, json_decode($paymentJSON));
        }

        if (($dataPost['wa_customer'] != '')) {
            sendReceipt('whatsapp', $dataPost, $builder->where('invoice_number', $dataPost['invoice_number'])->orderBy('id_transaction', 'DESC')->get()->getRow(), $builder1->where('invoice_number', $dataPost['invoice_number'])->get()->getResult(), $user, json_decode($paymentJSON));
        }
    }

    public function postUpdate()
    {
        $user = cekValidation('/admin/transactions/orders/update');
        $request = request();
        $dataPost = $request->getJSON(true);
        $dataPost['invoice_number'] = $dataPost['invoice_number'] ? $dataPost['invoice_number'] : 'DIGIPAYID-' . $user->id_user . '-' . strtoupper(substr(md5(Date('YmdHis')), 5, 8));
        $dataPost['external_id'] = $dataPost['invoice_number'];
        $dataPost['id_user'] = $user->id_user;
        $dataPost['amount_to_back'] = (int)$dataPost['amount_to_pay'] - (int)$dataPost['amount'];
        $dataPost['amount_to_receive'] = (int)$dataPost['amount_to_pay'] - (int)$dataPost['amount_to_back'] - (int)$dataPost['fee'];
        if ($dataPost['id_payment_method'] == 0) {
            $dataPost['status_transaction'] = 1;
            $dataPost['status_payment'] = 1;
        }
        $db = db_connect();

        if ((int)$user->id_user_parent > 0) {
            $builder = $db->table('app_transactions_' . $user->id_user_parent);
            $builder0 = $db->table('app_transaction_products_temp_' . $user->id_user_parent);
            $builder1 = $db->table('app_journal_finance_' . $user->id_user_parent);
        } else {
            $builder = $db->table('app_transactions_' . $user->id_user);
            $builder0 = $db->table('app_transaction_products_temp_' . $user->id_user);
            $builder1 = $db->table('app_journal_finance_' . $user->id_user);
        }

        $data = array();
        foreach ($builder0->get()->getResultArray() as $value) {
            $_data = $value;
            $_data['id'] = null;
            $_data['invoice_number'] = $dataPost['invoice_number'];

            array_push($data, $_data);
        }

        $payment = ($dataPost['id_payment_method'] === 0) ? null : json_encode(tokopay_generate_qris((int)$dataPost['amount_to_pay'], $dataPost['payment_method_code'], $dataPost['invoice_number'], $user));

        $paymentJSON = str_replace('"{', '{', str_replace('}"', '}', str_replace('""', '', str_replace('\\', '', json_encode($payment)))));
        $dataPost['payment_response'] = $paymentJSON;

        $builder->where('invoice_number', $dataPost['invoice_number'])->update($dataPost);
        // $builder1->insertBatch($data);

        if (($dataPost['id_payment_method'] === 0)) {

            $journal_insert = array();
            $journal_insert_admin = array();

            $journal_insert0['invoice_number'] = $dataPost['invoice_number'];
            $journal_insert0['amount_credit'] = $dataPost['amount_to_receive'];
            $journal_insert0['amount_debet'] = 0;
            $journal_insert0['accounting_type'] = 1;
            $journal_insert0['description'] = 'Penjualan ' . $dataPost['invoice_number'] . ' (Tunai)';
            array_push($journal_insert, $journal_insert0);

            if ((int)$dataPost['fee_on_merchant'] > 0) {
                $journal_insert1['invoice_number'] = $dataPost['invoice_number'];
                $journal_insert1['amount_credit'] = 0;
                $journal_insert1['amount_debet'] = (int)$dataPost['app_fee'] + (int)$dataPost['pg_fee'];
                $journal_insert1['accounting_type'] = 2;
                $journal_insert1['description'] = 'Fee ' . $dataPost['invoice_number'] . ' (Tunai)';
                array_push($journal_insert, $journal_insert1);
            }

            $journal_insert_admin0['invoice_number'] = $dataPost['invoice_number'];
            $journal_insert_admin0['id_user'] = $user->id_user;
            $journal_insert_admin0['id_user_parent'] = $user->id_user_parent;
            $journal_insert_admin0['amount_credit'] = (int)$dataPost['app_fee'];
            $journal_insert_admin0['amount_debet'] = 0;
            $journal_insert_admin0['accounting_type'] = 101;
            $journal_insert_admin0['description'] = 'Fee App ' . $dataPost['invoice_number'] . ' (Keuntungan Tunai)';
            array_push($journal_insert_admin, $journal_insert_admin0);

            // $journal_insert_admin1['id_user'] = $user->id_user;
            // $journal_insert_admin1['id_user_parent'] = $user->id_user_parent;
            // $journal_insert_admin1['amount_credit'] = 0;
            // $journal_insert_admin1['amount_debet'] = (int)$dataPost['pg_fee'];
            // $journal_insert_admin1['accounting_type'] = 102;
            // $journal_insert_admin1['description'] = 'Fee PG '.$dataPost['invoice_number'];
            // array_push($journal_insert_admin, $journal_insert_admin1);

            $builder1->insertBatch($journal_insert);
            $db->table('admin_journal_finance')->insertBatch($journal_insert_admin);

            if ((int)$user->id_user_parent > 0) {
                $db->table('app_transaction_products_temp_' . $user->id_user_parent)->truncate();
            } else {
                $db->table('app_transaction_products_temp_' . $user->id_user)->truncate();
            }
        }

        $dataFinal = $builder0->get()->getResult();
        $db->close();
        $finalData = json_encode($dataFinal);

        $code = ($dataPost['id_payment_method'] === 0) ? 0 : 1;
        echo '{
            "code": ' . $code . ',
            "error": "",
            "message": "",
            "data": ' . $finalData . ',
            "payment": ' . $paymentJSON . '
        }';
    }
}
