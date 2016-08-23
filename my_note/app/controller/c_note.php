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
	const REDIS_TEXT_NOTE_PREFIX = 'text_note:';
	const REDIS_IMAGE_NOTE_PREFIX = 'image_note:';
	const REDIS_IMAGE_WAITING_COMMENT_PREFIX = 'image_note:waiting:';
	/**
	 * 构造函数
	 */
	public function __construct(){
        $this->redis = $this->getRedis();
        $this->user = new UserController();
    }

    /**
     * 添加图片备注
     */
    public function addImageComment($openid, $image_key, $comment){
    	//更新图片备注
		$redis_image_note_key = self::REDIS_IMAGE_NOTE_PREFIX  . $openid;
		$image_note_json = $this->redis->hget($redis_image_note_key, $image_key);
		$image_note = json_decode($image_note_json, true);
		$image_note['comment'] = $comment;
		$this->redis->hset($redis_image_note_key, $image_key, json_encode($image_note));
		 
		 //更新最新动态
		$this->redis->hset(self::REDIS_LATEST_NOTE_KEY, $openid, json_encode([
			'timestamp' => time(),
			'type' => 'image',
			'image_path' => $image_note['image_path'],
			'comment' => $comment
		]));
    }

	/**
	 * 获取文本日志列表
	 */
	public function getTextList($param){
		$openid = $this->getAuthUserId();

		$redis_text_note_key = self::REDIS_TEXT_NOTE_PREFIX . $openid;

		$note_list = $this->redis->hgetall($redis_text_note_key);

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
	 * 获取图片日志列表
	 */
	public function getImageList($param){
		$openid = $this->getAuthUserId();

		$redis_image_note_key = self::REDIS_IMAGE_NOTE_PREFIX . $openid;

		$note_list = $this->redis->hgetall($redis_image_note_key);

		//按时间排序
		krsort($note_list);

		$result = [];
		foreach ($note_list as $timestamp => $json) {
			$content = json_decode($json, true);
			$item['friend_time']  = $this->getFriendTime($timestamp);
			$item['key'] = $timestamp;
			$item['image_path'] = $content['image_path'];
			$item['comment'] = $content['comment'];
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

		$latest_notes = $this->redis->hgetall(self::REDIS_LATEST_NOTE_KEY);

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
    	//检查参数
        $this->checkParam(['key', 'type'], $param);

        $openid = $this->getAuthUserId();
        $type = $param['type'];

        switch ($type) {
        	case 'text':
        		$redis_text_note_key = self::REDIS_TEXT_NOTE_PREFIX . $openid;
				$this->redis->hdel($redis_text_note_key, $param['key']);
				return $this->success();
        		break;
        	
        	default:
        		
        		break;
        }

	}


	public function test($param){
		
	}
}
