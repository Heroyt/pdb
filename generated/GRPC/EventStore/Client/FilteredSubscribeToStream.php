<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: ClientMessageDtos.proto

namespace GRPC\EventStore\Client;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>EventStore.Client.Messages.FilteredSubscribeToStream</code>
 */
class FilteredSubscribeToStream extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string event_stream_id = 1;</code>
     */
    protected $event_stream_id = '';
    /**
     * Generated from protobuf field <code>bool resolve_link_tos = 2;</code>
     */
    protected $resolve_link_tos = false;
    /**
     * Generated from protobuf field <code>.EventStore.Client.Messages.Filter filter = 3;</code>
     */
    protected $filter = null;
    /**
     * Generated from protobuf field <code>int32 checkpoint_interval = 4;</code>
     */
    protected $checkpoint_interval = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $event_stream_id
     *     @type bool $resolve_link_tos
     *     @type \GRPC\EventStore\Client\Filter $filter
     *     @type int $checkpoint_interval
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\ClientMessageDtos::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string event_stream_id = 1;</code>
     * @return string
     */
    public function getEventStreamId()
    {
        return $this->event_stream_id;
    }

    /**
     * Generated from protobuf field <code>string event_stream_id = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setEventStreamId($var)
    {
        GPBUtil::checkString($var, True);
        $this->event_stream_id = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bool resolve_link_tos = 2;</code>
     * @return bool
     */
    public function getResolveLinkTos()
    {
        return $this->resolve_link_tos;
    }

    /**
     * Generated from protobuf field <code>bool resolve_link_tos = 2;</code>
     * @param bool $var
     * @return $this
     */
    public function setResolveLinkTos($var)
    {
        GPBUtil::checkBool($var);
        $this->resolve_link_tos = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.EventStore.Client.Messages.Filter filter = 3;</code>
     * @return \GRPC\EventStore\Client\Filter|null
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Generated from protobuf field <code>.EventStore.Client.Messages.Filter filter = 3;</code>
     * @param \GRPC\EventStore\Client\Filter $var
     * @return $this
     */
    public function setFilter($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Client\Filter::class);
        $this->filter = $var;

        return $this;
    }

    public function hasFilter()
    {
        return isset($this->filter);
    }

    public function clearFilter()
    {
        unset($this->filter);
    }

    /**
     * Generated from protobuf field <code>int32 checkpoint_interval = 4;</code>
     * @return int
     */
    public function getCheckpointInterval()
    {
        return $this->checkpoint_interval;
    }

    /**
     * Generated from protobuf field <code>int32 checkpoint_interval = 4;</code>
     * @param int $var
     * @return $this
     */
    public function setCheckpointInterval($var)
    {
        GPBUtil::checkInt32($var);
        $this->checkpoint_interval = $var;

        return $this;
    }

}

