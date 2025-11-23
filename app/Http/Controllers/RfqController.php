<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RfqController extends Controller
{
   public function index()
   {
    return view('rfq.rfq');
   }
}
