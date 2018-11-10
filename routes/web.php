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

Route::get('/', function () {
    return view('/view');
    //return route('');
});

//商户的信息路由
//Route::get('shop/show','ShopController@show')->name('shop.show');
//Route::get('shop/index','ShopController@index')->name('shop.index');
Route::resource('shop','ShopController');

//会员表members的路由
Route::post('regmem/register','RegmemController@register')->name('regmem.register');
Route::post('regmem/login','RegmemController@login')->name('regmem.login');
Route::post('regmem/forgetPassword','RegmemController@forgetPassword')->name('regmem.forgetPassword');
Route::post('regmem/changePassword','RegmemController@changePassword')->name('regmem.changePassword');


Route::get('regmem/tel','RegmemController@tel')->name('regmem.tel');
Route::get('regmem/send','RegmemController@send')->name('regmem.send');


//Route::resource('regmem','RegmemController');

//添加地址模块栏
Route::post('address/saveadd','AddressController@saveadd')->name('address.saveadd');
Route::get('address/list','AddressController@list')->name('address.list');
Route::get('address/change','AddressController@change')->name('address.change');
Route::post('address/savechange','AddressController@savechange')->name('address.savechange');


//购物车模块所需节点
Route::post('cart/saveadd','CartController@saveadd')->name('cart.saveadd');
Route::get('cart/list','CartController@list')->name('cart.list');

//orders订单表的所需节点

//订单下单成功，发送邮件给用户
Route::get('order/message','OrderController@message')->name('order.message');

Route::post('order/shopsave','OrderController@shopsave')->name('order.shopsave');
Route::get('order/list','OrderController@list')->name('order.list');
Route::get('order/orderlist','OrderController@orderlist')->name('order.orderlist');



