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
	const REDIS_LATEST_NOTE_KEY = 'latest_note';
	const REDIS_IMAGE_NOTE_PREFIX = 'image_note:';
	const REDIS_IMAGE_WAITING_COMMENT_PREFIX = 'image_note:waiting:';
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
		$image_key = $this->redis1->get($redis_image_waiting_comment_key);
		if($image_key){
			$this->addImageComment($openid, $image_key, $content);
		}else{
			$redis_text_note_key = 'text_note:' . $openid;
			$now = time();
			$this->redis1->hset($redis_text_note_key, $now, $content);

			//添加到最新动态
			$this->redis1->hset(self::REDIS_LATEST_NOTE_KEY, $openid, json_encode([
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
		$this->redis1->hset($redis_image_note_key, $now, $value);

		//1分钟内接收到的文本信息作为该图片的描述
		$redis_image_waiting_comment_key = self::REDIS_IMAGE_WAITING_COMMENT_PREFIX . $openid;
		$this->redis1->setex($redis_image_waiting_comment_key, 60, $now);

		//添加到最新动态
		$this->redis1->hset(self::REDIS_LATEST_NOTE_KEY, $openid, json_encode([
			'timestamp' => $now,
			'type' => 'image',
			'image_path' => $file_path,
			'comment' => ''
		]));

		$reply = ['reply' => '挠挠：可以给图片添加备注哦...'];

       	return $this->success($reply);
    }

    /**
     * 添加图片备注
     */
    private function addImageComment($openid, $image_key, $comment){
    	//更新图片备注
		$redis_image_note_key = self::REDIS_IMAGE_NOTE_PREFIX  . $openid;
		$image_note_json = $this->redis1->hget($redis_image_note_key, $image_key);
		$image_note = json_decode($image_note_json, true);
		$image_note['comment'] = $comment;
		$this->redis1->hset($redis_image_note_key, $image_key, json_encode($image_note));
		 
		 //更新最新动态
		$this->redis1->hset(self::REDIS_LATEST_NOTE_KEY, $openid, json_encode([
			'timestamp' => time(),
			'type' => 'image',
			'image_path' => $image_note['image_path'],
			'comment' => $comment
		]));
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

		$note_list = $this->redis1->hgetall($redis_text_note_key);

		//按时间排序
		krsort($note_list);

		$result = [];
		foreach ($note_list as $timestamp => $content) {
			$item['friend_time']  = $this->getFriendTime($timestamp);
			$item['key'] = $timestamp;
			$item['content'] = $content;
			$result[] = $item;
		}

		return $this->success($result);
	}

	/**
	 * 获取友好显示的时间
	 */
	private function getFriendTime($timestamp){
		$now = time();
		$duration = $now - $timestamp;
		$one_day = 60*60*24;
		$one_hour = 60*60;
		$one_minute = 60;
		if($duration > $one_day*2){
			//大于2天直接显示日期
			return date('Y-m-d', $timestamp);
		}else if($duration > $one_day){
			//大于1天显示n天前
			$day = floor($duration / $one_day);
			return $day . '天前';
		}else if($duration > $one_hour){
			//大于1小时显示n小时前
			$hour = floor($duration / $one_hour);
			return $hour . '小时前';
		}else if($duration > $one_minute){
			//大于1分钟显示 n 分钟前
			$minutes = floor($duration / $one_minute);
			return $minutes . '分钟前';
		}else{
			return '刚刚';
		}


	}

	/**
	 * 获取所有用户的最新日志
	 */
	public function getLatest(){
		//身份验证
		$this->authenticate();

		$latest_notes = $this->redis1->hgetall(self::REDIS_LATEST_NOTE_KEY);

		$result = [];
		$time_series = [];
		//获取用户名称
		foreach ($latest_notes as $openid => $note_json) {
			$item = [];
			$item['openid'] = $openid;
			$name = $this->user->GetName($openid);
			$item['name'] = $name;
			$note = json_decode($note_json, true);
			$item = array_merge($item, $note);

			$time_series[] = $note['timestamp'];
			$result[] = $item;
		}

		//按发表时间排序
		array_multisort($time_series, SORT_DESC, $result);

		return $this->success($result);
	}

	/**
	 * 删除记录
	 */
	public function delete($param){
		//身份验证
		$this->authenticate();

    	//检查参数
        $this->checkParam(['openid', 'key', 'type'], $param);

        $type = $param['type'];

        switch ($type) {
        	case 'text':
        		$redis_text_note_key = 'text_note:' . $param['openid'];
				$this->redis1->hdel($redis_text_note_key, $param['key']);
				return $this->success();
        		break;
        	
        	default:
        		
        		break;
        }

	}


	public function test($param){
		
	}
}
