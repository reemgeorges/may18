<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\orders\OrderController;
use App\Http\Controllers\products\ProductController;
use App\Http\Controllers\reviews\ReviewsController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\users\UserController;
use App\Http\Controllers\vendors\VendorController;
use App\Http\Resources\ReviewCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [AuthController::class,'register']);
Route::post('/logout', [AuthController::class,'logout']);
;
Route::post('/login', [AuthController::class,'login'])->name('login');


Route::group([

  //'prefix' => 'products',
  //  'middleware' => ['auth:sanctum','throttle:60,1']
    'middleware' => ['auth:sanctum']
], function () {
    Route::match(['put', 'patch'], '/update-user/{id}', [UserController::class,'updateRoles']);
    Route::group([
        'middleware' => 'isadmin'
    ], function () {
        Route::post('/add-product', [ProductController::class,'store']);
        Route::match(['put', 'patch'], '/update-product/{id}', [ProductController::class,'update']);
        Route::delete('/delete-product/{id}', [ProductController::class,'destroy']);
        Route::get('/u', [UserController::class,'getUsersByRole']);
    });

    Route::get('/all-products', [ProductController::class,'index']);
    //There is something wrong right here....i wish you discover it
    Route::get('/product/{letter}', [ProductController::class,'filterProductsByCategory']);//هنا الخطا
    Route::get('/product/{id}', [ProductController::class,'show']);
    Route::get('/all-users', [UserController::class,'index']);

});

Route::group(['middleware' => ['auth:sanctum']], function () {
    // ...

    // Reviews routes
    Route::post('/reviews', [ReviewsController::class, 'store']);
    Route::get('/reviews', [ReviewsController::class, 'index']);
    Route::get('/reviews/{id}', [ReviewsController::class, 'show']);
    Route::match(['put', 'patch'], '/reviews/{id}', [ReviewsController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewsController::class, 'destroy']);

    // User reviews
    Route::get('/users/reviews/{userId}', [ReviewsController::class, 'getUserReviews']);

    // Product reviews
    Route::get('/products/reviews/{productId}', [ReviewsController::class, 'getProductReviews']);

    // عرض قائمة الطلبات
    Route::get('/orders',[OrderController::class, 'index']);
    Route::post('/orders',[OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::match(['put', 'patch'],'/orders/{order}',[OrderController::class, 'update']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']);



      // عرض قائمة الطلبات
      Route::get('/vendors',[VendorController::class, 'index']);
      Route::post('/vendors',[VendorController::class, 'store']);
      Route::get('/vendors/{vendor}', [VendorController::class, 'show']);
      Route::match(['put', 'patch'],'/vendors/{vendor}',[VendorController::class, 'update']);
      Route::delete('/vendors/{vendor}', [VendorController::class, 'destroy']);

      //===== ترتيب المستخدمين على حسب عدد اراءهم تنازليا
      Route::get('/users/users-with-reviews', [UserController::class, 'getUsersWithReviews']);

      //===========معدل كل منتج avg
      Route::get('/products/start_avg', [ProductController::class, 'getProductsWithAverageRating']);
   //=======================المستخدمين الذين قيمو منتج
   Route::get('/products/get_users/{productId}',[ProductController::class, 'getUsersByProductReviews']);
});
