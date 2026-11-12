<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::view('/orders', 'orders');
Route::view('/orders/create', 'create-order');
