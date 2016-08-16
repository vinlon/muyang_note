<?php
/**
 * 消息处理
 */

namespace Controller;

use CustomError;
use EasyWeChat\Message\News;
use Exception;
/**
 * 消息处理
 */
class MessageController extends BaseController{
    /**
	 * 构造函数
	 */
	public function __construct(){
        $this->redis0 = $this->getRedis(0);
    }

    /**
     * 处理消息
     */
    public function handle($message){
        $msg_type = $message->MsgType;
        
        //查看事件的注册列表
        $redis_handler_key = 'message_handler';
        $handler = $this->redis0->hget($redis_handler_key, $msg_type);
        if($handler){
            try {
                $headers = ['ticket' => TOKEN];
                $json = $this->postJSON($handler, json_encode($message), $headers);
                $result = json_decode($json, true);
                if($result['return_code'] === 200){
                    $reply = $result['data'];
                }else{
                    $reply = '挠挠：爸爸的程序出错了！';
                }
            } catch (Exception $e) {
                $reply = '挠挠：爸爸的程序出错了！';
            }
        }else{
            //没有注册事件，使用默认回复
            $redis_default_reply = 'message_default_reply';
            $reply = $this->redis0->hget($redis_default_reply, $msg_type);
            if(!$reply){
                $reply = '挠挠：我年纪还小，不明白你在说什么...[难过]';
            }
        }
        return $this->reply($reply);
    }

    /**
     * 回复消息
     */
     private function reply($reply){
         if(is_string($reply)){
             return $reply;
         }
         $type = isset($reply['type']) ? $reply['type'] : 'text';
         switch ($type) {
             case 'text':
                 return isset($reply['reply']) ? $reply['reply'] : '错误的消息格式';
                 break;
             case 'news':
                 return new News([
                    'title'       => isset($reply['title']) ? $reply['title'] : '未设置',
                    'description' => isset($reply['description']) ? $reply['description'] : '未设置',
                    'url'         => isset($reply['url']) ? $reply['url'] : 'http:/baidu.com',
                    'image'       => isset($reply['image']) ? $reply['image'] : ''
                ]);
                break;

             default:
                 # code...
                 break;
         }
     }

    /**
     * 注册消息处理事件
     */
    public function registHandler($param){
        //身份验证
		$this->authenticate();

		//检查参数
        $this->checkParam(['msg_type', 'handler'], $param);

        $msg_type = strtolower($param['msg_type']);
        $handler = $param['handler'];

        $msg_type_list = ['text' , 'image' , 'voice' , 'video'];

        //检查消息类型的有效性
        if(!in_array($msg_type, $msg_type_list)){
            return $this->error(CustomError::INVALID_MSG_TYPE);
        }

        //验证url合法性
        if(!filter_var($handler, FILTER_VALIDATE_URL)){
            return $this->error(CustomError::INVALID_HANDLER_URL);
        }

        //添加handler
        $redis_handler_key = 'message_handler';
        $this->redis0->hset($redis_handler_key, $msg_type, $handler);

        return $this->success();
    }
}

?>
