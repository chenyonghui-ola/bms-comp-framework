<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: table_store_search.proto

namespace Aliyun\OTS\ProtoBuffer\Protocol;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>aliyun.OTS.ProtoBuffer.Protocol.SplitAnalyzerParameter</code>
 */
class SplitAnalyzerParameter extends \Aliyun\OTS\ProtoBuffer\Protocol\Message
{
    /**
     * Generated from protobuf field <code>optional string delimiter = 1;</code>
     */
    private $delimiter = '';
    private $has_delimiter = false;

    public function __construct() {
        \GPBMetadata\TableStoreSearch::initOnce();
        parent::__construct();
    }

    /**
     * Generated from protobuf field <code>optional string delimiter = 1;</code>
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * Generated from protobuf field <code>optional string delimiter = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setDelimiter($var)
    {
        GPBUtil::checkString($var, True);
        $this->delimiter = $var;
        $this->has_delimiter = true;

        return $this;
    }

    public function hasDelimiter()
    {
        return $this->has_delimiter;
    }

}

