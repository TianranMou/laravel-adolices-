<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use League\HTMLToMarkdown\HtmlConverter;

class RocketChatService
{
    protected $baseUrl;
    protected $userId;
    protected $authToken;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.rocketchat.url'), '/');
        $this->userId = config('services.rocketchat.user_id');
        $this->authToken = config('services.rocketchat.auth_token');

        if (empty($this->baseUrl)) {
            Log::error('RocketChat URL is not configured');
            throw new \RuntimeException('RocketChat URL is not configured');
        }

        if (empty($this->userId) || empty($this->authToken)) {
            Log::error('RocketChat credentials are not configured');
            throw new \RuntimeException('RocketChat credentials are not configured');
        }
    }

    protected function getHeaders()
    {
        return [
            'X-User-Id' => $this->userId,
            'X-Auth-Token' => $this->authToken,
            'Content-Type' => 'application/json',
        ];
    }

    public function getChannels()
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/api/v1/channels.list");

            if (!$response->successful()) {
                Log::error('RocketChat API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => "{$this->baseUrl}/api/v1/channels.list"
                ]);
                throw new \RuntimeException('Failed to fetch RocketChat channels');
            }

            $data = $response->json();

            // Return the channels array from the response
            return $data['channels'] ?? [];
        } catch (\Exception $e) {
            Log::error('RocketChat API exception', [
                'message' => $e->getMessage(),
                'url' => "{$this->baseUrl}/api/v1/channels.list"
            ]);
            throw $e;
        }
    }

    public function getDirectMessages()
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/api/v1/im.list");

            if (!$response->successful()) {
                Log::error('RocketChat API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => "{$this->baseUrl}/api/v1/im.list"
                ]);
                throw new \RuntimeException('Failed to fetch RocketChat direct messages');
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('RocketChat API exception', [
                'message' => $e->getMessage(),
                'url' => "{$this->baseUrl}/api/v1/im.list"
            ]);
            throw $e;
        }
    }

    public function sendMessage($roomId, $message)
    {
        $markdownMessage = $this->convertHtmlToMarkdown($message);
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/api/v1/chat.postMessage", [
                    'roomId' => $roomId,
                    'text' => $markdownMessage,
                ]);

            if (!$response->successful()) {
                Log::error('RocketChat API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => "{$this->baseUrl}/api/v1/chat.postMessage"
                ]);
                throw new \RuntimeException('Failed to send RocketChat message');
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('RocketChat API exception', [
                'message' => $e->getMessage(),
                'url' => "{$this->baseUrl}/api/v1/chat.postMessage"
            ]);
            throw $e;
        }
    }

    public function createDirectMessage($username)
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/api/v1/im.create", [
                    'username' => $username,
                ]);

            if (!$response->successful()) {
                Log::error('RocketChat API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => "{$this->baseUrl}/api/v1/im.create"
                ]);
                throw new \RuntimeException('Failed to create RocketChat direct message');
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('RocketChat API exception', [
                'message' => $e->getMessage(),
                'url' => "{$this->baseUrl}/api/v1/im.create"
            ]);
            throw $e;
        }
    }

    public function searchUsers($query)
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/api/v1/users.list", [
                    'query' => json_encode(['username' => ['$regex' => $query]]),
                ]);

            if (!$response->successful()) {
                Log::error('RocketChat API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => "{$this->baseUrl}/api/v1/users.list"
                ]);
                throw new \RuntimeException('Failed to search RocketChat users');
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('RocketChat API exception', [
                'message' => $e->getMessage(),
                'url' => "{$this->baseUrl}/api/v1/users.list"
            ]);
            throw $e;
        }
    }

    /**
     * Create direct message and send message in one step
     *
     * @param string $username
     * @param string $message
     * @return array
     */
    public function createAndSendDirectMessage($username, $message)
    {
        // First create/get the direct message channel
        $dmResponse = $this->createDirectMessage($username);

        // Extract the room ID
        $roomId = $dmResponse['room']['_id'] ?? null;

        if (!$roomId) {
            Log::error('Failed to get room ID from direct message creation', [
                'username' => $username,
                'response' => $dmResponse
            ]);
            throw new \RuntimeException('Failed to get room ID for direct message');
        }

        // Now send the message to that room - no need to convert here as sendMessage will do it
        return $this->sendMessage($roomId, $message);
    }

    protected function convertHtmlToMarkdown($html)
    {
        $converter = new HtmlConverter();
        $converter->getConfig()->setOption('strip_tags', true);
        $converter->getConfig()->setOption('use_autolinks', false);

        // Convert HTML to Markdown
        $markdown = $converter->convert($html);

        // Clean up any remaining HTML entities
        $markdown = html_entity_decode($markdown);

        return $markdown;
    }
}
