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

$channel->queue_declare('hello', false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function($msg) {
    echo " [x] Received ", $msg->body, "\n";
};

$channel->basic_consume('hello', '', false, true, false, false, $callback);

//代码块循环 通道（$channel ）的回调。无论什么时候我们收到消息我们的回调函数（$callback）将传递给接收的消息。
while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();