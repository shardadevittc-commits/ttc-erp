<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\GateAndGaadi;

class DispatchController extends Controller
{
    public function index(Request $request)
    {
        $gate = GateAndGaadi::with('customer')->where('formlist', 'Material Out')->get();
        //echo"<pre>";print_r($gate->toArray());die;
        return view('dispatch.index', compact('gate'));
    }
}
