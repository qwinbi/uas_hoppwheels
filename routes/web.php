<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\RatingController;
use App\Http\Controllers\User\FavoriteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\FooterController;
use App\Http\Controllers\Admin\LogoController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');

Route::get('/dashboard', function () {
    return redirect()->route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'profile.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // User Routes
    Route::middleware('role:user')->group(function () {
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index'])->name('cart.index');
            Route::post('/', [CartController::class, 'store'])->name('cart.store');
            Route::delete('/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
        });

        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('orders.index');
            Route::post('/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
            Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
        });

        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        });

        Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');

        Route::prefix('favorites')->group(function () {
            Route::get('/', [FavoriteController::class, 'index'])->name('favorites.index');
            Route::post('/', [FavoriteController::class, 'store'])->name('favorites.store');
            Route::delete('/{favorite}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
        });
    });

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::resource('products', ProductController::class);
        
        Route::prefix('about')->group(function () {
            Route::get('/edit', [AboutController::class, 'edit'])->name('about.edit');
            Route::put('/', [AboutController::class, 'update'])->name('about.update');
        });
        
        Route::prefix('footer')->group(function () {
            Route::get('/edit', [FooterController::class, 'edit'])->name('footer.edit');
            Route::put('/', [FooterController::class, 'update'])->name('footer.update');
        });
        
        Route::resource('logos', LogoController::class);
        Route::put('/logos/{logo}/set-active', [LogoController::class, 'setActive'])->name('logos.set-active');
        
        Route::resource('payments', PaymentController::class)->only(['index', 'edit', 'update']);
        
        Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'update']);
        
        Route::prefix('profile')->group(function () {
            Route::get('/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
            Route::put('/', [AdminProfileController::class, 'update'])->name('profile.update');
        });
    });
});

require __DIR__.'/auth.php';