<?php


namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



// Route::group(['middleware' => ['cors']
// ], function($router){
//     Route::get('/token', [TokenController::class, 'token']);
// });

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', [MainController::class, 'index']
    )->name('dashboard');
    
    Route::group(['middleware' => ['cors']], function($router){
        Route::get('/token', [TokenController::class, 'token']);
    });

    Route::get('/room/error', function () {
        return view('exception.room-member');
    });
    Route::resource('rooms', RoomController::class);
    Route::post('/room/users', [ParticipantController::class, 'store'])->name('participants.store');
    Route::get('/room/users', [ParticipantController::class, 'index'])->name('participants.index');
    Route::get('/room/users/{room_id}', [ParticipantController::class, 'destroy'])->name('participants.destroy');
    Route::post('/room/{room_id}/{user_id}/messages', [MessageController::class, 'store']);
    Route::get('/room/{room_id}/{user_id}/messages', [MessageController::class, 'index']);
    Route::get('/room/{room_id}/messages', [MessageController::class, 'show']);
});
