<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: table_store_search.proto

namespace Aliyun\OTS\ProtoBuffer\Protocol;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>aliyun.OTS.ProtoBuffer.Protocol.SearchIndexSplitsOptions</code>
 */
class SearchIndexSplitsOptions extends \Aliyun\OTS\ProtoBuffer\Protocol\Message
{
    /**
     * Generated from protobuf field <code>optional string index_name = 1;</code>
     */
    private $index_name = '';
    private $has_index_name = false;

    public function __construct() {
        \GPBMetadata\TableStoreSearch::initOnce();
        parent::__construct();
    }

    /**
     * Generated from protobuf field <code>optional string index_name = 1;</code>
     * @return string
     */
    public function getIndexName()
    {
        return $this->index_name;
    }

    /**
     * Generated from protobuf field <code>optional string index_name = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setIndexName($var)
    {
        GPBUtil::checkString($var, True);
        $this->index_name = $var;
        $this->has_index_name = true;

        return $this;
    }

    public function hasIndexName()
    {
        return $this->has_index_name;
    }

}

