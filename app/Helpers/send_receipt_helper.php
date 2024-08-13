<?php

date_default_timezone_set("Asia/Bangkok");
//https://pdfcrowd.com/api/html-to-image-php/
// require "pdfcrowd.php";
// require_once __DIR__ . '/../../vendor/autoload.php';

function getDomainName()
{
    return env('API_DOMAIN_BASE_URL0');
}


function sendReceipt($type, $dataPost, $transaction, $dataProducts, $user, $payment)
{
    $products = '';
    foreach ($dataProducts as $product) {
        $products .= '<tr style="border: 1px solid black;border-collapse: collapse;">
                                <td style="padding:10px;font-size:14px;text-align:left;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;text-align:left;">' . $product->product_code . '</span></td>
                                <td style="padding:10px;font-size:14px;text-align:left;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;text-align:left;">' . $product->product_name . '</span></td>
                                <td style="padding:10px;font-size:14px;text-align:right;border: 1px solid black;border-collapse: collapse;">
                                    <p style="text-align:right;">' . format_rupiah($product->product_qty) . '</p>
                                </td>
                                <td style="padding:10px;font-size:14px;text-align:right;border: 1px solid black;border-collapse: collapse;">
                                    <p style="text-align:right;"><span style="font-size:14px;">' . format_rupiah($product->product_price) . '</span></p>
                                </td>
                                <td style="padding:10px;font-size:14px;text-align:right;border: 1px solid black;border-collapse: collapse;">
                                    <p style="text-align:right;"><span style="font-size:14px;">' . format_rupiah((int)$product->product_price * (int)$product->product_qty) . '</span></p>
                                </td>
                                <td style="padding:10px;font-size:14px;text-align:center;border: 1px solid black;border-collapse: collapse;">
                                    <p style="text-align:center;"><img src="' . getDomainName() . $product->product_image_url . '" height="20px"></p>
                                </td>
                            </tr>';
    }
    $htmlBody = '
            <div align="center" style="width: 750px; background: #f5f5f5; padding: 30px;">
            <br/>
                <h3 style="text-align:center;"><b>DIGIPAYID</b></h3>
                <hr>
                <p style="text-align:center;"><span style="font-size:12px;">Struk Pembayaran</span></p>
                <p style="text-align:center;"><span style="font-size:12px;font-weight:bold;">' . $user->merchant_name . '</span></p>
                <p style="text-align:center;"><span style="font-size:12px;">WA :' . $user->merchant_wa . '</span></p>
                <p style="text-align:center;"><span style="font-size:12px;">' . $user->merchant_address . '</span></p>
                <hr>
                <p style="text-align:center;"><span style="font-size:14px;"><strong>' . $dataPost['invoice_number'] . '</strong></span></p>
                <p style="text-align:center;"><u>' . date('l, d F Y H:i', strtotime($transaction->time_transaction)) . '</u></p>
                <p>&nbsp;</p>
                <div align="center" style="width:50%;text-align:center;">
                        <table class="ck-table-resized" style="width:100%;border: 1px solid black;border-collapse: collapse;">
                            <tbody>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Status</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px"><strong>' . (((int)$transaction->status_transaction === 1) ? 'LUNAS' : 'BELUM LUNAS') . '</strong></span></td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Jumlah Produk</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">' . $transaction->total_product . ' (Pcs)</span></td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Sub Total</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">
                                        <strong>IDR ' . format_rupiah((int)$transaction->amount - (int)$transaction->amount_tax - (int)$transaction->fee) . '</strong></span>
                                    </td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Pajak</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">
                                        IDR ' . format_rupiah($transaction->amount_tax) . '</span>
                                    </td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Biaya Penanganan</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">
                                        IDR ' . (((int)$transaction->fee_on_merchant === 0) ? format_rupiah($transaction->fee) : 0) . '</span>
                                    </span></td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Tagihan</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">
                                        <strong>IDR ' . format_rupiah($transaction->amount) . '</strong></span>
                                    </td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Dibayar</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">
                                        IDR ' . format_rupiah($transaction->amount_to_pay) . '</span>
                                    </span></td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Kembalian</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">
                                        IDR ' . format_rupiah($transaction->amount_to_back) . '
                                    </span></td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Metode Bayar</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">' . $transaction->payment_method_name . '</span></td>
                                </tr>
                            </tbody>
                        </table>
                </div>
                <hr>
                <p style="text-align:center;font-size:16px;"><strong>Detail Pembelian</strong></p>
                
                        <table class="ck-table-resized" style="width:100%;border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <th style="padding:10px;font-size:14px;text-align:left;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px; text-align:left;">Kode Produk</span></th>
                                    <th style="padding:10px;font-size:14px;text-align:left;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px; text-align:left;">Nama Produk</span></th>
                                    <th style="padding:10px;font-size:14px;text-align:right;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px; text-align:right;">Jumlah</span></th>
                                    <th style="padding:10px;font-size:14px;text-align:right;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px; text-align:right;">Harga</span></th>
                                    <th style="padding:10px;font-size:14px;text-align:right;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px; text-align:right;">Sub Total</span></th>
                                    <th style="padding:10px;font-size:14px;text-align:center;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px; text-align:center;">Gambar</span></th>
                                </tr>
                            </thead>
                            <tbody>
                            ' . $products . '
                            </tbody>
                        </table>
                </div>
            </div>
            ';

    $urlIMG = "receipts/" . $dataPost['invoice_number'] . ".png";

    $img = htmlToImage($htmlBody);

    // file_put_contents($urlIMG, file_get_contents($img));
    grab_image($img, $urlIMG);

    // die();

    $db = db_connect();
    $update['url_file_receipt'] = $img;

    if ((int)$user->id_user_parent > 0) {
        $db->table('app_transactions_' . $user->id_user_parent)->where('invoice_number', $dataPost['invoice_number'])->update($update);
    } else {
        $db->table('app_transactions_' . $user->id_user)->where('invoice_number', $dataPost['invoice_number'])->update($update);
    }

    if ($type === 'email') {
        // sendMail($dataPost['email_customer'], 'Struk Pembayaran - ' . $dataPost['invoice_number'], $htmlBody, $urlIMG);

        $id_user = (int)$user->id_user;
        if ((int)$user->id_user_parent > 0) {
            $id_user = (int)$user->id_user_parent;
        }
        $db->table('app_notifications')->insert([
            'id_user' => $id_user,
            'type' => 1,
            'destination' => $dataPost['email_customer'],
            'subject' => 'Struk pembayaran - ' . $dataPost['invoice_number'],
            'text_message' => $htmlBody,
            'attachment_url' => $urlIMG,
        ]);
    }

    if ($type === 'whatsapp') {
        $file =  $img;
        // if (isset($payment->res->data->qr_link)) {
        //     $file = $payment->res->data->qr_link;
        // } else {
        //     $file =  $img;
        //     // $file =  urlShortener($img) . '?file=' . substr(md5(Date('YmdHis')), 5, 10) . '.png';
        // }

        $message = '
*Bukti Bayar - ' . $dataPost['invoice_number'] . '*';
        // sendWhatsapp($dataPost['wa_customer'], $message, $file);

        $id_user = (int)$user->id_user;
        if ((int)$user->id_user_parent > 0) {
            $id_user = (int)$user->id_user_parent;
        }
        $db->table('app_notifications')->insert([
            'id_user' => $id_user,
            'type' => 2,
            'destination' => $dataPost['wa_customer'],
            'text_message' => $message,
            'attachment_url' => $file,
        ]);
    }

    normalize_notification($id_user);

    $db->close();
}

function sendBilling($type, $dataPost, $transaction, $dataProducts, $user, $payment)
{
    // $mpdf = new \Mpdf\Mpdf();
    $products = '';
    foreach ($dataProducts as $product) {
        $products .= '<tr style="border: 1px solid black;border-collapse: collapse;">
                                <td style="padding:10px;font-size:14px;text-align:left;"><span style="font-size:14px;text-align:left;">' . $product->product_code . '</span></td>
                                <td style="padding:10px;font-size:14px;text-align:left;"><span style="font-size:14px;text-align:left;">' . $product->product_name . '</span></td>
                                <td style="padding:10px;font-size:14px;text-align:right;">
                                    <p style="text-align:right;">' . format_rupiah($product->product_qty) . '</p>
                                </td>
                                <td style="padding:10px;font-size:14px;text-align:right;">
                                    <p style="text-align:right;"><span style="font-size:14px;">' . format_rupiah($product->product_price) . '</span></p>
                                </td>
                                <td style="padding:10px;font-size:14px;text-align:right;">
                                    <p style="text-align:right;"><span style="font-size:14px;">' . format_rupiah((int)$product->product_price * (int)$product->product_qty) . '</span></p>
                                </td>
                                <td style="padding:10px;font-size:14px;text-align:center;">
                                    <p style="text-align:center;"><img src="' . getDomainName() . $product->product_image_url . '" height="20px"></p>
                                </td>
                            </tr>';
    }

    $detailPayment = '';
    if (isset($payment->res->data->no_va)) {
        $detailPayment = '
        <tr style="border: 1px solid black;border-collapse: collapse;">
            <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Nomor VA / Transaksi</span></td>
            <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">' . $payment->res->data->no_va . '</span></td>
        </tr>';
    } elseif (isset($payment->res->data->pay_url)) {
        $detailPayment = '
        <tr style="border: 1px solid black;border-collapse: collapse;">
            <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Cara Bayar</span></td>
            <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><a href="' . $payment->res->data->pay_url . '" target="_blank"><button style="border: none;border-radius: 5px;color: white;padding: 5px 10px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;cursor: pointer;width: 100px;background-image: linear-gradient(98deg, #6ACDFF, #16B1FF 94%);">Bayar Sekarang</button></a></td>
        </tr>';
    } elseif (isset($payment->res->data->qr_link)) {
        $detailPayment = '
        <tr style="border: 1px solid black;border-collapse: collapse;">
            <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">QRIS</span></td>
            <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><img src="' . $payment->res->data->qr_link . '"></td>
        </tr>';
    }

    $htmlBody = '
            <div align="center" style="width: 750px; background: #f5f5f5; padding: 30px;">
            <br/>
                <h3 style="text-align:center;"><b>DIGIPAYID</b></h3>
                <hr>
                <p style="text-align:center;"><span style="font-size:12px;">Detail Tagihan</span></p>
                <p style="text-align:center;"><span style="font-size:12px;font-weight:bold;">' . $user->merchant_name . '</span></p>
                <p style="text-align:center;"><span style="font-size:12px;">WA :' . $user->merchant_wa . '</span></p>
                <p style="text-align:center;"><span style="font-size:12px;">' . $user->merchant_address . '</span></p>
                <hr>
                <p style="text-align:center;"><span style="font-size:14px;"><strong>' . $dataPost['invoice_number'] . '</strong></span></p>
                <p style="text-align:center;"><u>' . date('l, d F Y H:i', strtotime($transaction->time_transaction)) . '</u></p>
                <p>&nbsp;</p>
                <div align="center" style="width:50%;text-align:center;">
                        <table class="ck-table-resized" style="width:100%;border: 1px solid black;border-collapse: collapse;">
                            <tbody>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Status</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px"><strong>' . (((int)$transaction->status_transaction === 1) ? 'LUNAS' : 'BELUM LUNAS') . '</strong></span></td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Jumlah Produk</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">' . $transaction->total_product . ' (Pcs)</span></td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Sub Total</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">
                                        <strong>IDR ' . format_rupiah((int)$transaction->amount - (int)$transaction->fee - (int)$transaction->amount_tax) . '</strong></span>
                                    </td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Pajak</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">
                                        IDR ' . format_rupiah($transaction->amount_tax) . '</span>
                                    </td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Biaya Penanganan</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">
                                        IDR ' . (((int)$transaction->fee_on_merchant === 0) ? format_rupiah($transaction->fee) : 0) . '</span>
                                    </span></td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Tagihan</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">
                                        <strong>IDR ' . format_rupiah($transaction->amount) . '</strong></span>
                                    </td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Dibayar</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">
                                        IDR ' . format_rupiah($transaction->amount_to_pay) . '</span>
                                    </span></td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Kembalian</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">
                                        IDR ' . format_rupiah($transaction->amount_to_back) . '
                                    </span></td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Metode Bayar</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">' . $transaction->payment_method_name . '</span></td>
                                </tr>
                            </tbody>
                        </table>
                </div>
                <hr>
                <p style="text-align:center;font-size:16px;"><strong>Detail Pembelian</strong></p>
                
                <div align="center" style="width:100%;text-align:center;">
                        <table class="ck-table-resized" style="width:100%;border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <th style="padding:10px;font-size:14px;text-align:left;"><span style="font-size:14px; text-align:left;">Kode Produk</span></th>
                                    <th style="padding:10px;font-size:14px;text-align:left;"><span style="font-size:14px; text-align:left;">Nama Produk</span></th>
                                    <th style="padding:10px;font-size:14px;text-align:right;"><span style="font-size:14px; text-align:right;">Jumlah</span></th>
                                    <th style="padding:10px;font-size:14px;text-align:right;"><span style="font-size:14px; text-align:right;">Harga</span></th>
                                    <th style="padding:10px;font-size:14px;text-align:right;"><span style="font-size:14px; text-align:right;">Sub Total</span></th>
                                    <th style="padding:10px;font-size:14px;text-align:center;"><span style="font-size:14px; text-align:center;">Gambar</span></th>
                                </tr>
                            </thead>
                            <tbody>
                            ' . $products . '
                            </tbody>
                        </table>
                </div>
            </div>
            ';

    $urlIMG = "billings/" . $dataPost['invoice_number'] . ".png";

    $img = htmlToImage($htmlBody);

    // file_put_contents($urlIMG, file_get_contents($img));
    grab_image($img, $urlIMG);

    $db = db_connect();
    $update['url_file_billing'] = $img;

    if ((int)$user->id_user_parent > 0) {
        $db->table('app_transactions_' . $user->id_user_parent)->where('invoice_number', $dataPost['invoice_number'])->update($update);
    } else {
        $db->table('app_transactions_' . $user->id_user)->where('invoice_number', $dataPost['invoice_number'])->update($update);
    }

    if ($type === 'email') {
        // sendMail($dataPost['email_customer'], 'DIGIPAYID TAGIHAN - ' . $dataPost['invoice_number'], $htmlBody, $urlIMG);

        $id_user = (int)$user->id_user;
        if ((int)$user->id_user_parent > 0) {
            $id_user = (int)$user->id_user_parent;
        }
        $db->table('app_notifications')->insert([
            'id_user' => $id_user,
            'type' => 1,
            'destination' => $dataPost['email_customer'],
            'subject' => 'DIGIPAYID TAGIHAN - ' . $dataPost['invoice_number'],
            'text_message' => $htmlBody,
            'attachment_url' => $urlIMG,
        ]);
    }

    if ($type === 'whatsapp') {

        $caraBayar = 'Scan QRIS';
        if (isset($payment->res->data->qr_link)) {
            $caraBayar = 'Scan QRIS';
        } elseif (isset($payment->res->data->no_va)) {
            $caraBayar = $payment->res->data->no_va;
        } elseif (isset($payment->res->data->ovo_push)) {
            $caraBayar = ($payment->res->data->ovo_push);
        } elseif (isset($payment->res->data->checkout_url)) {
            $caraBayar = ($payment->res->data->checkout_url);
        } elseif (isset($payment->res->data->pay_url)) {
            $caraBayar = ($payment->res->data->pay_url);
        }

        if (isset($payment->res->data->qr_link)) {
            $file = $payment->res->data->qr_link;
        } else {
            $file =  $img;
            // $file =  urlShortener($img) . '?file=' . substr(md5(Date('YmdHis')), 5, 10) . '.png';
        }

        $message = '*TAGIHAN ' . $dataPost['invoice_number'] . '*

Total Bayar : *IDR ' . format_rupiah($transaction->amount_to_pay) . '*
Metode Bayar : *' . ($transaction->payment_method_name) . '*
Link Bayar : *' . $caraBayar . '*';
        // sendWhatsapp($dataPost['wa_customer'], $message, $file);

        $id_user = (int)$user->id_user;
        if ((int)$user->id_user_parent > 0) {
            $id_user = (int)$user->id_user_parent;
        }
        $db->table('app_notifications')->insert([
            'id_user' => $id_user,
            'type' => 2,
            'destination' => $dataPost['wa_customer'],
            'text_message' => $message,
            'attachment_url' => $file,
        ]);
    }

    normalize_notification($id_user);

    $db->close();
}


function sendReceiptTopup($type, $invoice_number, $dataJournal, $amountDebet, $user, $dt)
{
    $htmlBody = '
            <div align="center" style="width: 750px; background: #f5f5f5; padding: 30px;">
            <br/>
                <h3 style="text-align:center;"><b>DIGIPAYID</b></h3>
                <hr>
                <p style="text-align:center;"><span style="font-size:12px;">Struk Topup Merchant</span></p>
                <p style="text-align:center;"><span style="font-size:12px;font-weight:bold;">' . $user->merchant_name . '</span></p>
                <p style="text-align:center;"><span style="font-size:12px;">WA :' . $user->merchant_wa . '</span></p>
                <p style="text-align:center;"><span style="font-size:12px;">' . $user->merchant_address . '</span></p>
                <hr>
                <p style="text-align:center;"><span style="font-size:14px;"><strong>' . $invoice_number . '</strong></span></p>
                <p style="text-align:center;"><u>' . date('l, d F Y H:i', strtotime($dataJournal->created_at)) . '</u></p>
                <p>&nbsp;</p>
                <div align="center" style="width:50%;text-align:center;">
                        <table class="ck-table-resized" style="width:100%;border: 1px solid black;border-collapse: collapse;">
                            <tbody>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Status</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px"><strong>' . 'LUNAS' . '</strong></span></td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Total</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">
                                        <strong>IDR ' . format_rupiah((int)$dt['data']['total_dibayar']) . '</strong></span>
                                    </td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Biaya Penanganan</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">
                                        IDR -' . format_rupiah($amountDebet) . '</span>
                                    </span></td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Uang Diterima</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">
                                        <strong>IDR ' . format_rupiah((int)$dt['data']['total_dibayar'] - (int)$amountDebet) . '</strong></span>
                                    </td>
                                </tr>
                                <tr style="border: 1px solid black;border-collapse: collapse;">
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;">Metode Bayar</span></td>
                                    <td style="padding:10px;border: 1px solid black;border-collapse: collapse;"><span style="font-size:14px;padding-left:30px">' . $dt['data']['payment_channel'] . '</span></td>
                                </tr>
                            </tbody>
                        </table>
                </div>
                </div>
            </div>
            ';

    $urlIMG = "receipts/" . $invoice_number . ".png";

    $img = htmlToImage($htmlBody);

    // file_put_contents($urlIMG, file_get_contents($img));
    grab_image($img, $urlIMG);

    // die();

    $db = db_connect();
    if ($type === 'email') {
        // sendEmail($user->email, 'Struk Topup Saldo - ' . $invoice_number, $htmlBody, $urlIMG);

        $id_user = (int)$user->id_user;
        if ((int)$user->id_user_parent > 0) {
            $id_user = (int)$user->id_user_parent;
        }
        $db->table('app_notifications')->insert([
            'id_user' => $id_user,
            'type' => 1,
            'destination' => $user->email,
            'subject' => 'DIGIPAYID Struk Topup Saldo - ' . $invoice_number,
            'text_message' => $htmlBody,
            'attachment_url' => $urlIMG,
        ]);
    }

    if ($type === 'whatsapp') {
        $file =  $img;
        // if (isset($payment->res->data->qr_link)) {
        //     $file = $payment->res->data->qr_link;
        // } else {
        //     $file =  $img;
        //     // $file =  urlShortener($img) . '?file=' . substr(md5(Date('YmdHis')), 5, 10) . '.png';
        // }

        $message = '
*Bukti Bayar - ' . $invoice_number . '*';
        // sendWhatsapp($user->merchant_wa, $message, $file);

        $id_user = (int)$user->id_user;
        if ((int)$user->id_user_parent > 0) {
            $id_user = (int)$user->id_user_parent;
        }
        $db->table('app_notifications')->insert([
            'id_user' => $id_user,
            'type' => 2,
            'destination' => $user->merchant_wa,
            'text_message' => $message,
            'attachment_url' => $file,
        ]);
    }

    normalize_notification($id_user);

    $db->close();
}
