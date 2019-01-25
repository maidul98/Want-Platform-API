<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Events\MessageSentEvent;

use App\Classes\Register;

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

/**
 * Normal login/register 
 */
Route::post('login', 'PassportController@login');
Route::post('register', 'PassportController@register');

/**
 * Login with Google
 */
Route::get('redirect-google', 'PassportController@redirectToProviderGoogle');
Route::get('callback', 'PassportController@handleProviderCallbackGoogle');

//User account actions
Route::middleware('auth:api')->group(function () {
    Route::get('user', 'PassportController@details');
    Route::post('logout', 'PassportController@logout');
    Route::post('change-password', 'PassportController@changePassword');
});


/**
 * Payment endpoints
 */
Route::middleware('auth:api')->group(function () {
    Route::post('/pay', ['uses' =>'WantController@CompleteWant']);
    Route::post('/addcard', ['uses' =>'PaymentController@addCard']);
});


/**
 * Want endpoints
 */
Route::middleware('auth:api')->group(function () {
    Route::post('want', 'WantController@store');
    Route::put('want/{id}', 'WantController@update');
    Route::delete('want/{id}', 'WantController@destroy');
    Route::get('want/{id}', ['uses' =>'WantController@show']);
});

/**
 * Reviews
 */
Route::middleware('auth:api')->group(function () {
    Route::post('review', 'ReviewController@addReview');
    Route::get('review/{user}', 'ReviewController@getAllReviews');
});


/**
 * Conversations endpoints
 */
Route::middleware('auth:api')->group(function () {
    Route::get('convos', 'ConversationController@getConversations');
});


/**
 * Messageing endpoints
 */
Route::middleware('auth:api')->group(function () {
    Route::post('get-message', 'MessageController@fetch');
    Route::post('send-message', 'MessageController@sendMessage');
});


/**
 * Profile
 */
Route::middleware('auth:api')->group(function () {
    Route::get('profile/{user}', 'UserController@profile');
});

/**
 * Set and get current user avatar
 */
Route::middleware('auth:api')->group(function () {
    Route::post('avatar', 'SettingsController@updateAvatar');
    Route::get('avatar', 'UserController@getAvatar');
});

/**
 * Newsfeed
 */
Route::middleware('auth:api')->group(function () {
    Route::post('newsfeed', 'NewsFeedController@newsFeed');
});

/**
 * Category
 */
Route::middleware('auth:api')->group(function () {
    Route::get('category', 'CategoryController@getAll');
    Route::get('category/{id}', 'CategoryController@getSingle');
});
Route::get('test1', 'CategoryController@getSingle');
/**
 * Settings
 */
Route::middleware('auth:api')->group(function () {
    Route::post('update_profile', 'SettingsController@updateNameEmailTagDes');
    Route::get('category/{id}', 'CategoryController@getSingle');
});

Route::get('t', function(){
    $x = new Register();
    return $x->cat();
});



/**
 * Pusher auth.
 * If user belongs to the the chat then return a token.
 */
Route::post('pusher-auth', function() {
    $pusher = new Pusher\Pusher( env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), array('cluster' => env('PUSHER_APP_CLUSTER')));
    if(Auth::user())
    return $pusher->socket_auth(request()->channel_name, request()->socket_id);
});

