<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected Client $client;
    protected string $model;
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY', '');
        $this->model = env('OPENAI_MODEL', 'gpt-4o-mini');
        
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers'  => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ],
            'timeout'  => 60,
        ]);
    }

    public function summarizeText(string $text): string
    {
        return $this->chat([
            ['role' => 'system', 'content' => 'You are an expert document summarizer. Summarize the text concisely.'],
            ['role' => 'user',   'content' => "Summarize the following document in 3-5 concise paragraphs:\n\n{$text}"],
        ]);
    }

    public function extractKeywords(string $text): array
    {
        $response = $this->chat([
            ['role' => 'system', 'content' => 'You are a keyword extraction specialist. Return ONLY a JSON array of strings.'],
            ['role' => 'user',   'content' => "Extract the 10 most relevant keywords from this document. Return ONLY a JSON array of strings, no other explanation:\n\n{$text}"],
        ]);
        
        // Try to parse JSON. Sometimes OpenAI returns markdown code blocks like ```json ... ```
        $response = preg_replace('/```json\s*(.*?)\s*```/s', '$1', $response);
        $keywords = json_decode(trim($response), true);
        
        return is_array($keywords) ? $keywords : [];
    }

    public function analyzeSentiment(string $text): array
    {
        $response = $this->chat([
            ['role' => 'system', 'content' => 'You are a sentiment analysis expert. Return ONLY valid JSON with keys "label" (positive/negative/neutral) and "score" (0.0 to 1.0).'],
            ['role' => 'user',   'content' => "Analyze the sentiment of this document. Return JSON:\n\n{$text}"],
        ]);

        $response = preg_replace('/```json\s*(.*?)\s*```/s', '$1', $response);
        $data = json_decode(trim($response), true);

        if (is_array($data) && isset($data['label']) && isset($data['score'])) {
            return $data;
        }

        return ['label' => 'neutral', 'score' => 0.5];
    }

    public function generateLinkedInPost(string $text): string
    {
        return $this->chat([
            ['role' => 'system', 'content' => 'You are a professional LinkedIn content creator.'],
            ['role' => 'user',   'content' => "Based on this document, generate an engaging LinkedIn post (max 300 words) with relevant hashtags:\n\n{$text}"],
        ]);
    }

    private function chat(array $messages): string
    {
        if (empty($this->apiKey) || $this->apiKey === 'your_openai_api_key_here') {
            Log::warning('OpenAI API key is missing or invalid.');
            return "Simulated AI response (API key missing).";
        }

        try {
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model'       => $this->model,
                    'messages'    => $messages,
                    'temperature' => 0.7,
                    'max_tokens'  => 1000,
                ],
            ]);
            $data = json_decode($response->getBody(), true);
            return trim($data['choices'][0]['message']['content'] ?? '');
        } catch (\Exception $e) {
            Log::error('OpenAI API Error: ' . $e->getMessage());
            throw new \Exception('Failed to communicate with AI provider.');
        }
    }
}
