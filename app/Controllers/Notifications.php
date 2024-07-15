<?php

namespace App\Controllers;

class Notifications extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }

    public function postSend_email()
    {
        $request = request();
        $dataPost = $request->getPost();
        if (isset($dataPost['link_attachment'])) {
            sendMail($dataPost['to'], $dataPost['subject'], $dataPost['message'], $dataPost['link_attachment']);
        } else {
            sendMail($dataPost['to'], $dataPost['subject'], $dataPost['message']);
        }
    }
}
