<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //排除不需要token的路由
        //用户注册关联模块
        'regmem.store','regmem.tel','/regmem/register','/regmem/login','/regmem/forgetPassword','/regmem/changePassword',
        //地址模块
        '/address/saveadd','/address/list','/address/change','/address/savechange',
        //购物车模块
        '/cart/saveadd',
        //订单orders表模块
        '/order/shopsave'
    ];
}
