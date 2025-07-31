<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use Exception;

class TelegramBotService
{
    private string $botToken;
    private string $apiUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}/";

        if (empty($this->botToken)) {
            throw new Exception('Telegram bot token not configured');
        }
    }

    /**
     * Telegram orqali xabar yuborish
     *
     * @param string|int $chatId
     * @param string $message
     * @param string $parseMode
     * @return array|null
     */
    public function send($chatId, string $message, string $parseMode = 'HTML'): ?array
    {
        try {
            // Uzun xabarlarni bo'laklarga ajratish (Telegram 4096 belgi cheklovi)
            $chunks = str_split($message, 4096);
            $results = [];

            foreach ($chunks as $chunk) {
                $response = Http::timeout(30)->post($this->apiUrl . 'sendMessage', [
                    'chat_id' => $chatId,
                    'text' => $chunk,
                    'parse_mode' => $parseMode
                ]);

                if ($response->successful()) {
                    $results[] = $response->json();
                    Log::info('Telegram message sent successfully', [
                        'chat_id' => $chatId,
                        'message_length' => strlen($chunk)
                    ]);
                } else {
                    Log::error('Failed to send telegram message', [
                        'chat_id' => $chatId,
                        'error' => $response->body(),
                        'status' => $response->status()
                    ]);
                    return null;
                }

                // Spam oldini olish uchun kichik pauza
                if (count($chunks) > 1) {
                    usleep(100000); // 0.1 sekund
                }
            }

            return $results;

        } catch (Exception $e) {
            Log::error('Telegram send message exception', [
                'chat_id' => $chatId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Telegram bot webhook dan kelgan xabarni qayta ishlash
     *
     * @param array $message
     * @return bool
     */
    public function handleWebhook(array $message): bool
    {
        try {
            if (!isset($message['chat']['id'])) {
                Log::warning('Invalid telegram message format', ['message' => $message]);
                return false;
            }

            $chatId = $message['chat']['id'];

            if (!isset($message['text'])) {
                Log::info('Non-text message received', ['chat_id' => $chatId]);
                return true;
            }

            $text = $message['text'];
            $user = $message['from'] ?? [];

            Log::info('Telegram message received', [
                'chat_id' => $chatId,
                'user_id' => $user['id'] ?? null,
                'username' => $user['username'] ?? null,
                'text' => $text
            ]);

            // Komandalarni qayta ishlash
            if (str_starts_with($text, '/start')) {
                $welcomeMessage = $this->getWelcomeMessage($text, $user);
                return $this->send($chatId, $welcomeMessage) !== null;
            } else {
                $responseMessage = $this->getChatIdMessage($chatId, $user);
                return $this->send($chatId, $responseMessage) !== null;
            }

        } catch (Exception $e) {
            Log::error('Telegram webhook handling error', [
                'error' => $e->getMessage(),
                'message' => $message
            ]);
            return false;
        }
    }

    /**
     * Telegram API dan yangilanishlarni olish (polling uchun)
     *
     * @param int $offset
     * @param int $limit
     * @param int $timeout
     * @return array|null
     */
    public function getUpdates(int $offset = 0, int $limit = 100, int $timeout = 60): ?array
    {
        try {
            $response = Http::timeout($timeout + 10)->get($this->apiUrl . 'getUpdates', [
                'offset' => $offset,
                'limit' => $limit,
                'timeout' => $timeout
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Failed to get telegram updates', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return null;

        } catch (Exception $e) {
            Log::error('Telegram getUpdates exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Polling rejimini ishga tushirish (local development uchun)
     *
     * @return void
     */
    public function startPolling(): void
    {
        $offset = 0;

        echo "Telegram bot polling started...\n";
        echo "Bot username: " . ($this->getMe()['result']['username'] ?? 'Unknown') . "\n";
        echo "Press Ctrl+C to stop\n\n";

        while (true) {
            try {
                $updates = $this->getUpdates($offset, 100, 1);

                if ($updates && isset($updates['result']) && !empty($updates['result'])) {
                    foreach ($updates['result'] as $update) {
                        if (isset($update['message'])) {
                            echo "Processing message from chat: " . $update['message']['chat']['id'] . "\n";
                            $this->handleWebhook($update['message']);
                        }

                        $offset = $update['update_id'] + 1;
                    }
                }

                // CPU ni ortiqcha yuklamaslik uchun
                usleep(100000); // 0.1 sekund

            } catch (Exception $e) {
                Log::error('Polling error', ['error' => $e->getMessage()]);
                echo "Error: " . $e->getMessage() . "\n";
                sleep(5); // Xato bo'lsa 5 sekund kutish
            }
        }
    }

    /**
     * Webhook ni o'chirish (polling uchun)
     *
     * @return bool
     */
    public function deleteWebhook(): bool
    {
        try {
            $response = Http::post($this->apiUrl . 'deleteWebhook');

            if ($response->successful()) {
                Log::info('Telegram webhook deleted successfully');
                return true;
            }

            Log::error('Failed to delete telegram webhook', [
                'response' => $response->body()
            ]);
            return false;

        } catch (Exception $e) {
            Log::error('Delete webhook exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Webhook o'rnatish
     *
     * @param string $webhookUrl
     * @return bool
     */
    public function setWebhook(string $webhookUrl): bool
    {
        try {
            $response = Http::post($this->apiUrl . 'setWebhook', [
                'url' => $webhookUrl
            ]);

            if ($response->successful()) {
                Log::info('Telegram webhook configured successfully', ['url' => $webhookUrl]);
                return true;
            }

            Log::error('Failed to set telegram webhook', [
                'url' => $webhookUrl,
                'response' => $response->body()
            ]);
            return false;

        } catch (Exception $e) {
            Log::error('Telegram setWebhook exception', [
                'url' => $webhookUrl,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Bot ma'lumotlarini olish
     *
     * @return array|null
     */
    public function getMe(): ?array
    {
        try {
            $response = Http::get($this->apiUrl . 'getMe');

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (Exception $e) {
            Log::error('Telegram getMe exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Xush kelibsiz xabarini yaratish
     *
     * @param string $command
     * @param array $user
     * @return string
     */
    private function getWelcomeMessage(string $command, array $user): string
    {
        $firstName = $user['first_name'] ?? 'Foydalanuvchi';

        return "Assalomu alaykum, {$firstName}! 👋\n\n" .
               "Sizning komandangiz: <code>{$command}</code>\n\n" .
               "Bot muvaffaqiyatli ishga tushdi. Yordam kerak bo'lsa, /help buyrug'ini yuboring.";
    }

    /**
     * Chat ID ko'rsatuvchi xabarni yaratish
     *
     * @param string|int $chatId
     * @param array $user
     * @return string
     */
    private function getChatIdMessage($chatId, array $user): string
    {
        $firstName = $user['first_name'] ?? 'Foydalanuvchi';

        return "Assalomu alaykum, {$firstName}! 👋\n\n" .
               "Sizning Chat ID: <code>{$chatId}</code>\n\n" .
               "Bu ID ni saqlang, u orqali sizga bildirishnomalar yuboriladi.";
    }

    /**
     * Xabar yuborish (fayllar bilan)
     *
     * @param string|int $chatId
     * @param string $message
     * @param array $options
     * @return array|null
     */
    public function sendMessage($chatId, string $message, array $options = []): ?array
    {
        $data = array_merge([
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML'
        ], $options);

        try {
            $response = Http::timeout(30)->post($this->apiUrl . 'sendMessage', $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Failed to send telegram message', [
                'chat_id' => $chatId,
                'error' => $response->body()
            ]);
            return null;

        } catch (Exception $e) {
            Log::error('Telegram sendMessage exception', [
                'chat_id' => $chatId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
