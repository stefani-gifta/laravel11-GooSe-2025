<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        // 1. Validate Input
        $request->validate([
            'message' => 'required|string',
        ]);

        $userMessage = $request->input('message');

        try {
            // 2. SEND MESSAGE TO PYTHON SERVER (Port 5000)
            // This acts as the "bridge" connection
            $response = Http::post('http://127.0.0.1:5000/predict', [
                'message' => $userMessage,
            ]);

            // 3. Check Response
            if ($response->successful()) {
                // Get the answer from Python JSON {'reply': '...'}
                $botReply = $response->json()['reply'] ?? 'Error: AI did not provide a reply.';

                return response()->json([
                    'success' => true,
                    'response' => $botReply
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'response' => 'Error: Failed to contact AI (Status: ' . $response->status() . ')'
                ], 500);
            }

        } catch (\Exception $e) {
            // If the Python server is down
            return response()->json([
                'success' => false,
                'response' => 'AI server is not running. Make sure to run "python api.py"!'
            ], 500);
        }
    }
}