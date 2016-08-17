<?php
/**
 * 用户
 */
use Controller\UserController;

$app->group('/user/', function(){
    $user = new UserController();

    //设置用户名称
    $this->post('setName', function($request, $response) use ($user){
        $param = $request->getParsedBody();
        $result = $user->setName($param);
        return $response->withJson($result, 200, JSON_NUMERIC_CHECK);
    });


});


$app->group('/muyang/', function(){
    $user = new UserController();

    //设置沐阳的个人信息
    $this->post('setInfo', function($request, $response) use ($user){
        $param = $request->getParsedBody();
        $result = $user->setMuYangProfile($param);
        return $response->withJson($result, 200, JSON_NUMERIC_CHECK);
    });

    //获取沐阳的个人信息
    $this->get('info', function($request, $response) use ($user){
        $param = $_GET;
        $result = $user->getMuYangProfile($param);
        return $response->withJson($result, 200, JSON_NUMERIC_CHECK);
    });

    //Get Age
    $this->get('age', function($request, $response) use ($user){
        $param = $_GET;
        $result = $user->getMuYangAge($param);
        return $response->withJson($result, 200, JSON_NUMERIC_CHECK);
    });
})



?>
