<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: table_store_search.proto

namespace Aliyun\OTS\ProtoBuffer\Protocol;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>aliyun.OTS.ProtoBuffer.Protocol.GroupByGeoDistanceResultItem</code>
 */
class GroupByGeoDistanceResultItem extends \Aliyun\OTS\ProtoBuffer\Protocol\Message
{
    /**
     * Generated from protobuf field <code>optional double from = 1;</code>
     */
    private $from = 0.0;
    private $has_from = false;
    /**
     * Generated from protobuf field <code>optional double to = 2;</code>
     */
    private $to = 0.0;
    private $has_to = false;
    /**
     * Generated from protobuf field <code>optional int64 row_count = 3;</code>
     */
    private $row_count = 0;
    private $has_row_count = false;
    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.AggregationsResult sub_aggs_result = 4;</code>
     */
    private $sub_aggs_result = null;
    private $has_sub_aggs_result = false;
    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.GroupBysResult sub_group_bys_result = 5;</code>
     */
    private $sub_group_bys_result = null;
    private $has_sub_group_bys_result = false;

    public function __construct() {
        \GPBMetadata\TableStoreSearch::initOnce();
        parent::__construct();
    }

    /**
     * Generated from protobuf field <code>optional double from = 1;</code>
     * @return float
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Generated from protobuf field <code>optional double from = 1;</code>
     * @param float $var
     * @return $this
     */
    public function setFrom($var)
    {
        GPBUtil::checkDouble($var);
        $this->from = $var;
        $this->has_from = true;

        return $this;
    }

    public function hasFrom()
    {
        return $this->has_from;
    }

    /**
     * Generated from protobuf field <code>optional double to = 2;</code>
     * @return float
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Generated from protobuf field <code>optional double to = 2;</code>
     * @param float $var
     * @return $this
     */
    public function setTo($var)
    {
        GPBUtil::checkDouble($var);
        $this->to = $var;
        $this->has_to = true;

        return $this;
    }

    public function hasTo()
    {
        return $this->has_to;
    }

    /**
     * Generated from protobuf field <code>optional int64 row_count = 3;</code>
     * @return int|string
     */
    public function getRowCount()
    {
        return $this->row_count;
    }

    /**
     * Generated from protobuf field <code>optional int64 row_count = 3;</code>
     * @param int|string $var
     * @return $this
     */
    public function setRowCount($var)
    {
        GPBUtil::checkInt64($var);
        $this->row_count = $var;
        $this->has_row_count = true;

        return $this;
    }

    public function hasRowCount()
    {
        return $this->has_row_count;
    }

    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.AggregationsResult sub_aggs_result = 4;</code>
     * @return \Aliyun\OTS\ProtoBuffer\Protocol\AggregationsResult
     */
    public function getSubAggsResult()
    {
        return $this->sub_aggs_result;
    }

    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.AggregationsResult sub_aggs_result = 4;</code>
     * @param \Aliyun\OTS\ProtoBuffer\Protocol\AggregationsResult $var
     * @return $this
     */
    public function setSubAggsResult($var)
    {
        GPBUtil::checkMessage($var, \Aliyun\OTS\ProtoBuffer\Protocol\AggregationsResult::class);
        $this->sub_aggs_result = $var;
        $this->has_sub_aggs_result = true;

        return $this;
    }

    public function hasSubAggsResult()
    {
        return $this->has_sub_aggs_result;
    }

    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.GroupBysResult sub_group_bys_result = 5;</code>
     * @return \Aliyun\OTS\ProtoBuffer\Protocol\GroupBysResult
     */
    public function getSubGroupBysResult()
    {
        return $this->sub_group_bys_result;
    }

    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.GroupBysResult sub_group_bys_result = 5;</code>
     * @param \Aliyun\OTS\ProtoBuffer\Protocol\GroupBysResult $var
     * @return $this
     */
    public function setSubGroupBysResult($var)
    {
        GPBUtil::checkMessage($var, \Aliyun\OTS\ProtoBuffer\Protocol\GroupBysResult::class);
        $this->sub_group_bys_result = $var;
        $this->has_sub_group_bys_result = true;

        return $this;
    }

    public function hasSubGroupBysResult()
    {
        return $this->has_sub_group_bys_result;
    }

}

