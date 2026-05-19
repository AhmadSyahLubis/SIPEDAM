<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Protected Admin Routes
Route::get('/admin/dashboard', function () { return view('admin.dashboard'); })->name('admin.dashboard');
Route::get('/admin/reports', function () { return view('admin.reports.index'); })->name('admin.reports');
Route::get('/admin/services', function () { return view('admin.services.index'); })->name('admin.services');
Route::get('/admin/categories', function () { return view('admin.categories.index'); })->name('admin.categories');
Route::get('/admin/users', function () { return view('admin.users.index'); })->name('admin.users');

// Protected User Routes
Route::get('/user/dashboard', function () { return view('user.dashboard'); })->name('user.dashboard');
Route::get('/user/reports', function () { return view('user.reports.index'); })->name('user.reports');
Route::get('/user/services', function () { return view('user.services.index'); })->name('user.services');
Route::get('/user/profile', function () { return view('user.profile.index'); })->name('user.profile');
