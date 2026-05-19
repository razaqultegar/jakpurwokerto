<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('pages.admin.dashboard', ['title' => 'Dashboard Admin']);
    }
}
