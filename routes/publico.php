<?php

use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::middleware('cache.publico')->group(function () {
    Route::get('/', [PortfolioController::class, 'index'])->name('portfolio.inicio');
    Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
});
