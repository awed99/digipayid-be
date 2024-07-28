<?php

namespace App\Controllers;

class Crons extends BaseController
{
    public function index()
    {
        normalize();
        return view('welcome_message');
    }

    public function cliIndex()
    {
        normalize();
        // return view('welcome_message');
    }

    public function cliIndex2()
    {
        normalize();
        // return view('welcome_message');
    }

    public function cliNotif()
    {
        normalize_notifications();
        // return view('welcome_message');
    }

    public function postNormalize()
    {
        normalize();
    }

    public function getNormalize()
    {
        normalize();
    }
}
