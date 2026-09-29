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
    /*Route::middleware(['auth'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        include "Admin/users.php";
        // Route::resource('users', UserController::class);
        // Route::match(['patch', 'post'], '/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        // Route::get('/add-role', [UserController::class, 'create'])->name('add-role');
    });*/

    /*
        |--------------------------------------------------------------------------
        | Dashboard Routes
        |--------------------------------------------------------------------------
    */

    Route::middleware(['auth'])->group(function () {
        // Admin
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Sale
        Route::get('/sale/dashboard', [DashboardController::class, 'sale'])
            ->name('dashboard.sale');

        // Purchase
        Route::get('/purchase/dashboard', [DashboardController::class, 'purchase'])
            ->name('dashboard.purchase');

        // Gate
        Route::get('/gate/dashboard', [DashboardController::class, 'gate'])
            ->name('dashboard.gate');

        // Weight
        Route::get('/weight/dashboard', [DashboardController::class, 'weight'])
            ->name('dashboard.weight');

        // Unloader
        Route::get('/unloader/dashboard', [DashboardController::class, 'unloader'])
            ->name('dashboard.unloader');

        // Dispatch
        Route::get('/dispatch/dashboard', [DashboardController::class, 'dispatch'])
            ->name('dashboard.dispatch');

        // Lab
        Route::get('/lab/dashboard', [DashboardController::class, 'lab'])
            ->name('dashboard.lab');

        // Production
        Route::get('/production/dashboard', [DashboardController::class, 'production'])
            ->name('dashboard.production');

        // Lab Production
        Route::get('/lab-production/dashboard', [DashboardController::class, 'labProduction'])
            ->name('dashboard.lab-production');

        // Account
        Route::get('/account/dashboard', [DashboardController::class, 'account'])
            ->name('dashboard.account');


        // Admin
        include "Admin/users.php";
    });

