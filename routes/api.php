<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Events\MessageSentEvent;

use App\Classes\Register;

use App\Want;
use App\User;

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
 * Card endpoints
 */
Route::middleware('auth:api')->group(function () {
    Route::post('card', ['uses' =>'CardsController@add']);
    Route::delete('card', ['uses' =>'CardsController@remove']);
    Route::get('card', ['uses' =>'CardsController@getAll']);
});

/**Payment endpoints */
Route::middleware('auth:api')->group(function () {
    Route::post('boo', ['uses' =>'PaymentController@payFulfiller']);
});

/**
 * Want endpoints
 */
Route::middleware('auth:api')->group(function () {
    Route::post('want', 'WantController@store');
    Route::put('want/{id}', 'WantController@update');
    Route::delete('want/{id}', 'WantController@destroy');
    Route::get('want/{id}', ['uses' =>'WantController@show']);

    Route::post('accept', ['uses' => 'WantController@acceptWant']);

    Route::get('all', ['uses' => 'WantController@all']);
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
    Route::get('conversations', 'ConversationController@getConversations');
    Route::post('conversation', 'ConversationController@createConversation');
});


/**
 * Messageing endpoints
 */
Route::middleware('auth:api')->group(function () {
    Route::post('get-message', 'MessageController@fetch');
    Route::post('send-message', 'MessageController@sendMessage');
    Route::post('seen-message', 'MessageController@seen');
    Route::get('total-unread-message', 'MessageController@total_unread');
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

/**
 * Settings
 */
Route::middleware('auth:api')->group(function () {
    Route::post('update_profile', 'SettingsController@updateNameEmailTagDes');
    Route::get('category/{id}', 'CategoryController@getSingle');
});


/**
 * Bookmarks
 */
Route::middleware('auth:api')->group(function () {
    Route::post('bookmark', 'BookmarkController@add');
    Route::get('bookmarks', 'BookmarkController@all');
    Route::delete('bookmark', 'BookmarkController@remove');
});


/**
 * notifactions
 */
Route::middleware('auth:api')->group(function () {
    Route::get('notifiactions', 'NotificactionController@get_all_unread');
    Route::post('mark-notifiactions-read', 'NotificactionController@markAsRead');
});

/**
 * Comments 
 */
Route::middleware('auth:api')->group(function () {
    Route::post('comment', 'CommentController@store');
    Route::post('reply', 'CommentController@replyStore');
});

Route::get('/search', function (Request $request) {
    return Want::search($request->search)->get();
});


/**Password reset routes */
Route::group([      
    'prefix' => 'password-reset'
], function () {    
    Route::post('create', 'PasswordResetController@create');
    Route::get('find/{token}', 'PasswordResetController@find');
    Route::post('reset', 'PasswordResetController@reset');
});

/**
 * Mobile API
 */

 /**Add or update mobile device token */
Route::middleware('auth:api')->group(function () {
    Route::post('device-token', 'DeviceTokenController@addOrUpdateToken');
});


// Route::get('/ml', function (Request $request) {
//     $user = User::find(1);
//     // Prepare the request for recombee server, we need 10 recommended items for a given user.
//     $recommendations = Laracombee::recommendTo($user, 10)->wait();
//     return $recommendations->recomms;
// });


