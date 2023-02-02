<?php

namespace Imee\Models\Ots;

use Aliyun\OTS\OTSClient;
use Aliyun\OTS\Consts\ReturnTypeConst;
use Aliyun\OTS\Consts\DirectionConst;
use Aliyun\OTS\Consts\PrimaryKeyTypeConst;
use Aliyun\OTS\Consts\RowExistenceExpectationConst;
use Aliyun\OTS\Consts\SortOrderConst;
use Config\ConfigAliyunOts;

class BaseOts
{
    const DESC = DirectionConst::CONST_BACKWARD;
    const ASC = DirectionConst::CONST_FORWARD;

    const SEARCH_SORT_DESC = SortOrderConst::SORT_ORDER_DESC;
    const SEARCH_SORT_ASC = SortOrderConst::SORT_ORDER_ASC;

    const INF_MAX = PrimaryKeyTypeConst::CONST_INF_MAX;
    const INF_MIN = PrimaryKeyTypeConst::CONST_INF_MIN;

    const COND_EXPECT_EXIST = RowExistenceExpectationConst::CONST_EXPECT_EXIST;
    const COND_EXPECT_NOT_EXIST = RowExistenceExpectationConst::CONST_EXPECT_NOT_EXIST;
    const COND_IGNORE = RowExistenceExpectationConst::CONST_IGNORE;

    const FILTER_EQUAL = 1;
    const FILTER_NOT_EQUAL = 2;
    const FILTER_GREATER_THAN = 3;
    const FILTER_GREATER_EQUAL = 4;
    const FILTER_LESS_THAN = 5;
    const FILTER_LESS_EQUAL = 6;

    const FILTER_SET_NOT = 1;
    const FILTER_SET_AND = 2;
    const FILTER_SET_OR = 3;

    const EndPoint = ConfigAliyunOts::EndPoint;
    const EndPointDev = ConfigAliyunOts::EndPointDev;

    const AccessKeyID_PayChangeHistory = ConfigAliyunOts::AccessKeyID_PayChangeHistory;
    const AccessKeySecret_PayChangeHistory = ConfigAliyunOts::AccessKeySecret_PayChangeHistory;

    const AccessKeyID_Normal = ConfigAliyunOts::AccessKeyID_Normal;
    const AccessKeySecret_Normal = ConfigAliyunOts::AccessKeySecret_Normal;

    private $_instanceToPort = array(
        'xs-test'          => 11111,
        'xs-normal'        => 11112,
        'xs-pay-change'    => 11113,
        'xs-friend-circle' => 11114
    );

    private $_tableName;
    private $_instanceName;
    protected $_client = null;
    protected $_endPoint;

    public function __construct()
    {
        $this->_instanceName = "xs-normal";
        $this->_endPoint = "http://{$this->_instanceName}.ap-southeast-1.vpc.tablestore.aliyuncs.com";
    }

    private static function _getTableName()
    {
        $className = get_called_class();
        if (substr($className, 0, 3) != 'OTS') throw new \Exception("className must start with OTS");

        $name = preg_replace_callback("/[A-Z]/", function ($match) {
            return '_' . strtolower($match[0]);
        }, substr($className, 3));
        return substr($name, 1);
    }

    public static function getClient()
    {
        $className = get_called_class();
        $ots = new $className();
        return $ots->getOtsClient();
    }

    //按照主键单个查询
    public static function findFirst(array $pk)
    {
        $className = get_called_class();
        $ots = new $className();
        return $ots->getRow($pk);
    }

    //pks 每个元素都是上述里面的$pk
    public static function findFirsts(array $pks)
    {
        $className = get_called_class();
        $ots = new $className();
        return $ots->getRows($pks);
    }

    //通过主键按照范围查询
    public static function find(array $pkStart, array $pkEnd, $dir = self::DESC, array $filter = null, $limit = 2000, $tabelname = '')
    {
        $className = get_called_class();
        $ots = new $className();
        return $ots->getRange($pkStart, $pkEnd, $dir, $filter, $limit, $tabelname);
    }

    public static function search(array $query)
    {
        $className = get_called_class();
        $ots = new $className();
        return $ots->query($query);
    }

    //通过主键修改
    public static function updateByPk(array $pk, array $update, $condition = self::COND_EXPECT_EXIST)
    {
        $className = get_called_class();
        $ots = new $className();
        return $ots->update($pk, $update, $condition);
    }

    //通过主键删除
    public static function deleteByPk(array $pk, $condition = self::COND_EXPECT_EXIST)
    {
        $className = get_called_class();
        $ots = new $className();
        return $ots->delete($pk, $condition);
    }

    public function setInstanceName($instanceName)
    {
        if ($instanceName != $this->_instanceName) {
            $this->_client = null;
        }
        $this->_instanceName = $instanceName;
    }

    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;
    }

    public function delete(array $pk, $condition = self::COND_EXPECT_EXIST)
    {
        $request = [
            'table_name'     => $this->_getTableName(),
            'condition'      => $condition,
            'primary_key'    => $pk,
            'return_content' => [
                'return_type' => ReturnTypeConst::CONST_PK
            ]
        ];
        $this->initClient();
        try {
            $response = $this->_client->deleteRow($request);
            return true;
        } catch (\Exception $e) {
            //使用这种情况，可以减少一个write单元费用
            if (
                $condition == self::COND_EXPECT_EXIST
                && strpos($e->getMessage(), 'Condition check failed') !== false
            ) {
                return true;
            }
            return false;
        }
    }

    //
    public function update($tablename, array $pk, array $update, $condition = self::COND_EXPECT_EXIST)
    {
        $request = [
            'table_name'                  => $tablename,
            'condition'                   => $condition,
            'primary_key'                 => $pk,
            'update_of_attribute_columns' => [
                'PUT' => $update,
            ],
            'return_content'              => [
                'return_type' => ReturnTypeConst::CONST_PK
            ]
        ];
        $this->initClient();
        try {
            $response = $this->_client->updateRow($request);
            return true;
        } catch (\Exception $e) {
            if (($condition == self::COND_EXPECT_EXIST || $condition == self::COND_EXPECT_NOT_EXIST)
                && strpos($e->getMessage(), 'Condition check failed') !== false
            ) {
                return true;
            }
            return false;
        }
    }

    public function getRange(array $pkStart, array $pkEnd, $dir = self::DESC, array $filter = null, $limit = 20, $tablename = '')
    {
        $request = [
            'table_name'                  => $this->_getTableName(),
            'max_versions'                => 1,
            'direction'                   => $dir,
            'inclusive_start_primary_key' => $pkStart,
            'exclusive_end_primary_key'   => $pkEnd,
            'limit'                       => $limit,
        ];
        if (!empty($filter)) {
            $request['column_filter'] = $filter;
        }
        if (!empty($tablename)) {
            $request['table_name'] = $tablename;
        }
        $this->initClient();
        try {
            $response = $this->_client->getRange($request);
            return $this->_formatRangeRows($response);
        } catch (\Exception $e) {
            print_r($e->getMessage());
            return false;
        }
    }

    public function query(array $request)
    {
        $this->initClient();
        try {
            $response = $this->_client->search($request);
            return $this->_formatSearchRows($response);
        } catch (\Exception $e) {
            print_r($e->getMessage());
            return false;
        }
    }

    public function getRow(array $pk)
    {
        $request = [
            'table_name'     => $this->_getTableName(),
            'primary_key'    => $pk,
            'max_versions'   => 1,
            'return_content' => [
                'return_type' => ReturnTypeConst::CONST_PK
            ]
        ];
        $this->initClient();
        try {
            $response = $this->_client->getRow($request);
            return $this->_formatRow($response);
        } catch (\Exception $e) {
            //表明查询失败
            return false;
        }
    }

    public function getRows(array $pks)
    {
        $tableName = $this->_getTableName();
        $request = [
            'tables' => [
                [
                    'table_name'     => $tableName,
                    'primary_keys'   => $pks,
                    'max_versions'   => 1,
                    'return_content' => [
                        'return_type' => ReturnTypeConst::CONST_PK
                    ]
                ]
            ]
        ];
        $this->initClient();
        try {
            $response = $this->_client->batchGetRow($request);
            $data = array();
            foreach ($response['tables'] as $all) {
                if ($all['table_name'] == $tableName) {
                    foreach ($all['rows'] as $val) {
                        if ($val['is_ok'] > 0) {
                            $item = array();
                            foreach ($val['primary_key'] as $v) {
                                $item[$v[0]] = $v[1];
                            }
                            foreach ($val['attribute_columns'] as $v) {
                                $item[$v[0]] = $v[1];
                            }
                            $data[] = $item;
                        }
                    }
                }
            }
            return $data;
        } catch (\Exception $e) {
            //表明查询失败
            return false;
        }
    }

    protected function _formatRangeRows($response)
    {
        $data = array();
        foreach ($response['rows'] as $val) {
            $item = array();
            foreach ($val['primary_key'] as $v) {
                $item[$v[0]] = $v[1];
            }
            foreach ($val['attribute_columns'] as $v) {
                $item[$v[0]] = $v[1];
            }
            $data[] = $item;
        }
        return array(
            'data'           => $data,
            'pk_next'        => $response['next_start_primary_key'],
            'pk_next_string' => $this->_nextToString($response['next_start_primary_key']),
        );
    }

    protected function _formatSearchRows($response)
    {
        $data = array();
        foreach ($response['rows'] as $val) {
            $item = array();
            foreach ($val['primary_key'] as $v) {
                $item[$v[0]] = $v[1];
            }
            foreach ($val['attribute_columns'] as $v) {
                $item[$v[0]] = $v[1];
            }
            $data[] = $item;
        }
        return array(
            'data'           => $data,
            'total'          => $response['total_hits'],
            'pk_next_string' => base64_encode($response['next_token']),
            'next_token'     => $response['next_token'],
        );
    }

    protected function _formatRow($response)
    {
        //表明不存在此数据
        if (empty($response['primary_key'])) return null;

        $data = array();
        foreach ($response['primary_key'] as $val) {
            $data[$val[0]] = $val[0];
        }
        foreach ($response['attribute_columns'] as $val) {
            $data[$val[0]] = $val[0];
        }
        return $data;
    }

    private function _nextToString($next)
    {
        if (empty($next)) return null;
        $values = array();
        foreach ($next as $val) {
            $values[] = $val[0];
            $values[] = $val[1];
        }
        return implode(',', $values);
    }

    protected function initClient()
    {
        if ($this->_client != null) {
            return $this->_client;
        }
        $appKey = self::AccessKeyID_Normal;
        $appSecret = self::AccessKeySecret_Normal;
        if ($this->_instanceName == 'xs-pay-change') {
            $appKey = self::AccessKeyID_PayChangeHistory;
            $appSecret = self::AccessKeySecret_PayChangeHistory;
        }
        $this->_client = new OTSClient(array(
            'EndPoint'        => $this->_endPoint,
            'AccessKeyID'     => $appKey,
            'AccessKeySecret' => $appSecret,
            'InstanceName'    => $this->_instanceName,
            'SocketTimeout'   => 5,
        ));
        return $this->_client;
    }

    protected function getOtsClient()
    {
        $this->initClient();
        return $this->_client;
    }
}