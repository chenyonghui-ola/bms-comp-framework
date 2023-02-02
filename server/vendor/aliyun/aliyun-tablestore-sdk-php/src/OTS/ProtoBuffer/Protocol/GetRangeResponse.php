<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: table_store.proto

namespace Aliyun\OTS\ProtoBuffer\Protocol;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>aliyun.OTS.ProtoBuffer.Protocol.GetRangeResponse</code>
 */
class GetRangeResponse extends \Aliyun\OTS\ProtoBuffer\Protocol\Message
{
    /**
     * Generated from protobuf field <code>required .aliyun.OTS.ProtoBuffer.Protocol.ConsumedCapacity consumed = 1;</code>
     */
    private $consumed = null;
    private $has_consumed = false;
    /**
     * encoded as InplaceRowChangeSet
     *
     * Generated from protobuf field <code>required bytes rows = 2;</code>
     */
    private $rows = '';
    private $has_rows = false;
    /**
     * 若为空，则代表数据全部读取完毕. encoded as InplaceRowChangeSet, but only has primary key
     *
     * Generated from protobuf field <code>optional bytes next_start_primary_key = 3;</code>
     */
    private $next_start_primary_key = '';
    private $has_next_start_primary_key = false;
    /**
     * Generated from protobuf field <code>optional bytes next_token = 4;</code>
     */
    private $next_token = '';
    private $has_next_token = false;
    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.DataBlockType data_block_type = 5;</code>
     */
    private $data_block_type = 0;
    private $has_data_block_type = false;
    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.CompressType compress_type = 6;</code>
     */
    private $compress_type = 0;
    private $has_compress_type = false;

    public function __construct() {
        \GPBMetadata\TableStore::initOnce();
        parent::__construct();
    }

    /**
     * Generated from protobuf field <code>required .aliyun.OTS.ProtoBuffer.Protocol.ConsumedCapacity consumed = 1;</code>
     * @return \Aliyun\OTS\ProtoBuffer\Protocol\ConsumedCapacity
     */
    public function getConsumed()
    {
        return $this->consumed;
    }

    /**
     * Generated from protobuf field <code>required .aliyun.OTS.ProtoBuffer.Protocol.ConsumedCapacity consumed = 1;</code>
     * @param \Aliyun\OTS\ProtoBuffer\Protocol\ConsumedCapacity $var
     * @return $this
     */
    public function setConsumed($var)
    {
        GPBUtil::checkMessage($var, \Aliyun\OTS\ProtoBuffer\Protocol\ConsumedCapacity::class);
        $this->consumed = $var;
        $this->has_consumed = true;

        return $this;
    }

    public function hasConsumed()
    {
        return $this->has_consumed;
    }

    /**
     * encoded as InplaceRowChangeSet
     *
     * Generated from protobuf field <code>required bytes rows = 2;</code>
     * @return string
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * encoded as InplaceRowChangeSet
     *
     * Generated from protobuf field <code>required bytes rows = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setRows($var)
    {
        GPBUtil::checkString($var, False);
        $this->rows = $var;
        $this->has_rows = true;

        return $this;
    }

    public function hasRows()
    {
        return $this->has_rows;
    }

    /**
     * 若为空，则代表数据全部读取完毕. encoded as InplaceRowChangeSet, but only has primary key
     *
     * Generated from protobuf field <code>optional bytes next_start_primary_key = 3;</code>
     * @return string
     */
    public function getNextStartPrimaryKey()
    {
        return $this->next_start_primary_key;
    }

    /**
     * 若为空，则代表数据全部读取完毕. encoded as InplaceRowChangeSet, but only has primary key
     *
     * Generated from protobuf field <code>optional bytes next_start_primary_key = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setNextStartPrimaryKey($var)
    {
        GPBUtil::checkString($var, False);
        $this->next_start_primary_key = $var;
        $this->has_next_start_primary_key = true;

        return $this;
    }

    public function hasNextStartPrimaryKey()
    {
        return $this->has_next_start_primary_key;
    }

    /**
     * Generated from protobuf field <code>optional bytes next_token = 4;</code>
     * @return string
     */
    public function getNextToken()
    {
        return $this->next_token;
    }

    /**
     * Generated from protobuf field <code>optional bytes next_token = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setNextToken($var)
    {
        GPBUtil::checkString($var, False);
        $this->next_token = $var;
        $this->has_next_token = true;

        return $this;
    }

    public function hasNextToken()
    {
        return $this->has_next_token;
    }

    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.DataBlockType data_block_type = 5;</code>
     * @return int
     */
    public function getDataBlockType()
    {
        return $this->data_block_type;
    }

    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.DataBlockType data_block_type = 5;</code>
     * @param int $var
     * @return $this
     */
    public function setDataBlockType($var)
    {
        GPBUtil::checkEnum($var, \Aliyun\OTS\ProtoBuffer\Protocol\DataBlockType::class);
        $this->data_block_type = $var;
        $this->has_data_block_type = true;

        return $this;
    }

    public function hasDataBlockType()
    {
        return $this->has_data_block_type;
    }

    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.CompressType compress_type = 6;</code>
     * @return int
     */
    public function getCompressType()
    {
        return $this->compress_type;
    }

    /**
     * Generated from protobuf field <code>optional .aliyun.OTS.ProtoBuffer.Protocol.CompressType compress_type = 6;</code>
     * @param int $var
     * @return $this
     */
    public function setCompressType($var)
    {
        GPBUtil::checkEnum($var, \Aliyun\OTS\ProtoBuffer\Protocol\CompressType::class);
        $this->compress_type = $var;
        $this->has_compress_type = true;

        return $this;
    }

    public function hasCompressType()
    {
        return $this->has_compress_type;
    }

}

