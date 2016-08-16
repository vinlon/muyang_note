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
            'REGISTERED' => 10,
            'APPROVED' => 20
        ];
    }


    /**
     * 检查用户状态
     * @param  string $openid 用户标识
     * @return int status
     */
    public function checkStatus($openid){
        $redis_user_key = 'user:' . $openid;
        $status = $this->redis1->hget($redis_user_key, 'status');
        if($status === NULL){
            //匿名用户
            return $this->user_status['STRANGER'];
        }
        return array_search($status, $this->user_status);
    }
}


?>