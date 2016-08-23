<?php
/**
 * Note User
 */

namespace Controller;

/**
* 用户管理
*/
class UserController extends BaseController{
    const REDIS_MUYANG_KEY = 'muyang';
    const REDIS_USER_PREFIX = 'user:';
    const REDIS_MUYANG_BIRTHDAY_FIELD = 'birthday';
    /**
     * 构造函数
     */
    public function __construct(){
        $this->redis = $this->getRedis();
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
        $redis_user_key = self::REDIS_USER_PREFIX . $openid;
        $status = $this->redis->hget($redis_user_key, 'status');
        if($status === NULL){
            //匿名用户
            $this->redis->hset($redis_user_key, 'status', $this->user_status['ANONYMOUS']);
            $this->redis->hset($redis_user_key, 'comment', $content);
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

        $redis_user_key = self::REDIS_USER_PREFIX . $openid;
        //更新用户信息
        $this->redis->hset($redis_user_key, 'status', $this->user_status['APPROVED']);
        $this->redis->hset($redis_user_key, 'name', $name);

        return $this->success();
    }

    /**
     * 查询用户名称
     */
    public function getName($openid){
        $redis_user_key = self::REDIS_USER_PREFIX . $openid;
        $name = $this->redis->hget($redis_user_key, 'name');
        return $name;
    }

    /**
     * 获取沐阳的个人信息
     */
    public function getMuYangProfile($param){
        //身份验证
        $this->authenticate();

        //查询单条信息
        if(isset($param['field'])){
            $field = $param['field'];
            $profile = $this->redis->hget(self::REDIS_MUYANG_KEY, $field);
            if($profile === null){
                $profile = '';
            }
            return $this->success([ $field => $profile]);
        }

        $profiles = $this->redis->hgetall(self::REDIS_MUYANG_KEY);
        return $this->success($profiles);
    }

    /**
     * 设置沐阳的个人信息
     */
    public function setMuYangProfile($param){
        //身份验证
        $this->authenticate();

        //检查参数
        $this->checkParam(['field', 'value'], $param);

        $this->redis->hset(self::REDIS_MUYANG_KEY, $param['field'], $param['value']);
        $this->redis->hset(self::REDIS_MUYANG_KEY, 'last_update_time', date('Y-m-d H:i:s', time()));

        return $this->success();
    }

    /**
     * 获取Age信息
     */
    public function getMuYangAge($param){
        //身份验证
        $this->authenticate();

        $birthday = $this->redis->hget(self::REDIS_MUYANG_KEY, self::REDIS_MUYANG_BIRTHDAY_FIELD);

        $age_timestamp = time() - strtotime($birthday);

        $day = floor($age_timestamp / (60*60*24));
        $hour = floor(($age_timestamp % (60*60*24))/(60*60));
        $minutes = floor(($age_timestamp % (60*60))/(60));

        return $this->success([
            'day' => $day,
            'hour' => $hour,
            'minutes' => $minutes
        ]);
    }
}


?>
