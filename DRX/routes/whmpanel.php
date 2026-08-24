<?php

use Illuminate\Support\Facades\Route;

Route::controller('WebController')->group(function () {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/sso/login', 'sso')->name('sso');
});

Route::middleware('whmpanel.api')->prefix('api/v1')->name('api.')->controller('ApiController')->group(function () {
    Route::get('/server/info', 'serverInfo')->name('server.info');
    Route::get('/server/stats', 'serverStats')->name('server.stats');

    Route::get('/users', 'users')->name('users');
    Route::post('/users', 'createUser')->name('users.create');
    Route::get('/users/{username}', 'showUser')->name('users.show');
    Route::post('/users/{username}/suspend', 'suspendUser')->name('users.suspend');
    Route::post('/users/{username}/unsuspend', 'unsuspendUser')->name('users.unsuspend');
    Route::delete('/users/{username}', 'deleteUser')->name('users.delete');

    Route::get('/websites', 'websites')->name('websites');
    Route::post('/websites', 'createWebsite')->name('websites.create');
    Route::get('/dns/zones/{domain}/records', 'dnsRecords')->name('dns.records');
    Route::post('/dns/zones/{domain}/records', 'createDnsRecord')->name('dns.records.create');

    Route::post('/auth/sso', 'createSso')->name('auth.sso');
});
