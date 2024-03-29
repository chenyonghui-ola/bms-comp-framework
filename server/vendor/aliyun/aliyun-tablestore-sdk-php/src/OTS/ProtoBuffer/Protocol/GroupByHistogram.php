<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: table_store_search.proto

namespace Aliyun\OTS\ProtoBuffer\Protocol;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>aliyun.OTS.ProtoBuffer.Protocol.GroupByHistogram</code>
 */
class GroupByHistogram extends \Aliyun\OTS\ProtoBuffer\Protocol\Message
{
    /**
     * Generated from protobuf field <code>optional string field_name = 1;</code>
     */
    private $field_name = '';
    private $has_field_name = false;
    /**
     * Generated from protobuf field <code>optional bytes interval = 2;</code>
     */
    private $interval = '';
    private $has_interval = false;
    /**
     * Generated from protobuf field <code>optional bytes missing = 3;</code>
     */
    private $missing = '';
    private $has_missing = false;
    /**
     * Generated from protobuf field <code>optional int64 min_doc_count = 4;</code>
     */
    private $min_doc_count = 0;
    private $has_min_doc_count = false;
    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.GroupBySort sort = 5;</code>
     */
    private $sort = null;
    private $has_sort = false;
    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.FieldRange field_range = 6;</code>
     */
    private $field_range = null;
    private $has_field_range = false;
    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.Aggregations sub_aggs = 7;</code>
     */
    private $sub_aggs = null;
    private $has_sub_aggs = false;
    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.GroupBys sub_group_bys = 8;</code>
     */
    private $sub_group_bys = null;
    private $has_sub_group_bys = false;

    public function __construct() {
        \GPBMetadata\TableStoreSearch::initOnce();
        parent::__construct();
    }

    /**
     * Generated from protobuf field <code>optional string field_name = 1;</code>
     * @return string
     */
    public function getFieldName()
    {
        return $this->field_name;
    }

    /**
     * Generated from protobuf field <code>optional string field_name = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setFieldName($var)
    {
        GPBUtil::checkString($var, True);
        $this->field_name = $var;
        $this->has_field_name = true;

        return $this;
    }

    public function hasFieldName()
    {
        return $this->has_field_name;
    }

    /**
     * Generated from protobuf field <code>optional bytes interval = 2;</code>
     * @return string
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * Generated from protobuf field <code>optional bytes interval = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setInterval($var)
    {
        GPBUtil::checkString($var, False);
        $this->interval = $var;
        $this->has_interval = true;

        return $this;
    }

    public function hasInterval()
    {
        return $this->has_interval;
    }

    /**
     * Generated from protobuf field <code>optional bytes missing = 3;</code>
     * @return string
     */
    public function getMissing()
    {
        return $this->missing;
    }

    /**
     * Generated from protobuf field <code>optional bytes missing = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setMissing($var)
    {
        GPBUtil::checkString($var, False);
        $this->missing = $var;
        $this->has_missing = true;

        return $this;
    }

    public function hasMissing()
    {
        return $this->has_missing;
    }

    /**
     * Generated from protobuf field <code>optional int64 min_doc_count = 4;</code>
     * @return int|string
     */
    public function getMinDocCount()
    {
        return $this->min_doc_count;
    }

    /**
     * Generated from protobuf field <code>optional int64 min_doc_count = 4;</code>
     * @param int|string $var
     * @return $this
     */
    public function setMinDocCount($var)
    {
        GPBUtil::checkInt64($var);
        $this->min_doc_count = $var;
        $this->has_min_doc_count = true;

        return $this;
    }

    public function hasMinDocCount()
    {
        return $this->has_min_doc_count;
    }

    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.GroupBySort sort = 5;</code>
     * @return \Aliyun\OTS\ProtoBuffer\Protocol\GroupBySort
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.GroupBySort sort = 5;</code>
     * @param \Aliyun\OTS\ProtoBuffer\Protocol\GroupBySort $var
     * @return $this
     */
    public function setSort($var)
    {
        GPBUtil::checkMessage($var, \Aliyun\OTS\ProtoBuffer\Protocol\GroupBySort::class);
        $this->sort = $var;
        $this->has_sort = true;

        return $this;
    }

    public function hasSort()
    {
        return $this->has_sort;
    }

    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.FieldRange field_range = 6;</code>
     * @return \Aliyun\OTS\ProtoBuffer\Protocol\FieldRange
     */
    public function getFieldRange()
    {
        return $this->field_range;
    }

    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.FieldRange field_range = 6;</code>
     * @param \Aliyun\OTS\ProtoBuffer\Protocol\FieldRange $var
     * @return $this
     */
    public function setFieldRange($var)
    {
        GPBUtil::checkMessage($var, \Aliyun\OTS\ProtoBuffer\Protocol\FieldRange::class);
        $this->field_range = $var;
        $this->has_field_range = true;

        return $this;
    }

    public function hasFieldRange()
    {
        return $this->has_field_range;
    }

    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.Aggregations sub_aggs = 7;</code>
     * @return \Aliyun\OTS\ProtoBuffer\Protocol\Aggregations
     */
    public function getSubAggs()
    {
        return $this->sub_aggs;
    }

    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.Aggregations sub_aggs = 7;</code>
     * @param \Aliyun\OTS\ProtoBuffer\Protocol\Aggregations $var
     * @return $this
     */
    public function setSubAggs($var)
    {
        GPBUtil::checkMessage($var, \Aliyun\OTS\ProtoBuffer\Protocol\Aggregations::class);
        $this->sub_aggs = $var;
        $this->has_sub_aggs = true;

        return $this;
    }

    public function hasSubAggs()
    {
        return $this->has_sub_aggs;
    }

    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.GroupBys sub_group_bys = 8;</code>
     * @return \Aliyun\OTS\ProtoBuffer\Protocol\GroupBys
     */
    public function getSubGroupBys()
    {
        return $this->sub_group_bys;
    }

    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.GroupBys sub_group_bys = 8;</code>
     * @param \Aliyun\OTS\ProtoBuffer\Protocol\GroupBys $var
     * @return $this
     */
    public function setSubGroupBys($var)
    {
        GPBUtil::checkMessage($var, \Aliyun\OTS\ProtoBuffer\Protocol\GroupBys::class);
        $this->sub_group_bys = $var;
        $this->has_sub_group_bys = true;

        return $this;
    }

    public function hasSubGroupBys()
    {
        return $this->has_sub_group_bys;
    }

}

