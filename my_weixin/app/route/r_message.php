<?php
/**
 * Message
 */
use Controller\MessageController;

$app->group("/message/", function(){
    $message = new MessageController();

    //注册消息处理事件
    $this->post("registHandler", function($request, $response) use ($message){
        $param = $request->getParsedBody();
        $result = $message->registHandler($param);
        return $response->withJson($result, 200, JSON_NUMERIC_CHECK);
    });
});



?>
