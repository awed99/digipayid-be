<?php

date_default_timezone_set("Asia/Bangkok");
//https://pdfcrowd.com/api/html-to-image-php/
// require "pdfcrowd.php";
// require_once __DIR__ . '/../../vendor/autoload.php';

function getDomainName()
{
  return env('API_DOMAIN_BASE_URL0');
}


function sendReceiptCashier($type, $dataPost, $transaction, $dataProducts, $user, $payment, $nama)
{
  $products = '';
  foreach ($dataProducts as $product) {
    $products .= '<tr>
                <td>' . $product->product_name . ' x <span>' . format_rupiah($product->product_qty) . '</span></td>
                <td style="text-align: right">Rp. <span>' . format_rupiah($product->product_price) . '</span></td>
            </tr>';
  }

  $htmlBody = '
  
  <style>
    @page {
      size: 490px 2500px;
      margin: .5in;
    }
    #bgimg {
      position: fixed;
      left: -.5in;
      top: -.5in;
      width: 500px;
      height: 2500px;
      z-index: -999
    }
  </style>

  <div id="bgimg" style="font-family: Arial, sans-serif;color: #333;margin: 0;padding: 0;">
      <div style="width: 450px;background-color: #f6f6f6;border: 1px solid #ddd;padding: 20px;border-radius: 10px;">
        <div style="text-align: center;background-color: #6f42c1;color: #fff;padding: 20px;border-radius: 10px 10px 0 0;">
          <h3 style="margin: 0;font-size: 30px"><b>DIGIPAYID</b></h3>
          <p style="margin: 0">Struk Pembayaran</p>
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
                <td>Subtotal</td>
                <td style="text-align: right">Rp. <span>' . format_rupiah((int)$transaction->amount - (int)$transaction->amount_tax - (int)$transaction->fee) . '</span></td>
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

  if ($type === 'whatsapp') {
    $file =  $img;

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

  if ($type === 'whatsapp_payment') {
    $file =  $img;

    $message = '
*Bukti Bayar - ' . $nama . '*';
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

  normalize_notification($id_user);

  $db->close();
}
