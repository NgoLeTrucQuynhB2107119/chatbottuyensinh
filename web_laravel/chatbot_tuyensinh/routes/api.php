<?php

use App\Http\Controllers\GeminiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::post('/chatbot', function (Request $request) {
    $message = $request->input('message');
    $sender  = $request->input('sender', 'user_' . uniqid());

    $response = Http::timeout(120)->post('http://127.0.0.1:5005/webhooks/rest/webhook', [
        'sender' => $sender,
        'message' => $message,
    ]);


    return response()->json($response->json());
});
Route::post('/gemini', [GeminiController::class, 'chat']);
