<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // PENTING: Untuk kirim request ke Python

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'message' => 'required|string',
        ]);

        $userMessage = $request->input('message');

        try {
            // 2. KIRIM PESAN KE SERVER PYTHON (Port 5000)
            // Ini adalah "jembatan" koneksinya
            $response = Http::post('http://127.0.0.1:5000/predict', [
                'message' => $userMessage,
            ]);

            // 3. Cek Respon
            if ($response->successful()) {
                // Ambil jawaban dari JSON Python {'reply': '...'}
                $botReply = $response->json()['reply'] ?? 'Error: AI tidak memberikan jawaban.';

                return response()->json([
                    'success' => true,
                    'response' => $botReply
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'response' => 'Error: Gagal menghubungi AI (Status: ' . $response->status() . ')'
                ], 500);
            }

        } catch (\Exception $e) {
            // Jika server Python mati
            return response()->json([
                'success' => false,
                'response' => 'Server AI belum dinyalakan. Pastikan jalankan "python api.py"!'
            ], 500);
        }
    }
}