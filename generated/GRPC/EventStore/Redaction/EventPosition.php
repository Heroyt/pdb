<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: redaction.proto

namespace GRPC\EventStore\Redaction;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>event_store.client.redaction.EventPosition</code>
 */
class EventPosition extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>uint64 log_position = 1;</code>
     */
    protected $log_position = 0;
    /**
     * Generated from protobuf field <code>.event_store.client.redaction.ChunkInfo chunk_info = 2;</code>
     */
    protected $chunk_info = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int|string $log_position
     *     @type \GRPC\EventStore\Redaction\ChunkInfo $chunk_info
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Redaction::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>uint64 log_position = 1;</code>
     * @return int|string
     */
    public function getLogPosition()
    {
        return $this->log_position;
    }

    /**
     * Generated from protobuf field <code>uint64 log_position = 1;</code>
     * @param int|string $var
     * @return $this
     */
    public function setLogPosition($var)
    {
        GPBUtil::checkUint64($var);
        $this->log_position = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.event_store.client.redaction.ChunkInfo chunk_info = 2;</code>
     * @return \GRPC\EventStore\Redaction\ChunkInfo|null
     */
    public function getChunkInfo()
    {
        return $this->chunk_info;
    }

    /**
     * Generated from protobuf field <code>.event_store.client.redaction.ChunkInfo chunk_info = 2;</code>
     * @param \GRPC\EventStore\Redaction\ChunkInfo $var
     * @return $this
     */
    public function setChunkInfo($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Redaction\ChunkInfo::class);
        $this->chunk_info = $var;

        return $this;
    }

    public function hasChunkInfo()
    {
        return isset($this->chunk_info);
    }

    public function clearChunkInfo()
    {
        unset($this->chunk_info);
    }

}

