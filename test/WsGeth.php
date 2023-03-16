<?php

use React\Socket\ConnectionInterface;
use Web3p\EthereumUtil\Util;
use Web3php\Address\AddressFactory;
use Web3php\Address\Utils\TronAddressUtil;

require_once __DIR__.'/../vendor/autoload.php';
$addressFactory = new AddressFactory(new Util(),new TronAddressUtil());
//$item = new \Web3php\Chain\Ethereum\Subscription\Methods\Item\AlchemyMinedTransactionsAddressItem($addressFactory->makeEthereumAddress("0x057Dc1fdA5DcF6Ff56D6991E88389cA4826775a6"),
//    $addressFactory->makeEthereumAddress("0x057Dc1fdA5DcF6Ff56D6991E88389cA4826775a6"));
//$data[] = $item;
//$data[] = $item;
//$message = new \Web3php\Chain\Ethereum\Subscription\Methods\AlchemyMinedTransactions($data);
//var_dump($message->toString());
//use Ratchet\Client\connect;
//use Ratchet\RFC6455\Messaging\MessageInterface;
\Ratchet\Client\connect('ws://ws.rpc.cn',[],[],\React\EventLoop\Loop::get())->then(function($conn) use($addressFactory){
//    var_dump(get_class($conn));
    /**
     * @var Ratchet\Client\WebSocket $conn
     */

    $addressFactory = new AddressFactory(new Util(),new TronAddressUtil());

//    $conn->send('{"jsonrpc":"2.0","method":"eth_subscribe","params":["newHeads",{}],"id":1}');
//    $conn->send(new \Ratchet\RFC6455\Messaging\Frame('{"jsonrpc":"2.0","method":"eth_subscribe","params":["newHeads"],"id":1}'));
//    $message = new \Web3php\Chain\Ethereum\Subscription\Methods\NewHeads();
//    new \Ratchet\RFC6455\Messaging\Frame('{"jsonrpc":"2.0","method":"eth_subscribe","params":["logs",{"address":"0x057Dc1fdA5DcF6Ff56D6991E88389cA4826775a6","topics":["0x8c5be1e5ebec7d5bd14f71427d1e84f3dd0314c0f7b2291e5b200ac8c7c3b925"]}],"id":2}')
//    $LogsItem = new \Web3php\Chain\Ethereum\Subscription\Methods\Item\LogsItem(["0x8c5be1e5ebec7d5bd14f71427d1e84f3dd0314c0f7b2291e5b200ac8c7c3b925"],
//    [
//        $addressFactory->makeEthereumAddress("0x057Dc1fdA5DcF6Ff56D6991E88389cA4826775a6")
//    ]);
//    $message = new \Web3php\Chain\Ethereum\Subscription\Methods\Logs($LogsItem);
    $item = new \Web3php\Chain\Ethereum\Subscription\Methods\Item\AlchemyMinedTransactionsAddressItem($addressFactory->makeEthereumAddress("0x057Dc1fdA5DcF6Ff56D6991E88389cA4826775a6"));
    $item = new \Web3php\Chain\Ethereum\Subscription\Methods\Item\AlchemyPendingTransactionsAddressItem([$addressFactory->makeEthereumAddress("0x057Dc1fdA5DcF6Ff56D6991E88389cA4826775a6")]);
//        $addressFactory->makeEthereumAddress("0x057Dc1fdA5DcF6Ff56D6991E88389cA4826775a6"));
//    $data[] = $item;
//    $data[] = $item;
//    $message = new \Web3php\Chain\Ethereum\Subscription\Methods\AlchemyMinedTransactions($data);
    $message = new \Web3php\Chain\Ethereum\Subscription\Methods\AlchemyPendingTransactions($item);
    var_dump($message->toString());
    $conn->send($message->toString());
    $conn->on('message', function($msg) use ($conn) {
//        var_dump(get_class($msg),get_class_methods($msg));
//        var_dump($msg->getPayload());
//        var_dump($msg->getContents());
        $result = json_decode($msg->getPayload());
        var_dump($result);
//        echo "Received: {$msg}\n";
//        $conn->close();
    });
    $conn->on('close', function($code = null, $reason = null) {
        echo "Connection closed ({$code} - {$reason})\n";
    });

//    $conn->send('Hello World!');
}, function ($e) {
    echo "Could not connect: {$e->getMessage()}\n";
});


////连接到Geth节点的Websocket端口
//$wsUrl = 'ws://localhost:8546';
//$webSocket = new WebSocket($wsUrl);
//
////当连接成功时执行的回调函数
//$webSocket->on('open', function (WebSocket $conn) {
//    echo "Connected to Geth node\n";
//});
//
////当收到消息时执行的回调函数
//$webSocket->on('message', function (WebSocket $from, MessageInterface $msg) {
//    $data = json_decode($msg);
//    var_dump($data);
////    if (isset($data->params)) {
////        $params = $data->params;
////        if (count($params) == 3 && $params[0] == '0xfeed' && $params[1] == 'logs') {
////            // 处理以太坊事件通知
////            $event = $params[2];
////            echo "Received event: {$event->data}\n";
////        }
////    }
//});
//
////当连接断开时执行的回调函数
//$webSocket->on('close', function ($code = null, $reason = null) {
//    echo "Disconnected from Geth node\n";
//});

//启动事件循环
//$loop = \React\EventLoop\Factory::create();
//$webSocket->connect()->then(function (WebSocket $conn) {
//    // 订阅以太坊事件通知
//    $subscribeMsg = json_encode([
//        'id' => 1,
//        'method' => 'eth_subscribe',
//        'params' => ['logs', ['address' => '0x123...']]
//    ]);
//    $conn->send($subscribeMsg);
//}, function (\Exception $e) {
//    echo "Could not connect to Geth node: {$e->getMessage()}\n";
//});
//$loop->run();