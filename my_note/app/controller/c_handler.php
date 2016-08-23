<?php
/**
 *  Message Handler
 */

namespace Controller;

use CustomError;

/**
 * Note管理
 */
class MessageHandler extends BaseController
{
	const REDIS_LATEST_NOTE_KEY = 'latest_note';
	const REDIS_TEXT_NOTE_PREFIX = 'text_note:';
	const REDIS_IMAGE_NOTE_PREFIX = 'image_note:';
	const REDIS_IMAGE_WAITING_COMMENT_PREFIX = 'image_note:waiting:';
	/**
	 * 构造函数
	 */
	public function __construct(){
        $this->redis = $this->getRedis();
        $this->user = new UserController();
        $this->note = new NoteController();
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
		$user_status = $this->user->checkStatus($openid, $content);
		switch ($user_status) {
			case $this->user->user_status['STRANGER']:
				return $this->success(['reply' => '挠挠：你是谁？']);
				break;
			case $this->user->user_status['ANONYMOUS']:
				return $this->success(['reply' => '挠挠：要【爸爸】同意我才能和你玩...']);
				break;
		}

		//处理关键字
		//TODO

		$strlen = (strlen($content) + mb_strlen($content,'UTF8'))/2;
		if($strlen < 10){
			return $this->success(['reply' => '挠挠：少于5个字就太没有诚意了哦。。。']);
		}

		//判断文字信息是否为图片的备注
		$redis_image_waiting_comment_key = self::REDIS_IMAGE_WAITING_COMMENT_PREFIX . $openid;
		$image_key = $this->redis->get($redis_image_waiting_comment_key);
		if($image_key){
			$this->note->addImageComment($openid, $image_key, $content);
		}else{
			$redis_text_note_key = self::REDIS_TEXT_NOTE_PREFIX . $openid;
			$now = time();
			$this->redis->hset($redis_text_note_key, $now, $content);

			//添加到最新动态
			$this->redis->hset(self::REDIS_LATEST_NOTE_KEY, $openid, json_encode([
				'timestamp' => $now,
				'type' => 'text',
				'content' => $content
			]));
		}

		$reply = $this->getDynamicReply($openid);

       	return $this->success($reply);
    }

    /**
     * 处理图片消息 
     */
    public function handleImage($param){
		//身份验证
		$this->authenticate();

    	//检查参数
        $this->checkParam(['FromUserName', 'PicUrl'], $param);

		$pic_url = $param['PicUrl'];
		$openid = $param['FromUserName'];

		//检查用户注册信息
		$user_status = $this->user->checkStatus($openid, 'image');
		switch ($user_status) {
			case $this->user->user_status['STRANGER']:
				return $this->success(['reply' => '挠挠：你是谁？']);
				break;
			case $this->user->user_status['ANONYMOUS']:
				return $this->success(['reply' => '挠挠：要【爸爸】同意我才能和你玩...']);
				break;
		}

		//保存图片
		$root = __DIR__ . '/../../';
		$path = 'media/image/' . date('Y',time()) . '/' . date('m', time()) . '/';
		$file_name = uniqid() . '.jpg';
		if(!is_dir($root . $path)){
			mkdir($root . $path, 0777, true);
		}

		$stream = file_get_contents($pic_url);
		file_put_contents($root . $path . $file_name, $stream);

		$file_path = $path . $file_name;
		$value = json_encode([
			'image_path' => $file_path,
			'comment' => ''
		]);
		$redis_image_note_key = self::REDIS_IMAGE_NOTE_PREFIX  . $openid;
		$now = time();
		$this->redis->hset($redis_image_note_key, $now, $value);

		//1分钟内接收到的文本信息作为该图片的描述
		$redis_image_waiting_comment_key = self::REDIS_IMAGE_WAITING_COMMENT_PREFIX . $openid;
		$this->redis->setex($redis_image_waiting_comment_key, 60, $now);

		//添加到最新动态
		$this->redis->hset(self::REDIS_LATEST_NOTE_KEY, $openid, json_encode([
			'timestamp' => $now,
			'type' => 'image',
			'image_path' => $file_path,
			'comment' => ''
		]));

		$reply = ['reply' => '挠挠：给图片加个备注吧...'];

       	return $this->success($reply);
    }

	/**
	 * 备忘添加成功后自动生成回复
	 */
	private function getDynamicReply($openid){
		$redis_visit_record = 'visit_count:' . $openid;
		$visit_count = $this->redis->incrby($redis_visit_record, 1);
		//空闲1分钟则重新记录
		$this->redis->expire($redis_visit_record, 60);
		if($visit_count === 1 || $visit_count % 5 === 0){
			//第一次回复和每5次间隔
			return [
				'type' => 'news',
				'title' => '成长日记',
				'description' => '点击查看成长日志',
				'image' => '',
				'url' => 'http://ab.aikaka.com.cn/liwenlong/my_note/page/index.html#/?openid=' . $openid
			];
		}
		return '';
	}
}
