<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: table_store_search.proto

namespace Aliyun\OTS\ProtoBuffer\Protocol;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>aliyun.OTS.ProtoBuffer.Protocol.GroupByFieldResult</code>
 */
class GroupByFieldResult extends \Aliyun\OTS\ProtoBuffer\Protocol\Message
{
    /**
     * Generated from protobuf field <code>repeated .aliyun.OTS.ProtoBuffer.Protocol.GroupByFieldResultItem group_by_field_result_items = 1;</code>
     */
    private $group_by_field_result_items;
    private $has_group_by_field_result_items = false;

    public function __construct() {
        \GPBMetadata\TableStoreSearch::initOnce();
        parent::__construct();
    }

    /**
     * Generated from protobuf field <code>repeated .aliyun.OTS.ProtoBuffer.Protocol.GroupByFieldResultItem group_by_field_result_items = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getGroupByFieldResultItems()
    {
        return $this->group_by_field_result_items;
    }

    /**
     * Generated from protobuf field <code>repeated .aliyun.OTS.ProtoBuffer.Protocol.GroupByFieldResultItem group_by_field_result_items = 1;</code>
     * @param \Aliyun\OTS\ProtoBuffer\Protocol\GroupByFieldResultItem[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setGroupByFieldResultItems($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Aliyun\OTS\ProtoBuffer\Protocol\GroupByFieldResultItem::class);
        $this->group_by_field_result_items = $arr;
        $this->has_group_by_field_result_items = true;

        return $this;
    }

    public function hasGroupByFieldResultItems()
    {
        return $this->has_group_by_field_result_items;
    }

}

