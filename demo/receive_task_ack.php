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
    //ack确认
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

//关闭ack机制
$channel->basic_consume('my_key', '', false, false, false, false, $callback);


while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();