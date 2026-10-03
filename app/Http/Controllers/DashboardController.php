<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the main ERP dashboard.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->load('role');

        $stats = [
            'total_users'   => User::count(),
            'total_roles'   => Role::count(),
            'active_users'  => User::where('status', 1)->count(),
        ];

        return view('dashboard.index', compact('user', 'stats'));
    }


    /**
     * Sale Dashboard
     */
    public function sale()
    {
        $user = Auth::user();

        return view('dashboard.sale', compact('user'));
    }

    /**
     * Purchase Dashboard
     */
    public function purchase()
    {
        $user = Auth::user();

        return view('dashboard.purchase', compact('user'));
    }

    /**
     * Gate Dashboard
     */
    public function gate()
    {
        $user = Auth::user();

        return view('dashboard.gate', compact('user'));
    }

    /**
     * Weight Dashboard
     */
    public function weight()
    {
        $user = Auth::user();

        return view('dashboard.weight', compact('user'));
    }

    /**
     * Unloader Dashboard
     */
    public function unloader()
    {
        $user = Auth::user();

        return view('dashboard.unloader', compact('user'));
    }

    /**
     * Dispatch Dashboard
     */
    public function dispatch()
    {
        $user = Auth::user();

        return view('dashboard.dispatch', compact('user'));
    }

    /**
     * Lab Dashboard
     */
    public function lab()
    {
        $user = Auth::user();

        return view('dashboard.lab', compact('user'));
    }

    /**
     * Production Dashboard
     */
    public function production()
    {
        $user = Auth::user();

        return view('dashboard.production', compact('user'));
    }

    /**
     * Lab Production Dashboard
     */
    public function labProduction()
    {
        $user = Auth::user();

        return view('dashboard.lab-production', compact('user'));
    }

    /**
     * Account Dashboard
     */
    public function account()
    {
        $user = Auth::user();

        return view('dashboard.account', compact('user'));
    }
}
