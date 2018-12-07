<?php
/**
 * Created by PhpStorm.
 * User: lemon
 * Date: 18-12-6
 * Time: 上午10:42
 */
include(__DIR__.'/config.php');

//打开一个连接
$connection = new \PhpAmqpLib\Connection\AMQPStreamConnection(HOST,PORT,USER,PASS);
//打开一个通道
$channel = $connection->channel();

$channel->exchange_declare('logs', 'fanout', false, false, false);

list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

$channel->queue_bind($queue_name, 'logs');

echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function($msg){
    echo ' [x] ', $msg->body, "\n";
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();