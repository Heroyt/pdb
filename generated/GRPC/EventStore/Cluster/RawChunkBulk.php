<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: cluster.proto

namespace GRPC\EventStore\Cluster;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>event_store.cluster.RawChunkBulk</code>
 */
class RawChunkBulk extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>bytes leader_id = 1;</code>
     */
    protected $leader_id = '';
    /**
     * Generated from protobuf field <code>bytes subscription_id = 2;</code>
     */
    protected $subscription_id = '';
    /**
     * Generated from protobuf field <code>int32 chunk_start_number = 3;</code>
     */
    protected $chunk_start_number = 0;
    /**
     * Generated from protobuf field <code>int32 chunk_end_number = 4;</code>
     */
    protected $chunk_end_number = 0;
    /**
     * Generated from protobuf field <code>int32 raw_position = 5;</code>
     */
    protected $raw_position = 0;
    /**
     * Generated from protobuf field <code>bytes raw_bytes = 6;</code>
     */
    protected $raw_bytes = '';
    /**
     * Generated from protobuf field <code>bool complete_chunk = 7;</code>
     */
    protected $complete_chunk = false;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $leader_id
     *     @type string $subscription_id
     *     @type int $chunk_start_number
     *     @type int $chunk_end_number
     *     @type int $raw_position
     *     @type string $raw_bytes
     *     @type bool $complete_chunk
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Cluster::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>bytes leader_id = 1;</code>
     * @return string
     */
    public function getLeaderId()
    {
        return $this->leader_id;
    }

    /**
     * Generated from protobuf field <code>bytes leader_id = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setLeaderId($var)
    {
        GPBUtil::checkString($var, False);
        $this->leader_id = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bytes subscription_id = 2;</code>
     * @return string
     */
    public function getSubscriptionId()
    {
        return $this->subscription_id;
    }

    /**
     * Generated from protobuf field <code>bytes subscription_id = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setSubscriptionId($var)
    {
        GPBUtil::checkString($var, False);
        $this->subscription_id = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 chunk_start_number = 3;</code>
     * @return int
     */
    public function getChunkStartNumber()
    {
        return $this->chunk_start_number;
    }

    /**
     * Generated from protobuf field <code>int32 chunk_start_number = 3;</code>
     * @param int $var
     * @return $this
     */
    public function setChunkStartNumber($var)
    {
        GPBUtil::checkInt32($var);
        $this->chunk_start_number = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 chunk_end_number = 4;</code>
     * @return int
     */
    public function getChunkEndNumber()
    {
        return $this->chunk_end_number;
    }

    /**
     * Generated from protobuf field <code>int32 chunk_end_number = 4;</code>
     * @param int $var
     * @return $this
     */
    public function setChunkEndNumber($var)
    {
        GPBUtil::checkInt32($var);
        $this->chunk_end_number = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 raw_position = 5;</code>
     * @return int
     */
    public function getRawPosition()
    {
        return $this->raw_position;
    }

    /**
     * Generated from protobuf field <code>int32 raw_position = 5;</code>
     * @param int $var
     * @return $this
     */
    public function setRawPosition($var)
    {
        GPBUtil::checkInt32($var);
        $this->raw_position = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bytes raw_bytes = 6;</code>
     * @return string
     */
    public function getRawBytes()
    {
        return $this->raw_bytes;
    }

    /**
     * Generated from protobuf field <code>bytes raw_bytes = 6;</code>
     * @param string $var
     * @return $this
     */
    public function setRawBytes($var)
    {
        GPBUtil::checkString($var, False);
        $this->raw_bytes = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bool complete_chunk = 7;</code>
     * @return bool
     */
    public function getCompleteChunk()
    {
        return $this->complete_chunk;
    }

    /**
     * Generated from protobuf field <code>bool complete_chunk = 7;</code>
     * @param bool $var
     * @return $this
     */
    public function setCompleteChunk($var)
    {
        GPBUtil::checkBool($var);
        $this->complete_chunk = $var;

        return $this;
    }

}

