<?php 
/**
 * Weixin User
 */

namespace Controller;

use EasyWeChat\Foundation\Application;

/**
* 微信用户
*/
class UserController extends BaseController{
    /**
     * 构造函数
     */
    public function __construct(){
        $this->redis0 = $this->getRedis(0);

        $this->app = new Application([
            'debug'  => DEBUG,
            'app_id' => APPID,
            'secret' => APPSECRET,
            'log' => [
                'level' => 'debug',
                'file'  => __DIR__.'/../../logs/easywechat/user.log',
            ]
        ]);
    }

    /**
     * 获取用户信息
     * @param  array $param [ openid ]
     * @return array  user info
     */
    public function getInfo($param){
        //身份验证
        $this->authenticate();

        //检查参数
        $this->checkParam(['openid'], $param);

        $openid = $param['openid'];

        $user_service = $this->app->user;

        $user_info = $user_service->get($openid);

        var_dump($user_info);


    }
}


 ?>