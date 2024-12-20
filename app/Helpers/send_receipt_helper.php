<?php

date_default_timezone_set("Asia/Bangkok");
//https://pdfcrowd.com/api/html-to-image-php/
// require "pdfcrowd.php";
// require_once __DIR__ . '/../../vendor/autoload.php';

function getDomainName()
{
  return env('API_DOMAIN_BASE_URL0');
}


function sendReceipt($type, $dataPost, $transaction, $dataProducts, $user, $payment, $nama)
{
  $products = '';
  foreach ($dataProducts as $product) {
    $products .= '<tr>
                <td>' . $product->product_name . ' x <span>' . format_rupiah($product->product_qty) . '</span></td>
                <td style="text-align: right">Rp. <span>' . format_rupiah($product->product_price) . '</span></td>
            </tr>
            <tr>
                <td colspan="2" style="color: grey; font-size: 10px">' . $product->product_desc . '</td>
            </tr>
            <tr>
                <td colspan="2" style="font-size: 11px">*Req : ' . $product->product_custom_request . '</td>
            </tr>';
  }
  
  $wifi = '';
  if (isset($user->wifi_password) && ($user->wifi_password != null) && ($user->wifi_password != '')) {
      $wifi = 'Wifi : ' . $user->wifi_name.' - Pass : ' . $user->wifi_password;
      $wifi = '<p style="text-align: center">
              <strong><span>' . $wifi . '</span></strong>
            </p>';
  }

  $htmlBody = '
  
  <style>
    @page {
      size: 490px 3000px;
      margin: .5in;
    }
    #bgimg {
      position: fixed;
      left: -.5in;
      top: -.5in;
      width: 500px;
      height: 3000px;
      z-index: -999
    }
  </style>

  <div id="bgimg" style="font-family: Arial, sans-serif;color: #333;margin: 0;padding: 0;">
      <div style="width: 450px;background-color: #f6f6f6;border: 1px solid #ddd;padding: 20px;border-radius: 10px;">
        <div style="text-align: center;background-color: #6f42c1;color: #fff;padding: 20px;border-radius: 10px 10px 0 0;">
          <h3 style="margin: 0;font-size: 30px"><b>DIGIPAYID</b></h3>
          <p style="margin: 0">Struk Pembayaran - '.$transaction->nama.'</p>
        </div>

        <div style="padding: 20px; background-color: #fff">
          <div style="text-align: center">
            <p style="text-align: center;font-size: 14px;color: #999;margin-bottom: 10px;">' . $user->merchant_name . '</p>
            <p style="text-align: center;font-size: 14px;color: #999;margin-bottom: 10px;">WA: ' . $user->merchant_wa . '</p>
            <p style="text-align: center;font-size: 14px;color: #999;margin-bottom: 10px;">' . $user->merchant_address . '</p>
            <p style="text-align: center; font-weight: bold">' . $dataPost['invoice_number'] . '</p>
            <p style="text-align: center;font-size: 14px;color: #999;margin-bottom: 10px;">' . date('l, d F Y H:i', strtotime($transaction->time_transaction)) . '</p>
          </div>

          <div style="text-align: center">
            <p style="font-size: 30px;background-color: #fff;padding: 10px;border: 1px solid #ddd;margin: 0;">Rp. <span>' . format_rupiah($transaction->amount) . '</span></p>
          </div>

          <div style="margin-top: 20px;padding: 10px;border-top: 1px solid #ddd;border-bottom: 1px solid #ddd;">
            <table style="width: 100%; font-size: 16px">
              ' . $products . '
            </table>
          </div>

          <div style="margin-top: 10px">
            <table style="width: 100%; font-size: 18px">
              <tr>
                <td>Diskon</td>
                <td style="text-align: right">Rp. <span>' . format_rupiah((int)$transaction->discount_amount * -1) . ' ('. $transaction->discount . '%)</span></td>
              </tr>
              <tr>
                <td>Subtotal</td>
                <td style="text-align: right">Rp. <span>' . format_rupiah((int)$transaction->amount - (int)$transaction->amount_tax - (((int)$transaction->fee_on_merchant === 0) ? (int)$transaction->fee : 0)) . '</span></td>
              </tr>
              <tr>
                <td>Pajak</td>
                <td style="text-align: right">Rp. <span>' . format_rupiah($transaction->amount_tax) . '</span></td>
              </tr>
              <tr>
                <td>Biaya Penanganan</td>
                <td style="text-align: right">Rp. <span>' . (((int)$transaction->fee_on_merchant === 0) ? format_rupiah($transaction->fee) : 0) . '</span></td>
              </tr>
            </table>
          </div>

          <div style="margin-top: 10px">
            <table style="width: 100%;font-size: 18px;padding: 10px 0;border-top: 1px solid #ddd;">
              <tr>
                <td><strong>Total</strong></td>
                <td style="text-align: right">
                  <strong>Rp. <span>' . format_rupiah($transaction->amount) . '</span></strong>
                </td>
              </tr>
            </table>
          </div>

          <div style="margin-top: 10px">
            <table style="width: 100%; font-size: 18px">
              <tr>
                <td>Status</td>
                <td style="text-align: right"><strong>' . (((int)$transaction->status_transaction === 1) ? 'LUNAS' : 'BELUM LUNAS') . '</strong></td>
              </tr>
              <tr>
                <td>Jumlah Produk</td>
                <td style="text-align: right"><span>' . $transaction->total_product . '</span> (Pcs)</td>
              </tr>
              <tr>
                <td>Dibayar</td>
                <td style="text-align: right">Rp. <span>' . format_rupiah($transaction->amount_to_pay) . '</span></td>
              </tr>
              <tr>
                <td>Kembalian</td>
                <td style="text-align: right">Rp. <span>' . format_rupiah($transaction->amount_to_back) . '</span></td>
              </tr>
            </table>
          </div>

          <div style="margin-top: 20px;padding: 10px 0;border-top: 1px solid #ddd;">
            <p style="text-align: center">
              <strong>Metode Pembayaran: <span>' . $transaction->payment_method_name . '</span></strong>
            </p>
            ' . $wifi . '
          </div>
        </div>

        <div style="text-align: center;font-size: 12px;color: #666;margin-top: 20px;">
          <p>Terima kasih telah berbelanja di ' . $user->merchant_name . '</p>
          <p>
            <a href="https://www.digipayid.com" style="color: #2c3e50; text-decoration: none">www.digipayid.com</a>
          </p>
        </div>
      </div>
    </div>
    ';

  $urlIMG = "receipts/" . $dataPost['invoice_number'];

  $img = htmlToImage($htmlBody, $dataPost['invoice_number'], $urlIMG);

  // file_put_contents($urlIMG, file_get_contents($img));
  // grab_image($img, $urlIMG);

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
      'subject' => 'Struk pembayaran - ' . $dataPost['invoice_number'] . ' ' . $nama,
      'text_message' => $htmlBody,
      'attachment_url' => $urlIMG . '.png',
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
*Bukti Bayar ' . $nama . ' - ' . $dataPost['invoice_number'] . '*';
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
//   }


//   if ($type === 'whatsapp_payment') {
    $file =  $img;

    $message2 = '
*Bukti Bayar - ' . $nama . '*';
    // sendWhatsapp($dataPost['wa_customer'], $message, $file);

    $db->table('app_notifications')->insert([
      'id_user' => $id_user,
      'type' => 2,
      'destination' => $user->wa_kasir,
      'text_message' => $message2,
      'attachment_url' => $file,
    ]);
  }

    // sleep(1);
//   normalize_notification($id_user);

  $db->close();
}

function sendBilling($type, $dataPost, $transaction, $dataProducts, $user, $payment, $nama)
{
  // print_r($type);
  // print_r('------');
  // print_r($dataPost);
  // print_r('------');
  // print_r($transaction);
  // print_r('------');
  // print_r($dataProducts);
  // print_r('------');
  // print_r($user);
  // print_r('------');
  // print_r($payment);
  // $mpdf = new \Mpdf\Mpdf();
  $products = '';
  foreach ($dataProducts as $product) {
    $products .= '<tr>
                <td>' . $product->product_name . ' x <span>' . format_rupiah($product->product_qty) . '</span></td>
                <td style="text-align: right">Rp. <span>' . format_rupiah($product->product_price) . '</span></td>
            </tr>
            <tr>
                <td colspan="2" style="color: grey; font-size: 10px">' . $product->product_desc . '</td>
            </tr>
            <tr>
                <td colspan="2" style="font-size: 11px">*Req : ' . $product->product_custom_request . '</td>
            </tr>';
  }

  $detailPayment = '';
  if (isset($payment->res->data->no_va)) {
    $detailPayment = '<tr style="border-bottom: 1px solid black;border-collapse: collapse;">
            <td style="padding:10px;"><span style="font-size:14px;">Nomor VA / Transaksi</span></td>
            <td style="padding:10px;text-align: right;"><span style="font-size:14px;padding-left:30px">' . $payment->res->data->no_va . '</span></td>
        </tr>';
  } elseif (isset($payment->res->data->pay_url)) {
    $detailPayment = '<tr style="border-bottom: 1px solid black;border-collapse: collapse;">
            <td style="padding:10px;"><span style="font-size:14px;">Cara Bayar</span></td>
            <td style="padding:10px;text-align: right;">*Link Bayar</td>
        </tr>';
  } elseif (isset($payment->res->data->paylater_url)) {
    $detailPayment = '<tr style="border-bottom: 1px solid black;border-collapse: collapse;">
            <td style="padding:10px;"><span style="font-size:14px;">Cara Bayar</span></td>
            <td style="padding:10px;text-align: right;">*Link Bayar</td>
        </tr>';
  } elseif (isset($payment->res->data->qr_link)) {
    // $detailPayment = '<tr style="border-collapse: collapse;">
    //         <td style="padding:10px;border-collapse: collapse;text-align: center" colspan="2"><img src="' . $payment->res->data->qr_link . '" width="250"></td>
    //     </tr>';
    // $detailPayment = '';
  }

  $htmlBody = '<html>
  <head>
  <style>
    @page {
      size: 490px 3000px;
      margin: .5in;
    }
    #bgimg {
      position: fixed;
      left: -.5in;
      top: -.5in;
      width: 500px;
      height: 3000px;
      z-index: -999
    }
  </style>
  </head>

  <body>
  <div id="bgimg" style="font-family: Arial, sans-serif;color: #333;margin: 0;padding: 0;">
      <div style="width: 450px;background-color: #f6f6f6;border: 1px solid #ddd;padding: 20px;border-radius: 10px;">
        <div style="text-align: center;background-color: #6f42c1;color: #fff;padding: 20px;border-radius: 10px 10px 0 0;">
          <h3 style="margin: 0;font-size: 20px"><b>DIGIPAYID</b></h3>
          <p style="margin: 0">Detail Tagihan - '.$transaction->nama.'</p>
        </div>

        <div style="padding: 20px; background-color: #fff">
          <div style="text-align: center">
            <p style="text-align: center;font-size: 12px;color: #999;margin-bottom: 10px;">' . $user->merchant_name . '</p>
            <p style="text-align: center;font-size: 12px;color: #999;margin-bottom: 10px;">WA: ' . $user->merchant_wa . '</p>
            <p style="text-align: center;font-size: 12px;color: #999;margin-bottom: 10px;">' . $user->merchant_address . '</p>
            <p style="text-align: center; font-weight: bold">' . $dataPost['invoice_number'] . '</p>
            <p style="text-align: center;font-size: 12px;color: #999;margin-bottom: 10px;">' . date('l, d F Y H:i', strtotime($transaction->time_transaction)) . '</p>
          </div>

          <div style="text-align: center">
            <p style="font-size: 20px;background-color: #fff;padding: 10px;border: 1px solid #ddd;margin: 0;">Rp. <span>' . format_rupiah($transaction->amount) . '</span></p>
          </div>

          <div style="margin-top: 20px;padding: 10px;border-top: 1px solid #ddd;border-bottom: 1px solid #ddd;">
            <table style="width: 100%; font-size: 12px">
              ' . $products . '
            </table>
          </div>

          <div style="margin-top: 10px">
            <table style="width: 100%; font-size: 12px">
              <tr>
                <td>Diskon</td>
                <td style="text-align: right">Rp. <span>-' . format_rupiah((int)$transaction->discount_amount * -1) . ' ('. $transaction->discount . '%)</span></td>
              </tr>
              <tr>
                <td>Subtotal</td>
                <td style="text-align: right">Rp. <span>' . format_rupiah((int)$transaction->amount - (int)$transaction->amount_tax - (((int)$transaction->fee_on_merchant === 0) ? (int)$transaction->fee : 0)) . '</span></td>
              </tr>
              <tr>
                <td>Pajak</td>
                <td style="text-align: right">Rp. <span>' . format_rupiah($transaction->amount_tax) . '</span></td>
              </tr>
              <tr>
                <td>Biaya Penanganan</td>
                <td style="text-align: right">Rp. <span>' . (((int)$transaction->fee_on_merchant === 0) ? format_rupiah($transaction->fee) : 0) . '</span></td>
              </tr>
            </table>
          </div>

          <div style="margin-top: 10px">
            <table style="width: 100%;font-size: 12px;padding: 10px 0;border-top: 1px solid #ddd;">
              <tr>
                <td><strong>Total</strong></td>
                <td style="text-align: right">
                  <strong>Rp. <span>' . format_rupiah($transaction->amount) . '</span></strong>
                </td>
              </tr>
              ' . (($detailPayment !== '') ? $detailPayment  : '') . '
            </table>
          </div>

          <div style="margin-top: 10px">
            <table style="width: 100%; font-size: 12px">
              <tr>
                <td>Status</td>
                <td style="text-align: right"><strong>' . (((int)$transaction->status_transaction === 1) ? 'LUNAS' : 'BELUM LUNAS') . '</strong></td>
              </tr>
              <tr>
                <td>Jumlah Produk</td>
                <td style="text-align: right"><span>' . $transaction->total_product . '</span> (Pcs)</td>
              </tr>
              <tr>
                <td>Dibayar</td>
                <td style="text-align: right">Rp. <span>' . format_rupiah($transaction->amount_to_pay) . '</span></td>
              </tr>
              <tr>
                <td>Kembalian</td>
                <td style="text-align: right">Rp. <span>' . format_rupiah($transaction->amount_to_back) . '</span></td>
              </tr>
            </table>
          </div>

          <div style="margin-top: 20px;padding: 10px 0;border-top: 1px solid #ddd;">
            <p style="text-align: center">
              <strong>Metode Pembayaran: <span>' . $transaction->payment_method_name . '</span></strong>
            </p>
          </div>
        </div>

        <div style="text-align: center;font-size: 10px;color: #666;margin-top: 20px;">
          <p>Terima kasih telah berbelanja di ' . $user->merchant_name . '</p>
          <p>
            <a href="https://www.digipayid.com" style="color: #2c3e50; text-decoration: none">www.digipayid.com</a>
          </p>
        </div>
      </div>
    </div>
    </body>
    </html>
    ';

  // echo $htmlBody;
  // die;

  $urlIMG = "billings/" . $dataPost['invoice_number'];

  // print_r($htmlBody);
  // return response()->setStatusCode(200)->setBody($htmlBody);

  $img = htmlToImage($htmlBody, $dataPost['invoice_number'], $urlIMG);

  // file_put_contents($urlIMG, file_get_contents($img));
  // grab_image($img, $urlIMG);

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
      'subject' => 'Detail Tagihan - ' . $dataPost['invoice_number'] . ' ' . $nama,
      'text_message' => $htmlBody,
      'attachment_url' => $urlIMG . '.png',
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
    } elseif (isset($payment->res->data->paylater_url)) {
      $caraBayar = ($payment->res->data->paylater_url);
    }
    $file =  $img;

    // if (isset($payment->res->data->qr_link)) {
    //   $file = $payment->res->data->qr_link;
    // } else {
    //   $file =  $img;
    //   // $file =  urlShortener($img) . '?file=' . substr(md5(Date('YmdHis')), 5, 10) . '.png';
    // }

    $message = '*TAGIHAN ' . $dataPost['invoice_number'] . ' ' . $nama . '*

Total Bayar : *IDR ' . format_rupiah($transaction->amount_to_pay) . '*
Metode Bayar : *' . ($transaction->payment_method_name) . '*
*' . $caraBayar . '*';
    sendWhatsapp($dataPost['wa_customer'], $message, $file);
    if (isset($payment->res->data->qr_link)) {
      // $file = $payment->res->data->qr_link;
      sendWhatsapp($dataPost['wa_customer'], $message, $payment->res->data->qr_link);
    }

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
      "status" => 1
    ]);
  }

  if ($type === 'whatsapp_cash') {
    $file =  $img;

    $message = '
*Tagih Pembayaran - ' . $nama . '*';
    // sendWhatsapp($dataPost['wa_customer'], $message, $file);

    $id_user = (int)$user->id_user;
    if ((int)$user->id_user_parent > 0) {
      $id_user = (int)$user->id_user_parent;
    }
    $db->table('app_notifications')->insert([
      'id_user' => $id_user,
      'type' => 2,
      'destination' => $user->wa_kasir,
      'text_message' => $message,
      'attachment_url' => $file,
    ]);
  }

//   normalize_notification($id_user);

  $db->close();
}


function sendReceiptTopup($type, $invoice_number, $dataJournal, $amountDebet, $user, $dt, $status = "Berhasil")
{
    
    if (isset($dt['data'])) {
        $total = format_rupiah((int)$dt['data']['total_dibayar'] - (int)$amountDebet);
        $amount = format_rupiah((int)$dt['data']['total_dibayar']);
        $method = $dt['data']['payment_channel'];
    } else {
        $total = format_rupiah((int)$dt['amount'] - (int)$amountDebet);
        $amount = format_rupiah((int)$dt['amount']);
        $method = ($dt['type'] == 'DYNAMIC' || $dt['type'] == 'STATIC') ? 'QRIS' : $dt['channel_category'] . ' ' . $dt['channel_code'];
    }
    
  $htmlBody = '<style>
      @page {
        size: 490px 750px;
        margin: 0.5in;
      }
      #bgimg {
        position: fixed;
        left: -0.5in;
        top: -0.5in;
        width: 500px;
        height: 750px;
        z-index: -999;
      }
    </style>

    <div id="bgimg" style="font-family: Arial, sans-serif; color: #333; margin: 0; padding: 0">
      <div style="width: 450px;background-color: #f6f6f6;border: 1px solid #ddd;padding: 20px;border-radius: 10px;overflow: hidden;">
        <div style="text-align: center;background-color: #6f42c1;color: #fff;padding: 20px;border-radius: 10px 10px 0 0;">
          <h3 style="margin: 0; font-size: 30px"><b>DIGIPAYID</b></h3>
          <p style="margin: 0">Struk Deposit Dompet Digital</p>
        </div>

        <div style="padding: 20px; background-color: #fff">
          <div style="text-align: center">
            <p style="text-align: center;font-size: 14px;color: #999;margin-bottom: 10px;"><strong>' . $user->merchant_name . '</strong></p>
            <p style="text-align: center;font-size: 14px;color: #999;margin-bottom: 10px;">WA: ' . $user->merchant_wa . '</p>
            <p style="text-align: center;font-size: 14px;color: #999;margin-bottom: 10px;">' . $user->merchant_address . '</p>
            <p style="text-align: center; font-weight: bold">' . $invoice_number . '</p>
            <p style="text-align: center;font-size: 14px;color: #999;margin-bottom: 10px;">' . date('l, d F Y H:i', strtotime($dataJournal->created_at)) . '</p>
          </div>

          <div style="text-align: center">
            <p style="font-size: 30px;background-color: #fff;padding: 10px;border: 1px solid #ddd;margin: 0;">Rp. <span>' . $total . '</span></p>
          </div>

          <div style="margin-top: 20px">
            <table style="width: 100%; font-size: 18px">
              <tr>
                <td>Status</td>
                <td style="text-align: right">
                  <strong>' . $status . '</strong>
                </td>
              </tr>
              <tr>
                <td>Nominal Deposit</td>
                <td style="text-align: right">
                  <strong>Rp. <span>' . $amount . '</span></strong>
                </td>
              </tr>
              <tr>
                <td>Biaya Penanganan</td>
                <td style="text-align: right">Rp. <span>' . format_rupiah($amountDebet) . '</span></td>
              </tr>
              <tr>
                <td>Total Uang Masuk</td>
                <td style="text-align: right">
                  <strong>Rp. <span>' . $total . '</span></strong>
                </td>
              </tr>
            </table>
          </div>

          <div style="margin-top: 20px; padding: 10px 0; border-top: 1px solid #ddd">
            <p style="text-align: center">
              <strong>Metode Pembayaran: <span>' . $method . '</span></strong>
            </p>
          </div>
        </div>

        <div style="text-align: center; font-size: 12px; color: #666; margin-top: 20px">
          <p>Terima kasih telah melakukan deposit di DIGIPAYID</p>
          <p><a href="https://www.digipayid.com"style="color: #2c3e50; text-decoration: none">www.digipayid.com</a></p>
        </div>
      </div>
    </div>
  ';

  $urlIMG = "receipts/" . $invoice_number;

  $img = htmlToImage($htmlBody, $invoice_number, $urlIMG);

  // file_put_contents($urlIMG, file_get_contents($img));
  // grab_image($img, $urlIMG);

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
      'subject' => 'DIGIPAYID Struk Deposit - ' . $invoice_number,
      'text_message' => $htmlBody,
      'attachment_url' => $urlIMG . '.png',
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
    sendWhatsapp($user->merchant_wa, $message, $file);

    $id_user = (int)$user->id_user;
    if ((int)$user->id_user_parent > 0) {
      $id_user = (int)$user->id_user_parent;
    }
    $db->table('app_notifications')->insert([
      'id_user' => $id_user,
      'type' => 2,
      'destination' => $user->merchant_wa,
      'text_message' => $message,
      'attachment_url' => getDomain() . '/'  . $file,
    ]);
  }

//   normalize_notification($id_user);

  $db->close();
}


function sendReceiptWithdraw($type, $invoice_number, $dataJournal, $amountDebet, $user, $dt, $status = "Berhasil", $noWA = false)
{
    
    if (isset($dt['data'])) {
        $total = format_rupiah((int)$dt['data']['total_dibayar'] - (int)$amountDebet);
        $amount = format_rupiah((int)$dt['data']['total_dibayar']);
        $method = $dt['data']['payment_channel'];
    } else {
        $total = format_rupiah((int)$dt['amount'] - (int)$amountDebet);
        $amount = format_rupiah((int)$dt['amount']);
        $method = (isset($dt['type']) && ($dt['type'] == 'DYNAMIC' || $dt['type'] == 'STATIC')) ? 'QRIS' : $dt['channel_category'] . ' ' . $dt['channel_code'];
    }
    
  $htmlBody = '<style>
      @page {
        size: 490px 750px;
        margin: 0.5in;
      }
      #bgimg {
        position: fixed;
        left: -0.5in;
        top: -0.5in;
        width: 500px;
        height: 750px;
        z-index: -999;
      }
    </style>

    <div id="bgimg" style="font-family: Arial, sans-serif; color: #333; margin: 0; padding: 0">
      <div style="width: 450px;background-color: #f6f6f6;border: 1px solid #ddd;padding: 20px;border-radius: 10px;overflow: hidden;">
        <div style="text-align: center;background-color: #6f42c1;color: #fff;padding: 20px;border-radius: 10px 10px 0 0;">
          <h3 style="margin: 0; font-size: 30px"><b>DIGIPAYID</b></h3>
          <p style="margin: 0">Bukti Transfer</p>
        </div>

        <div style="padding: 20px; background-color: #fff">
          <div style="text-align: center">
            <p style="text-align: center;font-size: 14px;color: #999;margin-bottom: 10px;"><strong>' . $user->merchant_name . '</strong></p>
            <p style="text-align: center;font-size: 14px;color: #999;margin-bottom: 10px;">WA: ' . $user->merchant_wa . '</p>
            <p style="text-align: center;font-size: 14px;color: #999;margin-bottom: 10px;">' . $user->merchant_address . '</p>
            <p style="text-align: center; font-weight: bold">' . $invoice_number . '</p>
            <p style="text-align: center;font-size: 14px;color: #999;margin-bottom: 10px;">' . date('l, d F Y H:i', strtotime($dataJournal->created_at)) . '</p>
          </div>

          <div style="text-align: center">
            <p style="font-size: 30px;background-color: #fff;padding: 10px;border: 1px solid #ddd;margin: 0;">Rp. <span>' . format_rupiah((int)$dt['amount']) . '</span></p>
          </div>

          <div style="margin-top: 20px">
            <table style="width: 100%; font-size: 18px">
              <tr>
                <td>Status</td>
                <td style="text-align: right">
                  <strong>' . $status . '</strong>
                </td>
              </tr>
              <tr>
                <td>Nominal Withdraw</td>
                <td style="text-align: right">
                  <strong>Rp. <span>' . format_rupiah((int)$dt['amount']) . '</span></strong>
                </td>
              </tr>
              <tr>
                <td>Biaya Penanganan</td>
                <td style="text-align: right">Rp. <span>' . format_rupiah($amountDebet) . '</span></td>
              </tr>
              <tr>
                <td>Total Penarikan Uang</td>
                <td style="text-align: right">
                  <strong>Rp. <span>' . format_rupiah((int)$dt['amount'] + (int)$amountDebet) . '</span></strong>
                </td>
              </tr>
            </table>
          </div>

          <div style="margin-top: 20px; padding: 10px 0; border-top: 1px solid #ddd">
            <p style="text-align: center">
              <strong>Metode Pembayaran: <br/><span>' . $dt['channel_category'] . ' ' . $dt['channel_code'] . '</span></strong>
            </p>
          </div>
        </div>

        <div style="text-align: center; font-size: 12px; color: #666; margin-top: 20px">
          <p>Terima kasih telah melakukan withdraw di DIGIPAYID</p>
          <p><a href="https://www.digipayid.com"style="color: #2c3e50; text-decoration: none">www.digipayid.com</a></p>
        </div>
      </div>
    </div>
  ';

  $urlIMG = "receipts/" . $invoice_number;

  $img = htmlToImage($htmlBody, $invoice_number, $urlIMG);

  // file_put_contents($urlIMG, file_get_contents($img));
  // grab_image($img, $urlIMG);

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
      'subject' => 'DIGIPAYID Struk Withdraw - ' . $invoice_number,
      'text_message' => $htmlBody,
      'attachment_url' => $urlIMG . '.png',
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
*Bukti Transfer - ' . $invoice_number . '*';
    // sendWhatsapp($user->merchant_wa, $message, $file);

    $id_user = (int)$user->id_user;
    if ((int)$user->id_user_parent > 0) {
      $id_user = (int)$user->id_user_parent;
    }
    $db->table('app_notifications')->insert([
      'id_user' => $id_user,
      'type' => 2,
      'destination' => $noWA ?? $user->merchant_wa,
      'text_message' => $message,
      'attachment_url' => $file,
    ]);
  }

//   normalize_notification($id_user);

  $db->close();
}
