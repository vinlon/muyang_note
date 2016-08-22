<?php
/**
 * @by wenlong 该接口需要同时支持POST和GET,并且支持XML格式数据，与基于Slim的接口框架不兼容，所以使用单独的PHP文件来实现
 */

include __DIR__ . '/vendor/autoload.php'; // 引入 composer 入口文件

date_default_timezone_set('PRC'); //设置中国时区
//error handle
require "app/error_handler.php";

use EasyWeChat\Foundation\Application;
use Controller\MessageController;

$options = [
    'debug'  => DEBUG,
    'app_id' => APPID,
    'secret' => APPSECRET,
    'token'  => TOKEN,
    'aes_key' => AES_KEY, // 可选
    'log' => [
        'level' => 'debug',
        'file'  => __DIR__.'/logs/easywechat/server.log',
    ]
];

$app = new Application($options);

// $message = json_decode('{"ToUserName":"gh_a5762ca08dd6","FromUserName":"oJpdNxL7ViIFHcPKxn2cu8-yXA-w","CreateTime":"1471860154","MsgType":"image","PicUrl":"http://mmbiz.qpic.cn/mmbiz_jpg/KPR8ViaBujcicHMVzA4wRXBlr1Y4ibsc31hA87clicTtXhanQl5o01MekViaommicwicqkBJgBecPOvd3qiaRrxqB4Zxng/0","MsgId":"6321591226121660057","MediaId":"OatpMi0OGXvJcu53Zo88jWjuX7pRlrgZ0dXvvIai77IY8GpTSSJ2NSPevdXekuN_"}');
// $message_handler = new MessageController();
// var_dump($message_handler->handle($message));exit;

//接收 & 回复用户消息
$app->server->setMessageHandler(function ($message) {
    $message_handler = new MessageController();
    return $message_handler->handle($message);
});

$response = $app->server->serve();

// 将响应输出
$response->send();
