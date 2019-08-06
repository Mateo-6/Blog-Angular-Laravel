<?php
//use Symfony\Component\Routing\Annotation\Route;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\ApiAuthMiddleware;

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

/*route::get('/post/pruebas', 'PostController@test');
route::get('/categorias/pruebas', 'CategoryController@test');*/

// User paths
Route::prefix('/api/user')->group(function ()
{
    Route::get('/pruebas', 'UserController@test');
    Route::post('/register', 'UserController@register');
    Route::post('/login', 'UserController@login');
    Route::put('/update', 'UserController@update');
    Route::post('/upload', 'UserController@upload')->middleware(ApiAuthMiddleware::class);
    Route::get('/avatar/{filename}', 'UserController@getImage');
    Route::get('detail/{id}', 'UserController@detail');
});

// Category paths
Route::resource('/api/category', 'CategoryController');

// Post paths
Route::prefix('/api/post')->group(function ()
{
    Route::post('/upload', 'PostController@upload');
    Route::get('/image/{fileName}', 'PostController@getImage');
    Route::get('/category/{id}', 'PostController@getPostsByCategory');
    Route::get('/user/{id}', 'PostController@getPostsByUser');
});

Route::resource('/api/post', 'PostController');

