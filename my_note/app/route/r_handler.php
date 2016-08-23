<?php
/**
 * Note 备忘录
 */
use Controller\MessageHandler;

$app->group('/handle/', function(){
    $note = new MessageHandler();

    //处理文本信息
    $this->post('text', function($request, $response) use ($note){
        $param = $request->getParsedBody();
        $result = $note->handleText($param);
        return $response->withJson($result, 200, JSON_NUMERIC_CHECK);
    });
    //处理图片信息
    $this->post('image', function($request, $response) use ($note){
        $param = $request->getParsedBody();
        $result = $note->handleImage($param);
        return $response->withJson($result, 200, JSON_NUMERIC_CHECK);
    });


});


?>
