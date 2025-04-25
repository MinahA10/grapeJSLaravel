<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\AssetController;

Route::get('/test-api-route', fn () => 'API route works');

Route::post('/grapesjs/assets/upload', [AssetController::class, 'upload']);
Route::delete('/grapesjs/assets/delete', [AssetController::class, 'delete']);

Route::post('/grapesjs/{template}/save', [TemplateController::class, 'save']);
Route::get('/grapesjs/{template}/load', [TemplateController::class, 'load']);
