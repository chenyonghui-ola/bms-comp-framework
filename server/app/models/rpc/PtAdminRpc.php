<?php
/**
 * pt后台接口服务
 */

namespace Imee\Models\Rpc;

use GuzzleHttp\Psr7\Response;
use Imee\Libs\Rpc\BaseRpc;

class PtAdminRpc extends BaseRpc
{
    const API_PUSH_INDEX = 'pushIndex'; // 投递待审核数据
    const API_SEARCH = 'search'; // 获取搜索条件下的数据列表
    const API_AUDIT = 'audit'; // 审核

    protected $apiDevConfig = [
        'domain' => 'http://127.0.0.1',
        'host'   => 'pt-dev.iambanban.com'
    ];

    protected $apiConfig = [
        'domain' => 'https://admin.partying.sg',
        'host'   => 'admin.partying.sg'
    ];

    public $apiList = [
        self::API_PUSH_INDEX => [
            'path'   => '/api/open/csms/pushIndex',
            'method' => 'post',
        ],
        self::API_SEARCH     => [
            'path'   => '/api/open/csms/search',
            'method' => 'post',
        ],
        self::API_AUDIT      => [
            'path'   => '/api/open/csms/audit',
            'method' => 'post',
        ],
    ];

    protected function serviceConfig(): array
    {
        $config = ENV == 'dev' ? $this->apiDevConfig : $this->apiConfig;
        $config['options'] = [
            'headers'         => [
                'Content-Type'  => 'application/x-www-form-urlencoded',
                'Php-Auth-User' => 'csmsuser',
                'Php-Auth-Pw'   => 'C0kSvDGzx5BlgTol',
            ],
            'connect_timeout' => 5,
            'timeout'         => 10,
        ];

        $config['retry'] = [
            'max'   => 2,
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