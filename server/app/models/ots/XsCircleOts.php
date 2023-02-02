<?php

namespace Imee\Models\Ots;

use Aliyun\OTS\OTSClient;
use Aliyun\OTS\Consts\QueryTypeConst;
use Aliyun\OTS\Consts\ReturnTypeConst;
use Aliyun\OTS\Consts\ColumnReturnTypeConst;
use Aliyun\OTS\Consts\SortOrderConst;
use Aliyun\OTS\Consts\PrimaryKeyTypeConst;
use Aliyun\OTS\Consts\DirectionConst;
use Aliyun\OTS\Consts\LogicalOperatorConst;
use Aliyun\OTS\Consts\ComparatorTypeConst;


// 用于朋友圈相关内容
class XsCircleOts extends BaseOts
{
    // 开发环境朋友圈阿里云TableStore相关配置
    const CircleDev_EndPoint = 'https://xs-circle-sgp.cn-hangzhou.ots.aliyuncs.com';
    const CircleDev_AccessKeyID = 'LTAI4FjUJgcgr5x8eGC39sof';
    const CircleDev_AccessKeySecret = 'IbkH0fHdV82vCV4TlNwiD8aGVfeAxS';
    const CircleDev_InstanceName = 'xs-circle-sgp';

    const RESULT_STATUS_DELETED = 'deleted';
    const RESULT_STATUS_PENDING = 'pending';
    const RESULT_STATUS_SUCCESS = 'success';
    const RESULT_STATUS_FAILED = 'failed';

    const ADMIN_STATUS_PENDING = 'pending';
    const ADMIN_STATUS_PROCESSED = 'processed';

    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_DELETED = 'deleted';
    const STATUS_PENDING = 'pending';

    protected $client = null;
    public static $xs_circle_topic_search_v = (ENV == 'dev') ? 'xs_circle_topic_index' : 'xs_circle_topic_search_v4';

    public function __construct()
    {
        parent::__construct();
        $this->setInstanceName('xs-friend-circle');
    }

    /**
     * 查询朋友圈最近的历史记录
     * https://help.aliyun.com/document_detail/121083.html
     * @param array $options
     * @param $tablename
     * @param $index
     * @param $page
     * @param $limit
     * @return array|false|mixed
     */
    public function getList(array $options, $tablename, $index, $page = 1, $limit = 20)
    {
        $limit = intval($limit);
        $offset = ($page - 1) * $limit;
        $querys = array();
        $fieldsNormal = array(
            'uid'          => 'int',
            'topic_id'     => 'int',
            'status'       => 'string',
            'atype'        => 'string',
            'cmtid'        => 'int',
            'admin_status' => 'string',
            'content'      => 'string',
            'app_id'       => 'int',
        );

        $keywordFields = array('content');

        foreach ($fieldsNormal as $field => $type) {
            if (!isset($options[$field])) continue;
            if ($type == 'int') {
                if (is_array($options[$field])) {
                    $value = $options[$field];
                } else if (!is_numeric($options[$field])) {
                    continue;
                } else {
                    $value = intval($options[$field]);
                }
            } else if ($type == 'string') {
                if (empty($options[$field])) continue;
                if (is_array($options[$field])) {
                    $value = $options[$field];
                } else {
                    $value = trim($options[$field]);
                }
            } else {
                continue;
            }
            if (is_array($value)) {
                $querys[] = array(
                    'query_type' => QueryTypeConst::TERMS_QUERY,
                    'query'      => array(
                        'field_name' => $field,
                        'terms'      => $value
                    )
                );
            } else {
                $querys[] = array(
                    'query_type' => QueryTypeConst::TERM_QUERY,
                    'query'      => array(
                        'field_name' => $field,
                        'term'       => $value
                    )
                );
            }
        }
        foreach ($keywordFields as $field) {
            if (isset($options[$field]) && !empty($options[$field])) {
                $querys[] = array(
                    'query_type' => QueryTypeConst::MATCH_PHRASE_QUERY,
                    'query'      => array(
                        'field_name' => $field,
                        'text'       => trim($options[$field]),
                    )
                );
            }
        }
        if ((isset($options['start']) && $options['start']) || (isset($options['end']) && $options['end'])) {
            $querys[] = array(
                'query_type' => QueryTypeConst::RANGE_QUERY,
                'query'      => array(
                    'field_name'    => 'time',
                    'range_from'    => isset($options['start']) ? intval($options['start']) : 0,
                    'include_lower' => true,
                    'range_to'      => isset($options['end']) ? intval($options['end']) : intval("4102329600"),
                    'include_upper' => true,
                )
            );
        }
        $boolQuery = array();
        if (!empty($querys)) {
            $boolQuery = array(
                'query_type' => QueryTypeConst::BOOL_QUERY,
                'query'      => array(
                    'must_queries' => $querys
                )
            );
        }

        // 原先的not_query
        $must_not_queries = array();
        if (isset($options['not_query'])) {
            if (isset($options['not_query']['value']) && is_array($options['not_query']['value'])) {
                $must_not_queries[] = array(
                    'query_type' => QueryTypeConst::TERMS_QUERY,
                    'query'      => array(
                        'field_name' => $options['not_query']['column_name'],
                        'terms'      => $options['not_query']['value']
                    )
                );
            } else {
                $must_not_queries[] = array(
                    'query_type' => QueryTypeConst::TERM_QUERY,
                    'query'      => array(
                        'field_name' => $options['not_query']['column_name'],
                        'term'       => $options['not_query']['value']
                    )
                );
            }
        }


        // 原先的not_query 只能支持单个查询，需要封装一个多个BoolQuery
        if (isset($options['must_not_queries'])) {
            foreach ($options['must_not_queries'] as $not_field => $not_value) {
                if (is_array($not_value)) {

                    foreach ($not_value as $one_value) {
                        $must_not_queries[] = array(
                            'query_type' => QueryTypeConst::TERM_QUERY,
                            'query'      => array(
                                'field_name' => $not_field,
                                'term'       => $one_value
                            )
                        );
                    }
//					$must_not_queries[] = array(
//						'query_type' => QueryTypeConst::TERMS_QUERY,
//						'query' => array(
//							'field_name' => $not_field,
//							'terms' => $not_value
//						)
//					);
                } else {
                    $must_not_queries[] = array(
                        'query_type' => QueryTypeConst::TERM_QUERY,
                        'query'      => array(
                            'field_name' => $not_field,
                            'term'       => $not_value
                        )
                    );
                }
            }
        }

        //两个一起用的方式是： boolQuery( mustQuery( boolQuery1(mustQuery) , boolQuery2(mustnotQuery) ) )
        if (!empty($must_not_queries)) {
            if (!empty($boolQuery)) {
//				$boolQuery['query']['must_queries'][]  = array(
//					'query_type' => QueryTypeConst::BOOL_QUERY,
//					'query' => array(
//						'must_not_queries' => $must_not_queries
//					)
//				);

                $boolQuery['query']['must_not_queries'] = $must_not_queries;
            } else {
                $boolQuery = array(
                    'query_type' => QueryTypeConst::BOOL_QUERY,
                    'query'      => array(
                        'must_not_queries' => $must_not_queries
                    )
                );
            }
        }
        $request = array(
            'table_name'     => $tablename,
            'index_name'     => $index,
            'search_query'   => array(
                'offset'          => $offset,
                'limit'           => $limit,
                'get_total_count' => true,
                'query'           => !empty($boolQuery) ? $boolQuery : array(
                    'query_type' => QueryTypeConst::RANGE_QUERY,
                    'query'      => array(
                        'field_name'    => 'topic_id',
                        'range_from'    => 0,
                        'include_lower' => true,
                        'range_to'      => PHP_INT_MAX,
                        'include_upper' => false
                    )
                ),
                'sort'            => $tablename == 'xs_circle_topic' ? array(
                    array(
                        'field_sort' => array(
                            'field_name' => 'time',
                            'order'      => isset($options['time_order']) ? $options['time_order'] : SortOrderConst::SORT_ORDER_DESC,
                        )
                    ),
                    array(
                        'pk_sort' => array(
                            'order' => self::SEARCH_SORT_DESC
                        )
                    ),
                ) : array(
                    array(
                        'pk_sort' => array(
                            'order' => self::SEARCH_SORT_DESC
                        )
                    )
                )
            ),
            'columns_to_get' => array(
                'return_type' => ColumnReturnTypeConst::RETURN_ALL,
            )
        );
        if (ENV == 'dev') {
            return $this->getDevList($request);
        } else {
            return self::search($request);
        }
    }

    //查询朋友圈最近的历史记录
    public function getListToken(array $options, $tablename, $index, $page = 1, $limit = 20, $token = '')
    {
        $limit = intval($limit);
        $offset = ($page - 1) * $limit;
        $querys = array();
        $fieldsNormal = array(
            'uid'          => 'int',
            'topic_id'     => 'int',
            'status'       => 'string',
            'atype'        => 'string',
            'cmtid'        => 'int',
            'admin_status' => 'string',
            'content'      => 'string',
            'app_id'       => 'int',
        );

        $keywordFields = array('content');

        foreach ($fieldsNormal as $field => $type) {
            if (!isset($options[$field])) continue;
            if ($type == 'int') {
                if (!is_numeric($options[$field])) continue;
                $value = intval($options[$field]);
            } else if ($type == 'string') {
                if (empty(trim($options[$field]))) continue;
                $value = trim($options[$field]);
            } else {
                continue;
            }
            $querys[] = array(
                'query_type' => QueryTypeConst::TERM_QUERY,
                'query'      => array(
                    'field_name' => $field,
                    'term'       => $value
                )
            );
        }

        foreach ($keywordFields as $field) {
            if (isset($options[$field]) && !empty($options[$field])) {
                $querys[] = array(
                    'query_type' => QueryTypeConst::MATCH_PHRASE_QUERY,
                    'query'      => array(
                        'field_name' => $field,
                        'text'       => trim($options[$field]),
                    )
                );
            }
        }
        if ((isset($options['start']) && $options['start']) || (isset($options['end']) && $options['end'])) {
            $querys[] = array(
                'query_type' => QueryTypeConst::RANGE_QUERY,
                'query'      => array(
                    'field_name'    => 'time',
                    'range_from'    => isset($options['start']) ? intval($options['start']) : 0,
                    'include_lower' => true,
                    'range_to'      => isset($options['end']) ? intval($options['end']) : intval("4102329600"),
                    'include_upper' => true,
                )
            );
        }
        $boolQuery = array();
        if (!empty($querys)) {
            $boolQuery = array(
                'query_type' => QueryTypeConst::BOOL_QUERY,
                'query'      => array(
                    'must_queries' => $querys
                )
            );
        }
        if (isset($options['not_query'])) {
            $must_not_queries = array();
            $must_not_queries[] = array(
                'query_type' => QueryTypeConst::TERM_QUERY,
                'query'      => array(
                    'field_name' => $options['not_query']['column_name'],
                    'term'       => $options['not_query']['value']
                )
            );
            //两个一起用的方式是： boolQuery( mustQuery( boolQuery1(mustQuery) , boolQuery2(mustnotQuery) ) )
            if (!empty($boolQuery)) {
                $boolQuery['query']['must_queries'][] = array(
                    'query_type' => QueryTypeConst::BOOL_QUERY,
                    'query'      => array(
                        'must_not_queries' => $must_not_queries
                    )
                );
            } else {
                $boolQuery = array(
                    'query_type' => QueryTypeConst::BOOL_QUERY,
                    'query'      => array(
                        'must_not_queries' => $must_not_queries
                    )
                );
            }
        }

        $request = array(
            'table_name'     => $tablename,
            'index_name'     => $index,
            'search_query'   => array(
                'offset'          => $offset,
                'limit'           => $limit,
                'get_total_count' => true,
                'token'           => $token,
                'query'           => !empty($boolQuery) ? $boolQuery : array(
                    'query_type' => QueryTypeConst::RANGE_QUERY,
                    'query'      => array(
                        'field_name'    => 'topic_id',
                        'range_from'    => 0,
                        'include_lower' => true,
                        'range_to'      => PHP_INT_MAX,
                        'include_upper' => false
                    )
                ),
                'sort'            => null
            ),
            'columns_to_get' => array(
                'return_type' => ColumnReturnTypeConst::RETURN_ALL,
            )
        );
        if (ENV == 'dev') {
            return $this->getDevList($request);
        } else {
            return self::search($request);
        }
    }

    // 采用主键索引的方式查询
    public function getRangeList(array $options, $tablename, $startPK, $endPK, $limit = 15)
    {
        // 根据表区分主键值
        if (empty($startPK)) {
            if ($tablename == 'xs_circle_topic') {
                $startPK = [
                    array('uid', null, PrimaryKeyTypeConst::CONST_INF_MAX),
                    array('topic_id', null, PrimaryKeyTypeConst::CONST_INF_MAX)
                ];
            } else if ($tablename == 'xs_circle_comment') {
                $startPK = [
                    array('topic_id', null, PrimaryKeyTypeConst::CONST_INF_MAX),
                    array('cmtid', null, PrimaryKeyTypeConst::CONST_INF_MAX)
                ];
            }
        }

        if (empty($endPK)) {
            if ($tablename == 'xs_circle_topic') {
                $endPK = [
                    array('uid', null, PrimaryKeyTypeConst::CONST_INF_MIN),
                    array('topic_id', null, PrimaryKeyTypeConst::CONST_INF_MIN)
                ];
            } else if ($tablename == 'xs_circle_comment') {
                $endPK = [
                    array('topic_id', null, PrimaryKeyTypeConst::CONST_INF_MIN),
                    array('cmtid', null, PrimaryKeyTypeConst::CONST_INF_MIN)
                ];
            }
        }

        // 拼接筛选条件
        $subFilters = [];
        $fieldsNormal = array(
            'uid'          => 'int',
            'topic_id'     => 'int',
            'status'       => 'string',
            'atype'        => 'string',
            'cmtid'        => 'int',
            'admin_status' => 'string'
        );
        foreach ($fieldsNormal as $field => $type) {
            if (!isset($options[$field])) continue;
            if ($type == 'int') {
                if (!is_numeric($options[$field])) continue;
                $value = intval($options[$field]);
            } else if ($type == 'string') {
                if (empty(trim($options[$field]))) continue;
                $value = trim($options[$field]);
            } else {
                continue;
            }
            $subFilters[] = array(
                'column_name' => $field,
                'value'       => $value,
                'comparator'  => ComparatorTypeConst::CONST_EQUAL
            );
        }

        if (!empty($subFilters)) {
            if (count($subFilters) > 1) {
                $filter = array(
                    'logical_operator' => LogicalOperatorConst::CONST_AND,
                    'sub_filters'      => $subFilters
                );
            } else {
                $filter = $subFilters[0];
            }
        }

        if (ENV == 'dev') {
            $request = [
                'table_name'                  => $tablename,
                'max_versions'                => 1,
                'direction'                   => DirectionConst::CONST_BACKWARD,
                'inclusive_start_primary_key' => $startPK,
                'exclusive_end_primary_key'   => $endPK,
                'limit'                       => $limit,
            ];
            if (!empty($filter)) {
                $request['column_filter'] = $filter;
            }
            return $this->getDevRangeList($request);
        } else {
            return self::find($startPK, $endPK, DirectionConst::CONST_BACKWARD, $filter, $limit, $tablename);
        }
    }

    public function updateStatus($tablename, array $pk, array $update, $condition = self::COND_EXPECT_EXIST)
    {
        $puts = array();
        foreach ($update as $field => $value) {
            $puts[] = array($field, $value);
        }
        $request = [
            'table_name'                  => $tablename,
            'condition'                   => $condition,
            'primary_key'                 => $pk,
            'update_of_attribute_columns' => [
                'PUT' => $puts
            ],
            'return_content'              => [
                'return_type' => ReturnTypeConst::CONST_PK
            ]
        ];
        if (ENV == 'dev') {
            return $this->updateDevStatus($request);
        } else {
            return self::update($tablename, $pk, $puts, $condition);
        }
    }

    // 开发环境获取朋友圈数据
    public function getDevList($request)
    {
        $this->initCircleClient();
        try {
            $response = $this->client->search($request);
            return $this->_formatSearchRows($response);
        } catch (\Exception $e) {
            print_r($e->getMessage());
            return false;
        }
    }

    // 开发环境根据范围获取朋友圈
    public function getDevRangeList($request)
    {
        $this->initCircleClient();
        try {
            $response = $this->client->getRange($request);
            return $this->_formatRangeRows($response);
        } catch (\Exception $e) {
            print_r($e->getMessage());
            return false;
        }
    }

    // 开发环境更改朋友圈状态
    public function updateDevStatus($request)
    {
        $this->initCircleClient();
        try {
            $response = $this->client->updateRow($request);
            return $response;
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Condition check failed') !== false) {
                return true;
            }
            return false;
        }
    }

    public static function getLog(array $options, $desc = self::SEARCH_SORT_DESC, $page = 1, $limit = 20)
    {
        $limit = intval($limit);
        $offset = ($page - 1) * $limit;
        $querys = array();

        $fieldsNormal = array(
            'topic_id' => 'int',
            'uid'      => 'int',
            'status'   => 'string',
            'atype'    => 'string',
            'content'  => 'string',
        );
        $keywordFields = array('content');

        foreach ($fieldsNormal as $field => $type) {
            if (!isset($options[$field])) continue;
            if ($type == 'int') {
                if (!is_numeric($options[$field])) continue;
                $value = intval($options[$field]);
            } else if ($type == 'string') {
                if (empty(trim($options[$field]))) continue;
                $value = trim($options[$field]);
            } else {
                continue;
            }
            $querys[] = array(
                'query_type' => QueryTypeConst::TERM_QUERY,
                'query'      => array(
                    'field_name' => $field,
                    'term'       => $value
                )
            );
        }
        foreach ($keywordFields as $field) {
            if (isset($options[$field]) && !empty($options[$field])) {
                $querys[] = array(
                    'query_type' => QueryTypeConst::MATCH_PHRASE_QUERY,
                    'query'      => array(
                        'field_name' => $field,
                        'text'       => trim($options[$field]),
                    )
                );
            }
        }

        $request = array(
            'table_name'     => 'xs_circle_topic',
            'index_name'     => 'xs_circle_topic_search',
            'search_query'   => array(
                'offset'          => $offset,
                'limit'           => $limit,
                'get_total_count' => true,
                'query'           => !empty($querys) ? array(
                    'query_type' => QueryTypeConst::BOOL_QUERY,
                    'query'      => array(
                        'must_queries' => $querys
                    )
                ) : array(
                    'query_type' => QueryTypeConst::RANGE_QUERY,
                    'query'      => array(
                        'field_name'    => 'topic_id',
                        'range_from'    => 0,
                        'include_lower' => true,
                        'range_to'      => PHP_INT_MAX,
                        'include_upper' => false
                    )
                ),
                'sort'            => array(
                    array(
                        'pk_sort' => array(
                            'order' => $desc
                        )
                    ),
                ),
            ),
            'columns_to_get' => array(
                'return_type' => ColumnReturnTypeConst::RETURN_ALL,
            )
        );
        return self::search($request);
    }

    public function getTempList(array $options, $tablename, $index, $page = 1, $limit = 20)
    {
        $limit = intval($limit);
        $offset = ($page - 1) * $limit;
        $querys = array();
        $fieldsNormal = array(
            'uid'          => 'int',
            'topic_id'     => 'int',
            'status'       => 'string',
            'atype'        => 'string',
            'cmtid'        => 'int',
            'admin_status' => 'string',
            'content'      => 'string',
        );

        $keywordFields = array('content');

        foreach ($fieldsNormal as $field => $type) {
            if (!isset($options[$field])) continue;
            if ($type == 'int') {
                if (!is_numeric($options[$field])) continue;
                $value = intval($options[$field]);
            } else if ($type == 'string') {
                if (empty(trim($options[$field]))) continue;
                $value = trim($options[$field]);
            } else {
                continue;
            }
            $querys[] = array(
                'query_type' => QueryTypeConst::TERM_QUERY,
                'query'      => array(
                    'field_name' => $field,
                    'term'       => $value
                )
            );
        }

        foreach ($keywordFields as $field) {
            if (isset($options[$field]) && !empty($options[$field])) {
                $querys[] = array(
                    'query_type' => QueryTypeConst::MATCH_PHRASE_QUERY,
                    'query'      => array(
                        'field_name' => $field,
                        'text'       => trim($options[$field]),
                    )
                );
            }
        }

        $request = array(
            'table_name'     => $tablename,
            'index_name'     => $index,
            'search_query'   => array(
                'offset'          => $offset,
                'limit'           => $limit,
                'get_total_count' => true,
                'query'           => !empty($querys) ? array(
                    'query_type' => QueryTypeConst::BOOL_QUERY,
                    'query'      => array(
                        'should_queries' => $querys
                    )
                ) : array(
                    'query_type' => QueryTypeConst::RANGE_QUERY,
                    'query'      => array(
                        'field_name'    => 'topic_id',
                        'range_from'    => 0,
                        'include_lower' => true,
                        'range_to'      => PHP_INT_MAX,
                        'include_upper' => false
                    )
                ),
                'sort'            => array(
                    array(
                        'pk_sort' => array(
                            'order' => self::SEARCH_SORT_DESC
                        )
                    ),
                )
            ),
            'columns_to_get' => array(
                'return_type' => ColumnReturnTypeConst::RETURN_ALL,
            )
        );

        if (ENV == 'dev') {
            return $this->getDevList($request);
        } else {
            return self::search($request);
        }
    }

    // 开发环境初始化客户端
    protected function initCircleClient()
    {
        $this->client = new OTSClient(array(
            'EndPoint'        => self::CircleDev_EndPoint,
            'AccessKeyID'     => self::CircleDev_AccessKeyID,
            'AccessKeySecret' => self::CircleDev_AccessKeySecret,
            'InstanceName'    => self::CircleDev_InstanceName,
            'SocketTimeout'   => 5,
        ));
    }

    // 返回客户端
    protected function getOtsClient()
    {
        if (ENV == 'dev') {
            $this->initCircleClient();
            return $this->client;
        }
        $this->initClient();
        return $this->_client;
    }
}