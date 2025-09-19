<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeminiController extends Controller
{
    public function chat(Request $request)
    {
        $message = $request->input('message');

        try {
            // gọi Gemini API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('GEMINI_API_KEY'),
                'Content-Type'  => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent', [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $message]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Xin lỗi, tôi chưa có câu trả lời.";
            } else {
                $reply = "⚠️ Gemini trả về lỗi: " . $response->body();
            }
        } catch (\Exception $e) {
            $reply = "⚠️ Lỗi khi gọi Gemini: " . $e->getMessage();
        }

        return response()->json([
            'reply' => $reply
        ]);
    }
}
