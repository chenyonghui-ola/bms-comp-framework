<?php

namespace Imee\Models\Rpc;

use GuzzleHttp\Psr7\Response;
use Imee\Libs\Rpc\BaseRpc;

/**
 * 使用说明：
 * $obj = new \VeekaRpc();
 * 1.url传参
 * $obj->call(
 * \VeekaRpc::API_PRETTY_LIST, ['query' => []]
 * );
 * 2.json传参
 * $obj->call(
 * \VeekaRpc::API_PRETTY_LIST, ['json' => []]
 * );
 * 3.post x-www-form-urlencoded
 * $obj->call(
 * \VeekaRpc::API_PRETTY_LIST, ['form_params' => []]
 * );
 * 4.post form-data
 * $obj->call(
 * \VeekaRpc::API_PRETTY_LIST, ['multipart' => [
 * [
 * 'name'     => 'file',
 * 'contents' => $fileContent,
 * 'filename' => 'file_name.txt'
 * ],
 * [
 * 'name'     => 'test_name',
 * 'contents' => 'test_value'
 * ],
 * ]
 * );
 */

/**
 * veeka服务端接口配置
 */
class VeekaRpc extends BaseRpc
{
    const API_PRETTY_LIST = 'pretty_list';
    const API_APPLY_PRETTY = 'apply_pretty';
    const API_REVIEW_APPLY_PRETTY = 'review_apply_pretty';
    const API_UPDATE_PRETTY_EXPIRY = 'update_pretty_expiry';
    const API_PRETTY_UPDATE_LOG = 'update_pretty_log';
    const API_UPDATE_SONG = 'song_update';
	const API_COMMODITY_SEND = 'commodity_send';
	//创建家族
	const API_CREATE_FAMILY = 'create_family';
	//获取家族信息
	const API_GET_FAMILY = 'get_family_info';
	//获取我的家族信息
	const API_GET_MY_FAMILY = 'get_my_family_info';
	//解散家族
	const API_DELETE_FAMILY = 'delete_family';
	//删除成员
	const API_DELETE_FAMILY_MEMBER = 'delete_family_member';
	//加入家族
	const API_JOIN_FAMILY = 'join_family';

    protected $apiDevConfig = [
        'domain' => 'http://vk.sgola.cc',
        'host'   => 'vk.sgola.cc'
    ];

    protected $apiConfig = [
        'domain' => 'https://api.letsveeka.com',
        'host'   => 'api.letsveeka.com'
    ];

    public $apiList = [
	    self::API_CREATE_FAMILY          => [
		    'path'   => '/go/Family/InternalCreateFamily',
		    'method' => 'post',
	    ],
	    self::API_GET_FAMILY          => [
		    'path'   => '/go/Family/InternalGetFamilyInfo',
		    'method' => 'post',
	    ],
	    self::API_GET_MY_FAMILY          => [
		    'path'   => '/go/Family/InternalGetMyFamily',
		    'method' => 'post',
	    ],
	    self::API_DELETE_FAMILY          => [
		    'path'   => '/go/Family/InternalDisbandFamily',
		    'method' => 'post',
	    ],
	    self::API_DELETE_FAMILY_MEMBER          => [
		    'path'   => '/go/Family/InternalKickMember',
		    'method' => 'post',
	    ],
	    self::API_JOIN_FAMILY          => [
		    'path'   => 'go/Family/InternalJoinFamily',
		    'method' => 'post',
	    ],
	    self::API_COMMODITY_SEND          => [
		    'path'   => '/admincommodity/giveUserCommodity',
		    'method' => 'post',
	    ],
        self::API_PRETTY_LIST          => [
            'path'   => '/go/UserBase/AdminGetPerfectNumList',
            'method' => 'post',
        ],
        self::API_APPLY_PRETTY         => [
            'path'   => '/go/UserBase/AdminApplyPerfectNum',
            'method' => 'post',
        ],
        self::API_REVIEW_APPLY_PRETTY  => [
            'path'   => '/go/UserBase/AdminProcessPerfectNum',
            'method' => 'post',
        ],
        self::API_UPDATE_PRETTY_EXPIRY => [
            'path'   => '/go/UserBase/AdminUpdatePerfectNumExpiry',
            'method' => 'post',
            'retry'  => [
                'max'   => 2,
                'delay' => 100,
            ]
        ],
        self::API_PRETTY_UPDATE_LOG    => [
            'path'   => '/go/UserBase/AdminGetPerfectNumRecord',
            'method' => 'post',
            'retry'  => [
                'max'   => 2,
                'delay' => 100,
            ]
        ],
        self::API_UPDATE_SONG          => [
            'path'   => '/go/MusicLibrary/AdminUpdateSong',
            'method' => 'post',
            'retry'  => [
                'max'   => 2,
                'delay' => 100,
            ]
        ],
    ];

    protected function serviceConfig(): array
    {
        $config = ENV == 'dev' ? $this->apiDevConfig : $this->apiConfig;
        $config['options'] = [
            'headers'         => [
                'Content-Type' => 'application/json',
            ],
            'connect_timeout' => 5,
            'timeout'         => 10,
        ];

        $config['retry'] = [
            'max'   => 1,
            'delay' => 100,
        ];

        return $config;
    }

    protected function decode(Response $response = null, $code = 200): array
    {
        if ($response) {
            return [json_decode($response->getBody(), true), $response->getStatusCode()];
        }

        return [null, 500];
    }
}