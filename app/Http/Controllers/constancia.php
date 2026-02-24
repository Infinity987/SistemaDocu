<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class constancia extends Controller
{
    public function index() {
           
        return view('constancia.index');
    }
}
