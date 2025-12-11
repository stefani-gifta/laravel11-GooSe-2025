<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('chat');
});

Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
