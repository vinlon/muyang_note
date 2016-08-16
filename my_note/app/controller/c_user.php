<?php  
/**
 * Note User
 */

namespace Controller;

/**
* 用户管理
*/
class UserController extends BaseController{

    
    /**
     * 构造函数
     */
    public function __construct(){
        $this->redis1 = $this->getRedis(1);
        $this->user_status = [
            'STRANGER' => -10,
            'ANONYMOUS' => 0,
            'APPROVED' => 10
        ];
    }


    /**
     * 检查用户状态
     * @param  string $openid 用户标识
     * @return int status
     */
    public function checkStatus($openid, $content = ''){
        $redis_user_key = 'user:' . $openid;
        $status = $this->redis1->hget($redis_user_key, 'status');
        if($status === NULL){
            //匿名用户
            $this->redis1->hset($redis_user_key, 'status', $this->user_status['ANONYMOUS']);
            $this->redis1->hset($redis_user_key, 'comment', $content);
            return $this->user_status['STRANGER'];
        }
        return intval($status);
    }

    /**
     * 通过用户申请
     * @param  array $param [ openid \ name]
     */
    public function setName($param){
        //身份验证
        $this->authenticate();

        //检查参数
        $this->checkParam(['openid', 'name'], $param);

        $openid = $param['openid'];
        $name = $param['name'];

        $redis_user_key = 'user:' . $openid;
        //更新用户信息
        $this->redis1->hset($redis_user_key, 'status', $this->user_status['APPROVED']);
        $this->redis1->hset($redis_user_key, 'name', $name);

        return $this->success();
    }
}


?>