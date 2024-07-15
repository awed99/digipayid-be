<?php

// $authkey   = '44asQgtUNOe95EkHS9qgSA7VpvTa8884t63vNnDHPILxgM8SAP'; // API KEY Anda
// $api_key   = 'ab23a0f4c56b8f6fb1510135c0082841bf915cf2'; // API KEY Anda
// $id_device = '8185'; // ID DEVICE yang di SCAN (Sebagai pengirim)
// $url   = 'https://api.watsap.id/send-media'; // URL API
// $no_hp = '6281290383389'; // No.HP yang dikirim (No.HP Penerima)
// $pesan = '😁 Halo Terimakasih 🙏'; // Caption/Keterangan Gambar
// $tipe  = 'image'; // Tipe Pesan Media Gambar
// $link  = 'https://mauboy.com/wp-content/uploads/2018/05/Image7.jpg'; // Link atau URL FILE MEDIA (.jpg, .jpeg, .png)

// $domainLink = 'https://' . $_SERVER['HTTP_HOST'];
// // echo $_SERVER['SERVER_NAME'];
// echo $domainLink;
// die();

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://app.wapanels.com/api/create-message',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array(
  'appkey' => '7205f1ec-f32f-4bed-b700-983535861478',
  'authkey' => '44asQgtUNOe95EkHS9qgSA7VpvTa8884t63vNnDHPILxgM8SAP',
  'to' => '6281290383389',
  'message' => 'Test 
message X',
//   'file' => $link,
  'file' => 'https://mauboy.com/wp-content/uploads/2018/05/Image7.jpg',
  'sandbox' => 'false'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;