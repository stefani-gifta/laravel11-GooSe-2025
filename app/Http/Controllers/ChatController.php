<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'oneLineMode' => 'boolean'
        ]);

        // TODO: Integrate with the model here
        // for now, its just a dummy response
        
        return response()->json([
            'success' => true,
            'response' => 'This is a dummy response. Integrate your AI API here!'
        ]);
    }
}