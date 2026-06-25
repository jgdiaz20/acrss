<?php

namespace App\Http\Controllers;

class PublicHomeController extends Controller
{
    public function index()
    {
        return view('home.public');
    }
}
