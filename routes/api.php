<?php

use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\UserCollectionController;
use Illuminate\Support\Facades\Route;

// Rubrik routing API: seluruh endpoint demo berada di routes/api.php.
Route::apiResource('media', MediaController::class)->parameters(['media' => 'media']);
Route::get('users/{user}/collection', [UserCollectionController::class, 'index']);
Route::post('users/{user}/collection/{media}', [UserCollectionController::class, 'store']);
Route::delete('users/{user}/collection/{media}', [UserCollectionController::class, 'destroy']);
