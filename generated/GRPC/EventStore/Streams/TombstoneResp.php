<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: streams.proto

namespace GRPC\EventStore\Streams;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>event_store.client.streams.TombstoneResp</code>
 */
class TombstoneResp extends \Google\Protobuf\Internal\Message
{
    protected $position_option;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \GRPC\EventStore\Streams\TombstoneResp\Position $position
     *     @type \GRPC\EventStore\Shared\PBEmpty $no_position
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Streams::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.streams.TombstoneResp.Position position = 1;</code>
     * @return \GRPC\EventStore\Streams\TombstoneResp\Position|null
     */
    public function getPosition()
    {
        return $this->readOneof(1);
    }

    public function hasPosition()
    {
        return $this->hasOneof(1);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.streams.TombstoneResp.Position position = 1;</code>
     * @param \GRPC\EventStore\Streams\TombstoneResp\Position $var
     * @return $this
     */
    public function setPosition($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Streams\TombstoneResp\Position::class);
        $this->writeOneof(1, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty no_position = 2;</code>
     * @return \GRPC\EventStore\Shared\PBEmpty|null
     */
    public function getNoPosition()
    {
        return $this->readOneof(2);
    }

    public function hasNoPosition()
    {
        return $this->hasOneof(2);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty no_position = 2;</code>
     * @param \GRPC\EventStore\Shared\PBEmpty $var
     * @return $this
     */
    public function setNoPosition($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Shared\PBEmpty::class);
        $this->writeOneof(2, $var);

        return $this;
    }

    /**
     * @return string
     */
    public function getPositionOption()
    {
        return $this->whichOneof("position_option");
    }

}

