<?php
/**
 * Note 备忘录
 */
use Controller\NoteController;

$app->group('/handle/', function(){
    $note = new NoteController();

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

$app->group('/note/', function(){
    $note = new NoteController();

    //获取文本日志列表
    $this->post('textList', function($request, $response) use ($note){
        $param = $request->getParsedBody();
        $result = $note->getTextList($param);
        return $response->withJson($result, 200, JSON_NUMERIC_CHECK);
    });

    //获取所有用户的最新动态
    $this->get('latest', function($request, $response) use ($note){
        $param = $_GET;
        $result = $note->getLatest($param);
        return $response->withJson($result, 200, JSON_NUMERIC_CHECK);
    });

    //删除记录
     $this->post('delete', function($request, $response) use ($note){
        $param = $request->getParsedBody();
        $result = $note->delete($param);
        return $response->withJson($result, 200, JSON_NUMERIC_CHECK);
    });
});


?>
