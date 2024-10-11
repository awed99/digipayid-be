<?php


namespace App\Controllers\Transactions;

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
        $user = cekValidation('/transactions/orders/list');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();

        if ((int)$user->id_user_parent > 0) {
            $builder = $db->table('app_transactions_' . $user->id_user_parent);
            if (isset($dataPost->start_date)) {
                $builder->where('time_transaction >=', $dataPost->start_date . ' 00:00:00');
            }
            if (isset($dataPost->end_date)) {
                $builder->where('time_transaction <=', $dataPost->end_date . ' 23:59:59');
            }
            $result = $builder->orderBy('id_transaction ', 'desc')->get()->getResult();
        } else {
            $builder = $db->table('app_transactions_' . $user->id_user);
            if (isset($dataPost->start_date)) {
                $builder->where('time_transaction >=', $dataPost->start_date . ' 00:00:00');
            }
            if (isset($dataPost->end_date)) {
                $builder->where('time_transaction <=', $dataPost->end_date . ' 23:59:59');
            }
            $result = $builder->orderBy('id_transaction ', 'desc')->get()->getResult();
        }

        $db->close();
        $finalData = json_encode($result);
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
        $user = cekValidation('/transactions/orders/get_products');
        $db = db_connect();

        if ((int)$user->id_user_parent > 0) {
            $builder = $db->table('app_transaction_products_' . $user->id_user_parent)->where('invoice_number', $dataPost->invoice_number)->get()->getResult();
        } else {
            $builder = $db->table('app_transaction_products_' . $user->id_user)->where('invoice_number', $dataPost->invoice_number)->get()->getResult();
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

    public function postCancel_transaction()
    {
        $user = cekValidation('/transactions/orders/cancel_transaction');
        $request = request();
        $dataPost = $request->getJSON();
        $db = db_connect();

        $status['status'] = 9;
        $status['updated_at'] = date('Y-m-d H:i:s');
        $db->table("admin_journal_finance")->where('invoice_number', $dataPost->invoice_number)->update($status);

        if ((int)$user->id_user_parent > 0) {
            $db->table("app_journal_finance_" . $user->id_user_parent)->where('invoice_number', $dataPost->invoice_number)->update($status);
            $builder = $db->table('app_transactions_' . $user->id_user_parent);
            if (isset($dataPost->invoice_number)) {
                $builder->where('invoice_number', $dataPost->invoice_number)->update(['status_transaction' => 9]);
            }
            $result = $builder->orderBy('id_transaction ', 'desc')->get()->getResult();
        } else {
            $db->table("app_journal_finance_" . $user->id_user)->where('invoice_number', $dataPost->invoice_number)->update($status);
            $builder = $db->table('app_transactions_' . $user->id_user);
            if (isset($dataPost->invoice_number)) {
                $builder->where('invoice_number', $dataPost->invoice_number)->update(['status_transaction' => 9]);
            }
            $result = $builder->orderBy('id_transaction ', 'desc')->get()->getResult();
        }

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
        $user = cekValidation('/transactions/orders/get_temp_products');
        $db = db_connect();

        if ((int)$user->id_user_parent > 0) {
            $builder = $db->table('app_transaction_products_temp_' . $user->id_user_parent)->get()->getResult();
            $totalTRX = $db->table('app_transactions_' . $user->id_user_parent)->get()->getNumRows();
        } else {
            $builder = $db->table('app_transaction_products_temp_' . $user->id_user)->get()->getResult();
            $totalTRX = $db->table('app_transactions_' . $user->id_user)->get()->getNumRows();
        }
        $is_free = (int)$totalTRX <= (int)getenv('FREE_TRX_PROMOTION') ? 'true' : 'false';
        // $totalTRX = count($builder);

        $db->close();
        $finalData = json_encode($builder);
        echo '{
            "code": 0,
            "error": "",
            "message": "",
            "is_free": ' . $is_free . ',
            "data": ' . $finalData . ',
            "saldo": ' . $user->saldo . ',
            "tax_percentage": ' . $user->tax_percentage . '
        }';
    }

    public function postCreate_temp_products()
    {
        $request = request();
        $dataPost = $request->getPost();
        $dataPost['product_image_url'] = upload_file($request);
        $user = cekValidation('/transactions/orders/create_temp_products');
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
        $user = cekValidation('/transactions/orders/create_temp_products2');
        $db = db_connect();

        if ((int)$user->id_user_parent > 0) {
            $builder = $db->table('app_transaction_products_temp_' . $user->id_user_parent);
            $builder0 = $db->table('app_product_' . $user->id_user_parent);
        } else {
            $builder = $db->table('app_transaction_products_temp_' . $user->id_user);
            $builder0 = $db->table('app_product_' . $user->id_user);
        }
        $q = $builder0->whereIn('id_product', ($dataPost->id_product));

        $data = array();
        foreach ($q->get()->getResultArray() as $value) {
            $_data = $value;
            $qty = $builder->where('id_product', $_data['id_product'])->get()->getRow()->product_qty ?? 0;
            $_data['product_qty'] = ((int)$qty > 0) ? (int)$qty + 1 : 1;

            array_push($data, $_data);
        }
        // print_r($data);
        // die;

        // $query = $builder->insertBatch($data);
        $query = $builder->upsertBatch($data);
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
        $user = cekValidation('/transactions/orders/update_temp_products');
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
        $user = cekValidation('/transactions/orders/delete_temp_products');
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
        // header('Content-type: text/html; charset=UTF-8', true);
        // header('Content-type: text/plain', true);
        // header('Content-type: application/json', true);
        $user = cekValidation('/transactions/orders/create');

        if ((int)$user->saldo < 1000) {
            $data = '{
                "code": 99,
                "error": "Saldo anda kurang dari IDR 1.000, silahkan top up terlebih dahulu.",
                "message": "Saldo anda kurang dari IDR 1.000, silahkan top up terlebih dahulu.",
                "reff_id": "",
                "data": [],
                "payment": []
            }';
            return $this->response->setStatusCode(200)->setBody($data);
        }

        $tax_percentage = (int)$user->tax_percentage;
        $request = request();
        $dataPost = $request->getJSON(true);
        if (isset($dataPost['payment_method_code']) && $dataPost['payment_method_code'] === 'QRIS_PAYLATER') {
            $dataPost['invoice_number'] = 'DIGIPAYID-SAMPLE-001';
        } else {
            $dataPost['invoice_number'] = 'DIGIPAYID-' . $user->id_user . '-' . strtoupper(substr(md5(Date('YmdHis')), 5, 8));
        }

        // print_r($dataPost['invoice_number']);
        // $dataPost['invoice_number'] = 'DIGIPAYID-' . $user->id_user . '-' . strtoupper(substr(md5(Date('YmdHis')), 5, 8));
        if (isset($dataPost['customer_id'])) {
            $cust_id = $dataPost['customer_id'];
            unset($dataPost['customer_id']);
        } else {
            $cust_id = '9447e603-0373-4eb7-8a53-e204551c83ec';
        }

        $dataPost['external_id'] = $dataPost['invoice_number'];
        $dataPost['id_user'] = $user->id_user;
        $dataPost['tax_percentage'] = $tax_percentage;
        $dataPost['amount_tax'] = (int)$dataPost['amount_tax'];
        $dataPost['amount'] = (int)$dataPost['amount'];
        $dataPost['amount_to_pay'] = (int)$dataPost['amount_to_pay'];
        $dataPost['amount_to_back'] = (int)$dataPost['amount_to_pay'] - (int)$dataPost['amount'];
        $dataPost['amount_to_receive'] = (int)$dataPost['amount_to_pay'] - (int)$dataPost['amount_to_back'] - (int)$dataPost['fee'] - $dataPost['amount_tax'];
        if (($dataPost['payment_method_code'] === 'CASH')) {
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

        // print_r($dataPost);

        $builder->insert($dataPost);
        $builder1->insertBatch($data);

        sleep(1);

        $object = (object) array();
        $object->req = (object) array("reff_id" => $dataPost['invoice_number'], "amount" => $dataPost['amount_to_pay']);
        $object->image_src = 'qris/QRIS-PAYLATER.PNG';
        $object->res = (object) array("data" => (object) array("total_bayar" => $dataPost['amount_to_pay'], "pembayaran" => $dataPost['payment_method_name'], "payment_method_code" => $dataPost['payment_method_code']));
        $payment = json_encode($object);
        if (getenv('PG') === 'TOKOPAY') {
            $payment = ($dataPost['payment_method_code'] === 'CASH') ? '{}' : json_encode(tokopay_generate_qris((int)$dataPost['amount_to_pay'], $dataPost['payment_method_code'], $dataPost['invoice_number'], $user));

            // return response()->setJSON($payment);
            // die;
        } elseif (getenv('PG') === 'XENDIT') {
            $payLater = 'QRIS_PAYLATER';
            if ($dataPost['payment_method_code'] === 'QRIS' || $dataPost['payment_method_code'] === $payLater) {
                $payment = json_encode(xendit_generate_qris($dataPost['amount_to_pay'], $dataPost['invoice_number'], $dataPost['payment_method_code'], ($dataPost['payment_method_code'] === $payLater ? true : false)));

                // return response()->setJSON($payment);
                // die;
            }
            if ($dataPost['payment_method_code'] === 'ID_AKULAKU' || $dataPost['payment_method_code'] === 'ID_KREDIVO') {
                $payment = (xendit_initiate_paylater($cust_id, $builder->where('invoice_number', $dataPost['invoice_number'])->get()->getRow()));

                // return response()->setJSON($payment);
                // die;
            }
        }

        $paymentJSON = str_replace('"{', '{', str_replace('}"', '}', str_replace('""', '', str_replace('\\', '', json_encode($payment)))));
        $dataPostUpdate['payment_response'] = ($dataPost['payment_method_code'] === 'CASH') ? null : $paymentJSON;
        $builder->where('invoice_number', $dataPost['invoice_number'])->update($dataPostUpdate);

        $journal_insert = array();
        $journal_insert_admin = array();
        $journal_insert_affiliator = array();

        $journal_insert0['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert0['amount_credit'] = $dataPost['amount'];
        $journal_insert0['amount_debet'] = 0;
        $journal_insert0['accounting_type'] = 1;
        $journal_insert0['id_payment_method'] = (int)$dataPost['id_payment_method'];
        $journal_insert0['status'] = ($dataPost['payment_method_code'] !== 'CASH') ? 0 : 2;
        $journal_insert0['description'] = 'Penjualan ' . $dataPost['invoice_number'];
        array_push($journal_insert, $journal_insert0);

        $journal_insert1['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert1['amount_credit'] = 0;
        $journal_insert1['amount_debet'] = (float)$dataPost['app_fee'] + (float)$dataPost['pg_fee'];
        $journal_insert1['accounting_type'] = 101;
        $journal_insert1['id_payment_method'] = (int)$dataPost['id_payment_method'];
        $journal_insert1['status'] = ($dataPost['payment_method_code'] !== 'CASH') ? 0 : 2;
        $journal_insert1['description'] = 'Fee ' . $dataPost['invoice_number'];
        array_push($journal_insert, $journal_insert1);

        if ((float)$dataPost['amount_tax'] > 0) {
            $journal_insert2['invoice_number'] = $dataPost['invoice_number'];
            $journal_insert2['amount_credit'] = 0;
            $journal_insert2['amount_debet'] = (int)$dataPost['amount_tax'];
            $journal_insert2['accounting_type'] = 5;
            $journal_insert2['id_payment_method'] = (int)$dataPost['id_payment_method'];
            $journal_insert2['status'] = ($dataPost['payment_method_code'] !== 'CASH') ? 0 : 2;
            $journal_insert2['description'] = 'Tax ' . $dataPost['invoice_number'];
            array_push($journal_insert, $journal_insert2);
        }


        // $journal_insert_admin0['invoice_number'] = $dataPost['invoice_number'];
        // $journal_insert_admin0['id_user'] = $user->id_user;
        // $journal_insert_admin0['id_user_parent'] = $user->id_user_parent;
        // $journal_insert_admin0['amount_credit'] = (float)$dataPost['app_fee'];
        // $journal_insert_admin0['amount_debet'] = 0;
        // $journal_insert_admin0['accounting_type'] = 101;
        // $journal_insert_admin0['status'] = ($dataPost['payment_method_code'] !== 'CASH') ? 1 : 0;
        // $journal_insert_admin0['description'] = 'Fee App '.$dataPost['invoice_number'].' (Keuntungan)';
        // array_push($journal_insert_admin, $journal_insert_admin0);

        if (($dataPost['payment_method_code'] !== 'CASH')) {
            $journal_insert_admin0['invoice_number'] = $dataPost['invoice_number'];
            $journal_insert_admin0['id_user'] = $user->id_user;
            $journal_insert_admin0['id_user_parent'] = $user->id_user_parent;
            $journal_insert_admin0['amount_credit'] = (float)$dataPost['amount'];
            $journal_insert_admin0['amount_debet'] = 0;
            $journal_insert_admin0['accounting_type'] = 1;
            $journal_insert_admin0['id_payment_method'] = (int)$dataPost['id_payment_method'];
            $journal_insert_admin0['status'] = ($dataPost['payment_method_code'] !== 'CASH') ? 0 : 2;
            $journal_insert_admin0['description'] = 'Penjualan ' . $dataPost['invoice_number'];
            array_push($journal_insert_admin, $journal_insert_admin0);
        }

        $journal_insert_admin1['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert_admin1['id_user'] = $user->id_user;
        $journal_insert_admin1['id_user_parent'] = $user->id_user_parent;
        $journal_insert_admin1['amount_credit'] = (float)$dataPost['app_fee'];
        $journal_insert_admin1['amount_debet'] = 0;
        $journal_insert_admin1['accounting_type'] = 1001;
        $journal_insert_admin1['id_payment_method'] = (int)$dataPost['id_payment_method'];
        $journal_insert_admin1['status'] = ($dataPost['payment_method_code'] !== 'CASH') ? 0 : 2;
        $journal_insert_admin1['description'] = 'Fee App ' . $dataPost['invoice_number'] . ' (Keuntungan)';
        array_push($journal_insert_admin, $journal_insert_admin1);

        $journal_insert_admin1['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert_admin1['id_user'] = $user->id_user;
        $journal_insert_admin1['id_user_parent'] = $user->id_user_parent;
        $journal_insert_admin1['amount_credit'] = 0;
        $journal_insert_admin1['amount_debet'] = (float)$dataPost['app_fee'] * (float)getenv('FEE_AFFILIATOR_PERCENT');
        $journal_insert_admin1['accounting_type'] = 7002;
        $journal_insert_admin1['id_payment_method'] = (int)$dataPost['id_payment_method'];
        $journal_insert_admin1['status'] = ($dataPost['payment_method_code'] !== 'CASH') ? 0 : 2;
        $journal_insert_admin1['description'] = 'Fee Affiliator ' . $dataPost['invoice_number'];
        array_push($journal_insert_admin, $journal_insert_admin1);

        if (($dataPost['payment_method_code'] !== 'CASH')) {
            $journal_insert_admin2['invoice_number'] = $dataPost['invoice_number'];
            $journal_insert_admin2['id_user'] = $user->id_user;
            $journal_insert_admin2['id_user_parent'] = $user->id_user_parent;
            $journal_insert_admin2['amount_credit'] = 0;
            $journal_insert_admin2['amount_debet'] = (float)$dataPost['pg_fee'];
            $journal_insert_admin2['accounting_type'] = 1002;
            $journal_insert_admin2['id_payment_method'] = (int)$dataPost['id_payment_method'];
            $journal_insert_admin2['status'] = ($dataPost['payment_method_code'] !== 'CASH') ? 0 : 2;
            $journal_insert_admin2['description'] = 'Fee PG ' . $dataPost['invoice_number'];
            array_push($journal_insert_admin, $journal_insert_admin2);
        }

        if ((float)$dataPost['amount_tax'] > 0) {
            $journal_insert_admin3['invoice_number'] = $dataPost['invoice_number'];
            $journal_insert_admin3['id_user'] = $user->id_user;
            $journal_insert_admin3['id_user_parent'] = $user->id_user_parent;
            $journal_insert_admin3['amount_credit'] = 0;
            $journal_insert_admin3['amount_debet'] = (float)$dataPost['amount_tax'];
            $journal_insert_admin3['accounting_type'] = 5;
            $journal_insert_admin3['id_payment_method'] = (int)$dataPost['id_payment_method'];
            $journal_insert_admin3['status'] = ($dataPost['payment_method_code'] !== 'CASH') ? 0 : 2;
            $journal_insert_admin3['description'] = 'Tax ' . $dataPost['invoice_number'];
            array_push($journal_insert_admin, $journal_insert_admin3);
        }

        // $journal_insert_affiliator0['invoice_number'] = $dataPost['invoice_number'];
        // $journal_insert_affiliator0['amount_credit'] = $dataPost['amount'];
        // $journal_insert_affiliator0['amount_debet'] = 0;
        // $journal_insert_affiliator0['accounting_type'] = 1;
        // $journal_insert_affiliator0['id_payment_method'] = (int)$dataPost['id_payment_method'];
        // $journal_insert_affiliator0['status'] = ($dataPost['payment_method_code'] !== 'CASH') ? 0 : 2;
        // $journal_insert_affiliator0['description'] = 'Penjualan ' . $dataPost['invoice_number'];
        // array_push($journal_insert_affiliator, $journal_insert_affiliator0);

        $journal_insert_affiliator0['invoice_number'] = $dataPost['invoice_number'];
        $journal_insert_affiliator0['amount_credit'] = (float)$dataPost['app_fee'] * (float)getenv('FEE_AFFILIATOR_PERCENT');
        $journal_insert_affiliator0['amount_debet'] = 0;
        $journal_insert_affiliator0['accounting_type'] = 7001;
        $journal_insert_affiliator0['id_payment_method'] = (int)$dataPost['id_payment_method'];
        $journal_insert_affiliator0['status'] = ($dataPost['payment_method_code'] !== 'CASH') ? 0 : 2;
        $journal_insert_affiliator0['description'] = 'Fee Transaksi ' . $dataPost['invoice_number'];
        array_push($journal_insert_affiliator, $journal_insert_affiliator0);



        $builder2->insertBatch($journal_insert);
        $db->table('admin_journal_finance')->insertBatch($journal_insert_admin);

        $id_user_affiliator = $db->table('app_users')
            ->where('reff_code', $user->reff_code)->where('is_active', 1)->where('is_verified', 1)
            ->where('user_role', 3)->where('user_privilege', 8)
            ->get()->getRow();

        if ($id_user_affiliator) {
            $tbl_affiliator = "app_journal_finance_" . $id_user_affiliator->id_user;
            $db->table($tbl_affiliator)->insertBatch($journal_insert_affiliator);
        }


        // if ((int)$user->id_user_parent > 0) {
        //     $db->table('app_transaction_products_temp_'.$user->id_user_parent)->truncate();
        // } else {
        //     $db->table('app_transaction_products_temp_'.$user->id_user)->truncate();
        //     //app_journal_finance_40
        // }
        // }


        $dataFinal = $builder0->get()->getResult();
        $db->close();
        $finalData = json_encode($dataFinal);

        $paymentArr = json_decode($paymentJSON);
        if (isset($paymentArr->res->data->ovo_push)) {
            $paymentArr->res->data->ovo_push = urlShortener($paymentArr->res->data->ovo_push);
        }
        if (isset($paymentArr->res->data->checkout_url)) {
            $paymentArr->res->data->checkout_url = urlShortener($paymentArr->res->data->checkout_url);
        }
        if (isset($paymentArr->res->data->pay_url)) {
            $paymentArr->res->data->pay_url = urlShortener($paymentArr->res->data->pay_url);
        }
        if (isset($paymentArr->res->data->paylater_url)) {
            $paymentArr->res->data->paylater_url = urlShortener($paymentArr->res->data->paylater_url);
        }
        $paymentJSON = json_encode($paymentArr);


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
        $id_user = (string)((int)$user->id_user_parent > 0) ? $user->id_user_parent : $user->id_user;
        $insert_queue = array(
            "id_user" => $id_user,
            "invoice_number" => $dataPost['invoice_number'],
            "amount" => $dataPost['amount_to_pay'],
            "table_name_trx" => 'app_transactions_' . $id_user,
        );
        // print_r($insert_queue);
        $db->table('app_qris_paylater_list')->insert($insert_queue);

        // ob_start();
        // header('Content-type: text/html; charset=UTF-8', true);
        // header('Content-type: text/plain', true);
        // header('Content-type: application/json', true);
        // header("Connection: Keep-Alive", true);
        // header('Content-Encoding: none\r\n');

        // $code = ($dataPost['id_payment_method'] === 0) ? 0 : 1;
        // echo '{
        //     "code": ' . $code . ',
        //     "error": "",
        //     "message": "",
        //     "data": ' . $finalData . ',
        //     "payment": ' . $paymentJSON . '
        // }';


        // $size = ob_get_length();
        // header("Content-Length: " . $size . "\r\n");
        // // send info immediately and close connection
        // ob_end_flush();
        // ob_flush();
        // flush();

        // fastcgi_finish_request();

        // ob_start();
        // ob_flush();

        $code = (isset($dataPost['payment']) && $dataPost['payment'] === 0) ? 0 : 1;

        // ob_end_clean();
        // // header("Connection: close");
        // ignore_user_abort(true);
        // ob_start();
        echo '{
    "code": ' . $code . ',
    "error": "",
    "message": "",
    "reff_id": "' . $dataPost['invoice_number'] . '",
    "data": ' . $finalData . ',
    "payment": ' . $paymentJSON . '
}';

        if ((int)$user->id_user_parent > 0) {
            $db->table('app_transaction_products_temp_' . $user->id_user_parent)->truncate();
        } else {
            $db->table('app_transaction_products_temp_' . $user->id_user)->truncate();
            //app_journal_finance_40
        }
        // session_write_close(); //close session file on server side to avoid blocking other requests

        // header("Content-Encoding: none"); //send header to avoid the browser side to take content as gzip format
        // header("Content-Length: " . ob_get_length()); //send length header
        // header("Connection: close"); //or redirect to some url: header('Location: http://www.google.com');
        // // $size = ob_get_length();
        // // header("Content-Length: $size");
        // ob_end_flush(); // All output buffers must be flushed here  // Strange behaviour, will not work
        // flush(); // Unless both are called !

        // // fastcgi_finish_request();
        // // if (function_exists('fastcgi_finish_request')) {
        // //     fastcgi_finish_request();
        // // } else {
        // //     // echo '<p style="color: red;">This server does not support <code>fastcgi_finish_request()</code> function.</p>' . PHP_EOL;
        // //     // echo 'Exit now.<br>' . PHP_EOL;
        // //     exit();
        // // }

        // // sleep(30);

        // ob_start();
        if (($dataPost['payment_method_code'] === 'CASH')) {
            if (($dataPost['email_customer'] != '')) {
                sendReceipt('email', $dataPost, $builder->where('invoice_number', $dataPost['invoice_number'])->orderBy('id_transaction', 'DESC')->get()->getRow(), $builder1->where('invoice_number', $dataPost['invoice_number'])->get()->getResult(), $user, json_decode($paymentJSON));
            }

            if (($dataPost['wa_customer'] != '')) {
                sendReceipt('whatsapp', $dataPost, $builder->where('invoice_number', $dataPost['invoice_number'])->orderBy('id_transaction', 'DESC')->get()->getRow(), $builder1->where('invoice_number', $dataPost['invoice_number'])->get()->getResult(), $user, json_decode($paymentJSON));
            }
        } else {
            if (($dataPost['email_customer'] != '')) {
                sendBilling('email', $dataPost, $builder->where('invoice_number', $dataPost['invoice_number'])->orderBy('id_transaction', 'DESC')->get()->getRow(), $builder1->where('invoice_number', $dataPost['invoice_number'])->get()->getResult(), $user, json_decode($paymentJSON));
            }

            if (($dataPost['wa_customer'] != '')) {
                sendBilling('whatsapp', $dataPost, $builder->where('invoice_number', $dataPost['invoice_number'])->orderBy('id_transaction', 'DESC')->get()->getRow(), $builder1->where('invoice_number', $dataPost['invoice_number'])->get()->getResult(), $user, json_decode($paymentJSON));
            }
        }
        // ob_end_clean();

        // ob_end_flush();
        // ob_flush();
        // flush();
        // ob_end_clean();
    }

    public function postCheck_status()
    {
        $user = cekValidation('/transactions/orders/check_status');
        $request = request();
        $dataPost = $request->getJSON(true);

        $db = db_connect();

        if ((int)$user->id_user_parent > 0) {
            $trx = $db->table('app_transactions_' . $user->id_user_parent);
        } else {
            $trx = $db->table('app_transactions_' . $user->id_user);
        }

        if (isset($dataPost['payment_method_code']) && $dataPost['payment_method_code'] === 'QRIS_PAYLATER') {
            $res = $trx->where('invoice_number', $dataPost['invoice_number'])
                ->where('amount_to_pay', $dataPost['amount'])
                ->where('payment_method_code', $dataPost['payment_method_code'])
                ->get()->getRow();
        } else {
            $res = $trx->where('invoice_number', $dataPost['invoice_number'])
                ->get()->getRow();
        }

        $db->close();

        echo '{
            "code": 1,
            "error": "",
            "message": "",
            "data": [],
            "status": ' . (int)$res->status_payment . '
        }';
    }

    public function postResend_billing()
    {
        // header('Content-type: text/html; charset=UTF-8', true);
        // header('Content-type: text/plain', true);
        // header('Content-type: application/json', true);
        $user = cekValidation('/transactions/orders/resend_billing');
        $request = request();
        $dataPost = $request->getJSON(true);

        $db = db_connect();

        if ((int)$user->id_user_parent > 0) {
            $trx = $db->table('app_transactions_' . $user->id_user_parent)->where('invoice_number', $dataPost['invoice_number'])->get()->getRow();
            $products = $db->table('app_transaction_products_' . $user->id_user_parent)->where('invoice_number', $dataPost['invoice_number'])->get()->getResult();
        } else {
            $trx = $db->table('app_transactions_' . $user->id_user)->where('invoice_number', $dataPost['invoice_number'])->get()->getRow();
            $products = $db->table('app_transaction_products_' . $user->id_user)->where('invoice_number', $dataPost['invoice_number'])->get()->getResult();
        }

        // $dataPost['email_customer'] = $trx->email_customer;
        // $dataPost['wa_customer'] = $trx->wa_customer;

        $db->close();


        if (getenv('PG') === 'TOKOPAY') {
            $payment = ($trx->payment_method_code === 'CASH') ? '{}' : json_encode(tokopay_generate_qris((int)$trx->amount_to_pay, $trx->payment_method_code, $dataPost['invoice_number'], $user));
        } else if (getenv('PG') === 'XENDIT') {
            $payment = ($trx->payment_response);
        } else {
            $payment = '{}';
        }

        $paymentJSON = str_replace('"{', '{', str_replace('}"', '}', str_replace('""', '', str_replace('\\', '', json_encode($payment)))));

        $paymentArr = json_decode($paymentJSON);
        if (isset($paymentArr->res->data->ovo_push)) {
            $paymentArr->res->data->ovo_push = urlShortener($paymentArr->res->data->ovo_push);
        } else if (isset($paymentArr->res->data->checkout_url)) {
            $paymentArr->res->data->checkout_url = urlShortener($paymentArr->res->data->checkout_url);
        } else if (isset($paymentArr->res->data->pay_url)) {
            $paymentArr->res->data->pay_url = urlShortener($paymentArr->res->data->pay_url);
        } else if (isset($paymentArr->res->data->paylater_url)) {
            $paymentArr->res->data->paylater_url = urlShortener($paymentArr->res->data->paylater_url);
        } else if ($trx->payment_method_code === 'QRIS_PAYLATER') {
            $paymentArr = (object) array();
            $paymentArr->data = ["payment_method_code" => $trx->payment_method_code];
            $paymentArr->res = (object) array("data" => (object) array("amount" => $trx->amount, "pembayaran" => "QRIS", "payment_method_code" => $trx->payment_method_code, "qr_link" => getDomain() . '/qris/QRIS-PAYLATER.PNG'));
        } else if ($trx->payment_method_code === 'QRIS') {
            $paymentArr = (object) array();
            $paymentArr->data = ["payment_method_code" => $trx->payment_method_code];
            $paymentArr->res = (object) array("data" => (object) array("amount" => $trx->amount, "pembayaran" => "QRIS", "payment_method_code" => $trx->payment_method_code, "qr_link" => getDomain() . '/qris/QRIS-' . $dataPost['invoice_number'] . '.png'));
        }
        $paymentJSON = json_encode($paymentArr);

        $code = ($trx->payment_method_code === 'CASH') ? 0 : 1;

        // ob_end_clean();
        // // header("Connection: close");
        // ignore_user_abort(true);
        // ob_start();

        echo '{
    "code": ' . $code . ',
    "error": "",
    "message": "",
    "data": [],
    "payment": ' . $paymentJSON . '
}';

        // session_write_close(); //close session file on server side to avoid blocking other requests

        // header("Content-Encoding: none"); //send header to avoid the browser side to take content as gzip format
        // header("Content-Length: " . ob_get_length()); //send length header
        // header("Connection: close"); //or redirect to some url: header('Location: http://www.google.com');
        // // $size = ob_get_length();
        // // header("Content-Length: $size");
        // ob_end_flush(); // All output buffers must be flushed here  // Strange behaviour, will not work
        // flush(); // Unless both are called !

        // // fastcgi_finish_request();
        // // if (function_exists('fastcgi_finish_request')) {
        // //     fastcgi_finish_request();
        // // } else {
        // //     // echo '<p style="color: red;">This server does not support <code>fastcgi_finish_request()</code> function.</p>' . PHP_EOL;
        // //     // echo 'Exit now.<br>' . PHP_EOL;
        // //     exit();
        // // }

        // // sleep(30);

        // ob_start();
        if ($trx->payment_method_code === 'CASH') {
            if (isset($dataPost['email_customer']) && ($dataPost['email_customer'] != '')) {
                sendReceipt('email', $dataPost, $trx, $products, $user, json_decode($paymentJSON));
            }

            if (isset($dataPost['wa_customer']) && ($dataPost['wa_customer'] != '')) {
                sendReceipt('whatsapp', $dataPost, $trx, $products, $user, json_decode($paymentJSON));
            }
        } else {
            if (isset($dataPost['email_customer']) && ($dataPost['email_customer'] != '')) {
                sendBilling('email', $dataPost, $trx, $products, $user, json_decode($paymentJSON));
            }

            if (isset($dataPost['wa_customer']) && ($dataPost['wa_customer'] != '')) {
                sendBilling('whatsapp', $dataPost, $trx, $products, $user, json_decode($paymentJSON));
            }
        }
        // ob_end_clean();

        // ob_end_flush();
        // ob_flush();
        // flush();
        // ob_end_clean();
    }



    public function postUpdate()
    {
        $user = cekValidation('/transactions/orders/update');
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
