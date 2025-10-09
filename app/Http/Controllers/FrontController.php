<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    //

    public function index(){

        $categorires = Category::all();
        dd($categorires);
        return view('welcome');
    }
}
