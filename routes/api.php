<?php

use Illuminate\Http\Request;
use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Route;

Route::post('/youtube/download', [DownloadController::class, 'download']);

Route::get('/prueba', function(){
    return "klk bro";
});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
