<?php

use App\Http\Controllers\PageController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

/*
 | The public site is one resolver: locale, page slug and detail slug are all
 | editable content, so they are matched at request time rather than baked
 | into route definitions.
 */
$reserved = implode('|', config('novera.reserved_prefixes', []));

Route::get('/{path?}', [PageController::class, 'resolve'])
    ->where('path', '^(?!(?:'.$reserved.')(?:/|$)).*$')
    ->middleware(SetLocale::class)
    ->name('nv.resolve');
