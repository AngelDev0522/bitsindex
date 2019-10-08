<?php

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


use TCG\Voyager\Events\Routing;
use TCG\Voyager\Events\RoutingAdmin;
use TCG\Voyager\Events\RoutingAdminAfter;
use TCG\Voyager\Events\RoutingAfter;
use TCG\Voyager\Facades\Voyager;
Auth::routes();


Route::get('/', 'WelcomeController@index');

Route::get('/clearcache', 'QRAuthController@clear');
// Route::get('/register', 'QRAuthController@activate');

Route::group(array('middleware' => 'guest'), function() {
    // Route::get('/qr-register', 'QRAuthController@register');
    Route::post('/qr-login', ['uses' => 'QRAuthController@checkUser']);
    Route::get('/qr-login', ['uses' => 'QRAuthController@login']);
    Route::get('/qr-activate', 'QRAuthController@activate');
});

Route::group(array('middleware' => 'auth'), function() {
    Route::get('/2fa-login', 'UserController@twoFALogin');
    Route::post('/2fa-verify', 'UserController@twoFAVerify');
});

Route::group(array('middleware' => ['auth', '2faAuth']), function() {
    Route::get('/home', 'HomeController@index');
    Route::get('/profile', ['uses' => 'HomeController@profile']);
    Route::get('/chat', ['uses' => 'UserController@index']);
    Route::get('/users', ['uses' => 'UserController@getOtherUsers']);
    Route::get('/messages/{userId}/', ['uses' => 'UserController@getStoredMessage']);
    Route::post('/messages/{userId}/', ['uses' => 'UserController@postNewMessage']);
    Route::post('/fileUpload/{userId}/', ['uses' => 'UserController@postImage']);
    Route::get('/user/{user}/online', ['uses' => 'UserOnlineController']);
    Route::get('/user/{user}/offline', ['uses' => 'UserOfflineController']);
    Route::get('event/add','EventController@createEvent');
    Route::post('event/add','EventController@store');
    Route::get('event','EventController@calender');
    Route::get('event/remove','EventController@removeevent');
    Route::get('event/remove/{id}','EventController@remove');
    Route::get('inbox','MailController@inbox');
    Route::get('sentmail','MailController@sentmail');
    Route::get('sentmails','MailController@sentmails');
    Route::get('compose','MailController@compose');
    Route::get('showemail/{id}','MailController@showemail');
    Route::get('removemail/{id}','MailController@removemail');
    Route::post('sendemail','MailController@sendemail');
    Route::post('updateprofile','HomeController@updateprofile');

    $coins = ['litecoin', 'peercoin', 'ripple'];
    foreach($coins as $coin){
        Route::post("wallet/$coin/importsecret", "WalletController@{$coin}ImportSecret");
        Route::post("wallet/$coin/exportsecret", "WalletController@{$coin}ExportSecret");
        Route::post("wallet/$coin/send", "WalletController@{$coin}Send");
        Route::get("wallet/$coin", "WalletController@{$coin}");
    }

    Route::post('/get2FACode', 'UserController@get2FACode');
    Route::post('/verify2FACode', 'UserController@verify2FACode');
});

Route::group(['prefix' => 'admin'], function () {
    Route::get('users/download', ['uses' => 'AdminController@download', 'as' => 'voyager.users.download']);
    Route::post('users/upload', ['uses' => 'AdminController@upload', 'as' => 'voyager.users.upload']);
    Route::group(['as' => 'voyager.'], function () {
        event(new Routing());

        $namespacePrefix = '\\'.config('voyager.controllers.namespace').'\\';

        Route::get('login', ['uses' => $namespacePrefix.'VoyagerAuthController@login',     'as' => 'login']);
        Route::post('login', ['uses' => $namespacePrefix.'VoyagerAuthController@postLogin', 'as' => 'postlogin']);

        Route::group(array('middleware' => ['admin.user', '2faAuth']), function () use ($namespacePrefix) {
            event(new RoutingAdmin());

            // Main Admin and Logout Route
            Route::get('/', ['uses' => $namespacePrefix.'VoyagerController@index',   'as' => 'dashboard']);
            Route::post('logout', ['uses' => 'VoyagerController@logout',  'as' => 'logout']);
            Route::post('upload', ['uses' => $namespacePrefix.'VoyagerController@upload',  'as' => 'upload']);

            Route::get('profile', ['uses' => $namespacePrefix.'VoyagerUserController@profile', 'as' => 'profile']);

            try {
                foreach (Voyager::model('DataType')::all() as $dataType) {
                    $breadController = $dataType->controller
                                     ? Str::start($dataType->controller, '\\')
                                     : $namespacePrefix.'VoyagerBaseController';

                    Route::get($dataType->slug.'/order', $breadController.'@order')->name($dataType->slug.'.order');
                    Route::post($dataType->slug.'/action', $breadController.'@action')->name($dataType->slug.'.action');
                    Route::post($dataType->slug.'/order', $breadController.'@update_order')->name($dataType->slug.'.order');
                    Route::get($dataType->slug.'/{id}/restore', $breadController.'@restore')->name($dataType->slug.'.restore');
                    Route::get($dataType->slug.'/relation', $breadController.'@relation')->name($dataType->slug.'.relation');
                    Route::resource($dataType->slug, $breadController);
                }
            } catch (\InvalidArgumentException $e) {
                throw new \InvalidArgumentException("Custom routes hasn't been configured because: ".$e->getMessage(), 1);
            } catch (\Exception $e) {
                // do nothing, might just be because table not yet migrated.
            }

            // Role Routes
            Route::resource('roles', $namespacePrefix.'VoyagerRoleController');

            // Menu Routes
            Route::group([
                'as'     => 'menus.',
                'prefix' => 'menus/{menu}',
            ], function () use ($namespacePrefix) {
                Route::get('builder', ['uses' => $namespacePrefix.'VoyagerMenuController@builder',    'as' => 'builder']);
                Route::post('order', ['uses' => $namespacePrefix.'VoyagerMenuController@order_item', 'as' => 'order']);

                Route::group([
                    'as'     => 'item.',
                    'prefix' => 'item',
                ], function () use ($namespacePrefix) {
                    Route::delete('{id}', ['uses' => $namespacePrefix.'VoyagerMenuController@delete_menu', 'as' => 'destroy']);
                    Route::post('/', ['uses' => $namespacePrefix.'VoyagerMenuController@add_item',    'as' => 'add']);
                    Route::put('/', ['uses' => $namespacePrefix.'VoyagerMenuController@update_item', 'as' => 'update']);
                });
            });

            // Settings
            Route::group([
                'as'     => 'settings.',
                'prefix' => 'settings',
            ], function () use ($namespacePrefix) {
                Route::get('/', ['uses' => $namespacePrefix.'VoyagerSettingsController@index',        'as' => 'index']);
                Route::post('/', ['uses' => $namespacePrefix.'VoyagerSettingsController@store',        'as' => 'store']);
                Route::put('/', ['uses' => $namespacePrefix.'VoyagerSettingsController@update',       'as' => 'update']);
                Route::delete('{id}', ['uses' => $namespacePrefix.'VoyagerSettingsController@delete',       'as' => 'delete']);
                Route::get('{id}/move_up', ['uses' => $namespacePrefix.'VoyagerSettingsController@move_up',      'as' => 'move_up']);
                Route::get('{id}/move_down', ['uses' => $namespacePrefix.'VoyagerSettingsController@move_down',    'as' => 'move_down']);
                Route::put('{id}/delete_value', ['uses' => $namespacePrefix.'VoyagerSettingsController@delete_value', 'as' => 'delete_value']);
            });

            // Admin Media
            Route::group([
                'as'     => 'media.',
                'prefix' => 'media',
            ], function () use ($namespacePrefix) {
                Route::get('/', ['uses' => $namespacePrefix.'VoyagerMediaController@index',              'as' => 'index']);
                Route::post('files', ['uses' => $namespacePrefix.'VoyagerMediaController@files',              'as' => 'files']);
                Route::post('new_folder', ['uses' => $namespacePrefix.'VoyagerMediaController@new_folder',         'as' => 'new_folder']);
                Route::post('delete_file_folder', ['uses' => $namespacePrefix.'VoyagerMediaController@delete', 'as' => 'delete']);
                Route::post('move_file', ['uses' => $namespacePrefix.'VoyagerMediaController@move',          'as' => 'move']);
                Route::post('rename_file', ['uses' => $namespacePrefix.'VoyagerMediaController@rename',        'as' => 'rename']);
                Route::post('upload', ['uses' => $namespacePrefix.'VoyagerMediaController@upload',             'as' => 'upload']);
                Route::post('remove', ['uses' => $namespacePrefix.'VoyagerMediaController@remove',             'as' => 'remove']);
                Route::post('crop', ['uses' => $namespacePrefix.'VoyagerMediaController@crop',             'as' => 'crop']);
            });

            // BREAD Routes
            Route::group([
                'as'     => 'bread.',
                'prefix' => 'bread',
            ], function () use ($namespacePrefix) {
                Route::get('/', ['uses' => $namespacePrefix.'VoyagerBreadController@index',              'as' => 'index']);
                Route::get('{table}/create', ['uses' => $namespacePrefix.'VoyagerBreadController@create',     'as' => 'create']);
                Route::post('/', ['uses' => $namespacePrefix.'VoyagerBreadController@store',   'as' => 'store']);
                Route::get('{table}/edit', ['uses' => $namespacePrefix.'VoyagerBreadController@edit', 'as' => 'edit']);
                Route::put('{id}', ['uses' => $namespacePrefix.'VoyagerBreadController@update',  'as' => 'update']);
                Route::delete('{id}', ['uses' => $namespacePrefix.'VoyagerBreadController@destroy',  'as' => 'delete']);
                Route::post('relationship', ['uses' => $namespacePrefix.'VoyagerBreadController@addRelationship',  'as' => 'relationship']);
                Route::get('delete_relationship/{id}', ['uses' => $namespacePrefix.'VoyagerBreadController@deleteRelationship',  'as' => 'delete_relationship']);
            });

            // Database Routes
            Route::resource('database', $namespacePrefix.'VoyagerDatabaseController');

            // Compass Routes
            Route::group([
                'as'     => 'compass.',
                'prefix' => 'compass',
            ], function () use ($namespacePrefix) {
                Route::get('/', ['uses' => $namespacePrefix.'VoyagerCompassController@index',  'as' => 'index']);
                Route::post('/', ['uses' => $namespacePrefix.'VoyagerCompassController@index',  'as' => 'post']);
            });

            event(new RoutingAdminAfter());
        });

        //Asset Routes
        Route::get('assets', ['uses' => $namespacePrefix.'VoyagerController@assets', 'as' => 'assets']);

        event(new RoutingAfter());
    });
    // Voyager::routes();
});
