<?php

namespace App\Http\Controllers;

use App\Services\RocketChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RocketChatController extends Controller
{
    protected $rocketChatService;

    public function __construct(RocketChatService $rocketChatService)
    {
        $this->rocketChatService = $rocketChatService;
    }

    public function testConnection()
    {
        try {
            // Try to get channels list
            $channels = $this->rocketChatService->getChannels();

            return response()->json([
                'success' => true,
                'message' => 'Connection successful',
                'channels_count' => count($channels),
                'channels' => $channels
            ]);
        } catch (\Exception $e) {
            Log::error('RocketChat test connection failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searchUsers(Request $request)
    {
        try {
            $query = $request->get('query');
            $response = $this->rocketChatService->searchUsers($query);

            // Extract users from the response
            $users = $response['users'] ?? [];

            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            Log::error('RocketChat user search failed', [
                'error' => $e->getMessage(),
                'query' => $request->get('query')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getChannels()
    {
        $response = $this->rocketChatService->getChannels();
        return response()->json($response);
    }

    public function sendMessage(Request $request)
    {
        $roomId = $request->input('roomId');
        $message = $request->input('message');

        $response = $this->rocketChatService->sendMessage($roomId, $message);
        return response()->json($response);
    }
}
