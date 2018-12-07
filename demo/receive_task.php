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

$channel->queue_declare('my_key', false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function($msg) {
    echo " [x] Received ", $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done", "\n";
};

//公平调度
/*
 * 使用basic.qos方法，并设置prefetch_count=1。这样是告诉RabbitMQ，再同一时刻，不要发送超过1条消息给一个工作者（worker），
 * 直到它已经处理了上一条消息并且作出了响应。这样，RabbitMQ就会把消息分发给下一个空闲的工作者（worker）。
 */
$channel->basic_qos(null, 1, null);
//关闭ack机制
$channel->basic_consume('my_key', '', false, true, false, false, $callback);


while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();