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



?>
