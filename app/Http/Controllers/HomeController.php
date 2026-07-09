<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.home.index', [
            'title' => 'Beranda',
            'articles' => ArticleController::latest(3),
        ]);
    }
}
