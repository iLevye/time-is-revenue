<?php

namespace App\Services;

use Http\Adapter\Guzzle6\Client;
use Http\Factory\Guzzle\RequestFactory;
use Http\Factory\Guzzle\StreamFactory;
use TgBotApi\BotApiBase\ApiClient;
use TgBotApi\BotApiBase\BotApi;
use TgBotApi\BotApiBase\BotApiNormalizer;
use TgBotApi\BotApiBase\Method\SendMessageMethod;


class Telegram {

    function sendMessage($message, $chatId, $botKey){

        $requestFactory = new RequestFactory();
        $streamFactory = new StreamFactory();
        $client = new Client();

        $apiClient = new ApiClient($requestFactory, $streamFactory, $client);
        $bot = new BotApi($botKey, $apiClient, new BotApiNormalizer());

        $bot->send(SendMessageMethod::create($chatId, $message));

    }
}