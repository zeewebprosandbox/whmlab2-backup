<?php

use Illuminate\Support\Facades\Route;

Route::controller('WebController')->group(function () {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/services', 'services')->name('services');
    Route::get('/services/{module}', 'serviceModule')->name('services.show');
    Route::post('/services/{module}/items', 'storeServiceItem')->name('services.items.store');
    Route::get('/websites', 'websites')->name('websites');
    Route::get('/websites/{domain}', 'website')->name('websites.show');
    Route::post('/websites/{domain}/php', 'updateWebsitePhp')->name('websites.php.update');
    Route::post('/websites/{domain}/dns/repair', 'repairWebsiteDns')->name('websites.dns.repair');
    Route::post('/websites/{domain}/ssl/enable', 'enableWebsiteSsl')->name('websites.ssl.enable');
    Route::post('/terminal/open', 'openTerminal')->name('terminal.open');
    Route::get('/sso/login', 'sso')->name('sso');
});

Route::middleware('whmpanel.api')->prefix('api/v1')->name('api.')->controller('ApiController')->group(function () {
    Route::get('/server/info', 'serverInfo')->name('server.info');
    Route::get('/server/stats', 'serverStats')->name('server.stats');
    Route::get('/services', 'services')->name('services');
    Route::get('/services/{module}', 'serviceModule')->name('services.show');
    Route::post('/services/{module}/items', 'createServiceItem')->name('services.items.create');

    Route::get('/users', 'users')->name('users');
    Route::post('/users', 'createUser')->name('users.create');
    Route::get('/users/{username}', 'showUser')->name('users.show');
    Route::post('/users/{username}/suspend', 'suspendUser')->name('users.suspend');
    Route::post('/users/{username}/unsuspend', 'unsuspendUser')->name('users.unsuspend');
    Route::delete('/users/{username}', 'deleteUser')->name('users.delete');

    Route::get('/websites', 'websites')->name('websites');
    Route::get('/domains/list', 'listDomains')->name('domains.list');
    Route::post('/websites', 'createWebsite')->name('websites.create');
    Route::get('/websites/{domain}/diagnostics', 'websiteDiagnostics')->name('websites.diagnostics');
    Route::get('/websites/{domain}/php', 'websitePhp')->name('websites.php');
    Route::post('/websites/{domain}/php', 'updateWebsitePhp')->name('websites.php.update');
    Route::post('/websites/{domain}/dns/repair', 'repairWebsiteDns')->name('websites.dns.repair');
    Route::post('/websites/{domain}/ssl/enable', 'enableWebsiteSsl')->name('websites.ssl.enable');
    Route::get('/dns/zones/{domain}/records', 'dnsRecords')->name('dns.records');
    Route::post('/dns/zones/{domain}/records', 'createDnsRecord')->name('dns.records.create');

    Route::post('/auth/sso', 'createSso')->name('auth.sso');
});
