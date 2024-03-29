<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: table_store_search.proto

namespace Aliyun\OTS\ProtoBuffer\Protocol;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>aliyun.OTS.ProtoBuffer.Protocol.TermsQuery</code>
 */
class TermsQuery extends \Aliyun\OTS\ProtoBuffer\Protocol\Message
{
    /**
     * Generated from protobuf field <code>optional string field_name = 1;</code>
     */
    private $field_name = '';
    private $has_field_name = false;
    /**
     * Generated from protobuf field <code>repeated bytes terms = 2;</code>
     */
    private $terms;
    private $has_terms = false;
    /**
     * Generated from protobuf field <code>optional float weight = 3;</code>
     */
    private $weight = 0.0;
    private $has_weight = false;

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
     * Generated from protobuf field <code>repeated bytes terms = 2;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getTerms()
    {
        return $this->terms;
    }

    /**
     * Generated from protobuf field <code>repeated bytes terms = 2;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setTerms($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::BYTES);
        $this->terms = $arr;
        $this->has_terms = true;

        return $this;
    }

    public function hasTerms()
    {
        return $this->has_terms;
    }

    /**
     * Generated from protobuf field <code>optional float weight = 3;</code>
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Generated from protobuf field <code>optional float weight = 3;</code>
     * @param float $var
     * @return $this
     */
    public function setWeight($var)
    {
        GPBUtil::checkFloat($var);
        $this->weight = $var;
        $this->has_weight = true;

        return $this;
    }

    public function hasWeight()
    {
        return $this->has_weight;
    }

}

