<?php

use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

include __DIR__ . '/vendor/autoload.php';

$env = Dotenv::createImmutable(__DIR__);
$env->load();
$env->required(['DISCORD_TOKEN', 'BOT_USERNAME'])->notEmpty();

$discord = new Discord([
    'token' => $_ENV['DISCORD_TOKEN'],
    'logger' => (new Logger('debug'))->pushHandler(
        new StreamHandler('debug.log', Logger::DEBUG)
    ),
]);

$discord->on('ready', function (Discord $discord) {
    echo "บอทพร้อมแล้ว!", PHP_EOL;

    // Listen for messages.
    $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
        // Not react on self message
        if (($user = $message->author)->username === $_ENV['BOT_USERNAME']) {
            return;
        }

        $channel = $discord->getChannel($message->channel_id);

        if (strpos($message->content, 'บอท') !== false) {
            $channel->sendMessage(
                MessageBuilder::new()->setContent("ว่าไงครับ <@$user->id>")
            );
        }
    });
});

$discord->run();
