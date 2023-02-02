<?php

namespace Imee\Models\Nsq;

use Imee\Libs\Utility;
use Imee\Service\Nsq;

class UserNsq
{
	const TYPE_NO = 1;
	const TYPE_LANGUAGE = 2;
	const TYPE_LEVEL = 3;
	const TYPE_HEADIMG = 4;
	const TYPE_BGIMG = 5;
	const TYPE_ROOMIMG = 6;
	const TYPE_ROOMBG = 7;

	public static $type = [
		self::TYPE_NO => '用户封禁',
		self::TYPE_LANGUAGE => '修改语言',
		self::TYPE_LEVEL => '修改用户等级',
		self::TYPE_HEADIMG => '头像违规清空',
		self::TYPE_BGIMG => '资料背景图清空',
		self::TYPE_ROOMIMG => '房间封面清空',
		self::TYPE_ROOMBG => '房间背景清空',
	];

	const TOPIC_XS_USER_UPDATE = 'xs.user.update';

	/**
	 * 更新用户信息发消息通知给服务端（服务端根据uid删除该用户缓存）
	 */
	public static function publishXsUserUpdate($uid, $type)
	{
		return Nsq::publish(self::TOPIC_XS_USER_UPDATE, [
			'cmd' => 'xs.user.update',
			'time' => Utility::microtimeFloat(),
			'data' => [
				'uid' => $uid,
				'type' => $type
			],
		]);
	}
}