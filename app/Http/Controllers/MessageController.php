<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Api;
use Telegram\Bot\BotsManager;
use Exception; // Add this import for Exception handling

class MessageController extends Controller
{
    protected BotsManager $botsManager;

    public function __construct(BotsManager $botsManager)
    {
        $this->botsManager = $botsManager;
    }

    public function __invoke()
    {
        try {
            $telegram = new Api(config('telegram.bot_token'));
            $update = $telegram->getWebhookUpdate();

            $this->botsManager->bot()->commandsHandler(true);

            $hasCommand = false;
            if (!empty($update->message->entities)) {
                foreach ($update->message->entities as $entity) {
                    if ($entity->type === 'bot_command') {
                        $hasCommand = true;
                        break;
                    }
                }
            }

            return response(null, 200);
        } catch (Exception $e) {
            // Handle the exception here
            // You might want to log the error or return a different response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}