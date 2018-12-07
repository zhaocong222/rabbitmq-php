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

//命名的交换机
$channel->exchange_declare('logs','fanout',false,false,false);

//消息主体
$data = implode(' ',array_slice($argv,1));
if (empty($data)) $data = 'info: hello world!';

$msg = new \PhpAmqpLib\Message\AMQPMessage($data);

$channel->basic_publish($msg,'logs');

function shutdown($channel, $connection){
    $channel->close();
    $connection->close();
}

register_shutdown_function('shutdown',$channel,$connection);

echo " [x] Sent ".$data."\n";