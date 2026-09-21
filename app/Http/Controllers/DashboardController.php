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
            'total_users' => User::count(),
            'total_roles' => Role::count(),
            'active_users' => User::where('status', 1)->count(),
        ];

        return view('dashboard.index', compact('user', 'stats'));
    }
}
