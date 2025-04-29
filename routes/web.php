<?php

use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('badge/{template}/pdf', [TemplateController::class, 'generatePdf']);
Route::get('template/{template}/pdf', [TemplateController::class, 'generatContentPdf']);
Route::get('pages/{template}/editor', [TemplateController::class, 'editor']);
Route::get('/list/templates', [TemplateController::class, 'getTemplates']);
Route::get('/pages/{template}', [TemplateController::class, 'show'])->name('template.show');

