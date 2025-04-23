<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TemplateController;

Route::post('store/template', [TemplateController::class, 'store'])->name('store.template');

