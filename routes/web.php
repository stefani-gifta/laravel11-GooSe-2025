<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController; // <--- TAMBAHKAN BARIS INI (Wajib!)

Route::get('/', function () {
    return view('chat'); // Pastikan file resources/views/chat.blade.php ada
});

// Route ini sudah benar
Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');