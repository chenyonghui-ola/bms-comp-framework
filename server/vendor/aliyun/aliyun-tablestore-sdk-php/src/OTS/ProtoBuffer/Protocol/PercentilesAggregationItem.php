<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: table_store_search.proto

namespace Aliyun\OTS\ProtoBuffer\Protocol;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>aliyun.OTS.ProtoBuffer.Protocol.PercentilesAggregationItem</code>
 */
class PercentilesAggregationItem extends \Aliyun\OTS\ProtoBuffer\Protocol\Message
{
    /**
     * Generated from protobuf field <code>optional double key = 1;</code>
     */
    private $key = 0.0;
    private $has_key = false;
    /**
     * Generated from protobuf field <code>optional bytes value = 2;</code>
     */
    private $value = '';
    private $has_value = false;

    public function __construct() {
        \GPBMetadata\TableStoreSearch::initOnce();
        parent::__construct();
    }

    /**
     * Generated from protobuf field <code>optional double key = 1;</code>
     * @return float
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Generated from protobuf field <code>optional double key = 1;</code>
     * @param float $var
     * @return $this
     */
    public function setKey($var)
    {
        GPBUtil::checkDouble($var);
        $this->key = $var;
        $this->has_key = true;

        return $this;
    }

    public function hasKey()
    {
        return $this->has_key;
    }

    /**
     * Generated from protobuf field <code>optional bytes value = 2;</code>
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Generated from protobuf field <code>optional bytes value = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setValue($var)
    {
        GPBUtil::checkString($var, False);
        $this->value = $var;
        $this->has_value = true;

        return $this;
    }

    public function hasValue()
    {
        return $this->has_value;
    }

}

