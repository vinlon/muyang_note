<?php
/**
 *  Note Controller
 */

namespace Controller;

use CustomError;

/**
 * Note管理
 */
class NoteController extends BaseController
{
	/**
	 * 构造函数
	 */
	public function __construct(){
        $this->redis1 = $this->getRedis(1);
        $this->user = new UserController();
    }

    /**
     * 处理文本消息
     */
    public function handleText($param){
		//身份验证
		$this->authenticate();

    	//检查参数
        $this->checkParam(['FromUserName', 'Content'], $param);

		$content = $param['Content'];
		$openid = $param['FromUserName'];

		//检查用户注册信息
		$user_status = $this->user->checkStatus($openid);
		switch ($user_status) {
			case $this->user->user_status['STRANGER']:
				return $this->success(['reply' => '挠挠：你是谁？']);
				break;
			case $this->user->user_status['ANONYMOUS']:
				return $this->success(['reply' => '挠挠：你到底是谁？']);
				break;
			case $this->user->user_status['REGISTERED']:
				return $this->success(['reply' => '挠挠：要【爸爸】同意我才能和你玩...']);
				break;
		}

		//处理关键字
		//TODO

		$strlen = (strlen($content) + mb_strlen($content,'UTF8'))/2;
		if($strlen < 10){
			return $this->success(['reply' => '挠挠：少于5个字就太没有诚意了哦。。。']);
		}

		$redis_text_note_key = 'text_note:' . $openid;
		$this->redis1->hset($redis_text_note_key, time(), $content);

		$reply = $this->getDynamicReply($openid);

       	return $this->success($reply);
    }

	/**
	 * 获取文本日志列表
	 */
	public function getTextList($param){
		//身份验证
		$this->authenticate();

    	//检查参数
        $this->checkParam(['openid'], $param);

		$openid = $param['openid'];

		$redis_text_note_key = 'text_note:' . $openid;

		$result = $this->redis1->hgetall($redis_text_note_key);

		return $this->success($result);
	}

	/**
	 * 备忘添加成功后自动生成回复
	 */
	private function getDynamicReply($openid){
		$redis_visit_record = 'visit_count:' . $openid;
		$visit_count = $this->redis1->incrby($redis_visit_record, 1);
		//空闲10分钟则重新记录
		$this->redis1->expire($redis_visit_record, 10*60);
		if($visit_count === 1 || $visit_count % 5 === 0){
			//第一次回复和每5次间隔
			return [
				'type' => 'news',
				'title' => '成长日记',
				'description' => '点击查看成长日志',
				'image' => '',
				'url' => 'http://ab.aikaka.com.cn/liwenlong/my_note/page/index.html#/'
			];
		}
		return '';
	}
}
