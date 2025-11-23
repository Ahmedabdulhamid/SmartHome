<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function goToPage($slug)
    {
       $page=Page::whereSlug($slug)->first();
       return view('pages.pages',['page'=>$page]);

    }
}
