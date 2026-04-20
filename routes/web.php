<?php

$router->get('/', 'HomeController@index');
$router->get('/about', 'HomeController@about');
$router->get('/warranty', 'HomeController@warranty');
$router->get('/shop', 'ProductController@index');
$router->get('/product', 'ProductController@show');

$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

$router->get('/cart', 'CartController@index');
$router->post('/cart/add', 'CartController@add');
$router->post('/cart/update', 'CartController@update');
$router->post('/cart/remove', 'CartController@remove');

$router->get('/checkout', 'CheckoutController@index');
$router->post('/checkout', 'CheckoutController@store');
$router->post('/checkout/voucher-preview', 'CheckoutController@voucherPreview');

$router->get('/profile', 'AccountController@profile');
$router->post('/profile', 'AccountController@updateProfile');
$router->get('/orders', 'AccountController@orders');
$router->get('/order', 'AccountController@orderDetail');
$router->get('/after-sales', 'AccountController@afterSales');
$router->post('/after-sales', 'AccountController@submitAfterSales');

$router->get('/admin', 'AdminController@dashboard');
$router->get('/admin/customers', 'AdminController@customers');
$router->get('/admin/users/create', 'AdminUserController@create');
$router->post('/admin/users/create', 'AdminUserController@store');
$router->get('/admin/users/edit', 'AdminUserController@edit');
$router->post('/admin/users/update', 'AdminUserController@update');
$router->post('/admin/users/delete', 'AdminUserController@delete');
$router->get('/admin/promotions', 'AdminPromotionController@index');
$router->get('/admin/promotions/create', 'AdminPromotionController@create');
$router->post('/admin/promotions/create', 'AdminPromotionController@store');
$router->get('/admin/reports', 'AdminController@reports');
$router->get('/admin/products', 'AdminProductPageController@index');
$router->get('/admin/products/create', 'AdminProductPageController@create');
$router->get('/admin/products/edit', 'AdminProductPageController@edit');
$router->post('/admin/products', 'AdminProductPageController@store');
$router->post('/admin/products/create', 'AdminProductPageController@store');
$router->post('/admin/products/update', 'AdminProductPageController@update');
$router->post('/admin/products/delete', 'AdminProductPageController@delete');
$router->get('/admin/orders', 'AdminOrderController@index');
$router->post('/admin/orders/status', 'AdminOrderController@updateStatus');
$router->get('/admin/refunds', 'AdminRefundController@index');
$router->post('/admin/refunds/update', 'AdminRefundController@update');
