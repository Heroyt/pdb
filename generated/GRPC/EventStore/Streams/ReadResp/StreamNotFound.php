<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: streams.proto

namespace GRPC\EventStore\Streams\ReadResp;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>event_store.client.streams.ReadResp.StreamNotFound</code>
 */
class StreamNotFound extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.event_store.client.StreamIdentifier stream_identifier = 1;</code>
     */
    protected $stream_identifier = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \GRPC\EventStore\Shared\StreamIdentifier $stream_identifier
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Streams::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.StreamIdentifier stream_identifier = 1;</code>
     * @return \GRPC\EventStore\Shared\StreamIdentifier|null
     */
    public function getStreamIdentifier()
    {
        return $this->stream_identifier;
    }

    /**
     * Generated from protobuf field <code>.event_store.client.StreamIdentifier stream_identifier = 1;</code>
     * @param \GRPC\EventStore\Shared\StreamIdentifier $var
     * @return $this
     */
    public function setStreamIdentifier($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Shared\StreamIdentifier::class);
        $this->stream_identifier = $var;

        return $this;
    }

    public function hasStreamIdentifier()
    {
        return isset($this->stream_identifier);
    }

    public function clearStreamIdentifier()
    {
        unset($this->stream_identifier);
    }

}

