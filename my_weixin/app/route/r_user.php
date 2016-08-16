<?php
/**
 * User
 */
use Controller\UserController;

$app->group("/user/", function(){
    $user = new UserController();

    //获取用户信息 (订阅号不支持该接口)
    $this->get("info", function($request, $response) use ($user){
        $param = $_GET;
        $result = $user->getInfo($param);
        return $response->withJson($result, 200, JSON_NUMERIC_CHECK);
    });
});



?>
