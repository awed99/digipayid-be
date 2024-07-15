<?php

use Google\Client;
use Google\Service\Drive;

function uploadToToGdrive($filex = 'logo.png', $invNo = '', $user = null, $folderId = '17mhG-Gvf_sfRhlrixNZUQiNTLUs1uwZ8')
{

    try {
        $jsonKey = json_decode(file_get_contents('digipayid-key.json'), true);

        $client = new Client();
        $client->setAuthConfig($jsonKey);
        $client->addScope(Drive::DRIVE);
        $driveService = new Drive($client);
        $fileMetadata = new Drive\DriveFile(array(
            'name' => $invNo . '.pdf',
            'parents' => array($folderId)
        ));
        $content = file_get_contents($filex);
        $file_info = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $file_info->buffer($content);
        $file = $driveService->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => $mime_type,
            'uploadType' => 'multipart',
            'fields' => 'id'
        ));

        $db = db_connect();
        $update['url_file_billing'] = "https://drive.google.com/file/d/" . $file->id;

        if ((int)$user->id_user_parent > 0) {
            $db->table('app_transactions_' . $user->id_user_parent)->where('invoice_number', $invNo)->update($update);
        } else {
            $db->table('app_transactions_' . $user->id_user)->where('invoice_number', $invNo)->update($update);
        }
        $db->close();

        // printf("File ID: %s\n", "https://drive.google.com/file/d/".$file->id);
        unlink($filex);
        return "https://drive.google.com/file/d/" . $file->id;
    } catch (Exception $e) {
        return "Error Message: " . $e;
    }
}
