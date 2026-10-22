<?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\Auth\LoginController;
    use App\Http\Controllers\DashboardController;

    Route::get('/', function () {
        return redirect()->route('login');
    });

    // Authentication routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Protected ERP Dashboard & User/Role Management
    Route::middleware(['auth'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        include "Gate/gate.php";
        include "Weight/weight.php";
        include "Lab/lab.php";
        include "Dispatch/dispatch.php";

        include "Dashboards/dashboards.php";
        include "Admin/users.php";
        include "Admin/countries.php";
        include "Admin/cities.php";
        include "Admin/states.php";

        include "Admin/brands.php";
        include "Admin/products.php";
        include "Admin/grades.php";
        include "Admin/sizes.php";
        include "Admin/units.php";
        include "Admin/saleOrders.php";
        include "Admin/customers.php";
        include "Admin/rawMaterials.php";

        include "UnloadMaterial/unloader.php";

        include "Admin/purchase.php";

    });
