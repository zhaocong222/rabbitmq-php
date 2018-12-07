<?php
/**
 * Created by PhpStorm.
 * User: lemon
 * Date: 18-12-6
 * Time: 上午10:40
 */
include(__DIR__.'/config.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection(HOST,PORT,USER,PASS);
$channel = $connection->channel();

$channel->queue_declare('hello',false,false,false,false);

$msg = new \PhpAmqpLib\Message\AMQPMessage('Hello World!');

//交换机参数为空 -》 消息会发到  和 routing_key名 完全匹配的 队列上
$channel->basic_publish($msg,'','hello');

function shutdown($channel, $connection){
    $channel->close();
    $connection->close();
}

register_shutdown_function('shutdown',$channel,$connection);

echo " [x] Sent 'Hello World!'\n";