<?php

use Illuminate\Http\Request;


Route::get('version', function () {
    return response()->json(['version' => config('app.version')]);
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    Log::debug('User:' . serialize($request->user()));
    return $request->user();
});


Route::get('profile', 'API\V1\ProfileController@profile');
Route::put('profile', 'API\V1\ProfileController@updateProfile');
Route::post('change-password', 'API\V1\ProfileController@changePassword');
Route::get('tag/list', 'API\V1\TagController@list');
Route::get('size/list', 'API\V1\SizeController@list');
Route::get('category/list', 'API\V1\CategoryController@list');
Route::post('product/upload', 'API\V1\ProductController@upload');
Route::get('sub_category/list', 'API\V1\SubCategoryController@list');
Route::get('faq/list', 'API\V1\FaqController@list');
Route::get('social_link/list', 'API\V1\SocialLinkController@list');
Route::get('product/sub_category', 'API\V1\ProductController@get_sub_category');
Route::get('client/list', 'API\V1\ClientController@list');
Route::get('banner/list', 'API\V1\BannerController@list');
Route::get('video/list', 'API\V1\VideoController@list');
Route::get('gallery/list', 'API\V1\GalleryController@list');
Route::get('slider/list', 'API\V1\SliderController@list');
Route::post('footer/remove', 'API\V1\FooterController@removeSubFooter');

Route::apiResources([
    'user' => 'API\V1\UserController',
    'product' => 'API\V1\ProductController',
    'category' => 'API\V1\CategoryController',
    'sub_category' => 'API\V1\SubCategoryController',
    'tag' => 'API\V1\TagController',
    'size' => 'API\V1\SizeController',
    'faq' => 'API\V1\FaqController',
    'social_link' => 'API\V1\SocialLinkController',
    'client' => 'API\V1\ClientController',
    'banner' => 'API\V1\BannerController',
    'video' => 'API\V1\VideoController',
    'gallery' => 'API\V1\GalleryController',
    'slider' => 'API\V1\SliderController',
    'company' => 'API\V1\CompanyController',
    'footer' => 'API\V1\FooterController',
]);
