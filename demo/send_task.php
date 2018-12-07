<?php
include(__DIR__.'/config.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection(HOST,PORT,USER,PASS);
$channel = $connection->channel();

//定义持久化的队列
$channel->queue_declare('my_key',false,true,false,false);

$data = implode(' ',array_slice($argv,1));

if (empty($data)) $data = 'hello world';

$msg = new \PhpAmqpLib\Message\AMQPMessage($data,[
    'delivery_mode'=>\PhpAmqpLib\Message\AMQPMessage::DELIVERY_MODE_PERSISTENT //消息持久化
]);

$channel->basic_publish($msg,'','my_key');

echo " [x] Sent ", $data, "\n";

/*
 * 将消息设为持久化并不能完全保证不会丢失。以上代码只是告诉了RabbitMq要把消息存到硬盘，但从RabbitMq收到消息到保存之间还是有一个很小的间隔时间。
 * 因为RabbitMq并不是所有的消息都使用fsync(2)——它有可能只是保存到缓存中，并不一定会写到硬盘中。并不能保证真正的持久化，但已经足够应付我们的简单工作队列。
 * 如果你一定要保证持久化，你可以使用publisher confirms。
 */
$channel->close();
$connection->close();