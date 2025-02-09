<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: ClientMessageDtos.proto

namespace GRPC\EventStore\Client;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>EventStore.Client.Messages.ReadEventCompleted</code>
 */
class ReadEventCompleted extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.EventStore.Client.Messages.ReadEventCompleted.ReadEventResult result = 1;</code>
     */
    protected $result = 0;
    /**
     * Generated from protobuf field <code>.EventStore.Client.Messages.ResolvedIndexedEvent event = 2;</code>
     */
    protected $event = null;
    /**
     * Generated from protobuf field <code>string error = 3;</code>
     */
    protected $error = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $result
     *     @type \GRPC\EventStore\Client\ResolvedIndexedEvent $event
     *     @type string $error
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\ClientMessageDtos::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.EventStore.Client.Messages.ReadEventCompleted.ReadEventResult result = 1;</code>
     * @return int
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Generated from protobuf field <code>.EventStore.Client.Messages.ReadEventCompleted.ReadEventResult result = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setResult($var)
    {
        GPBUtil::checkEnum($var, \GRPC\EventStore\Client\ReadEventCompleted\ReadEventResult::class);
        $this->result = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.EventStore.Client.Messages.ResolvedIndexedEvent event = 2;</code>
     * @return \GRPC\EventStore\Client\ResolvedIndexedEvent|null
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Generated from protobuf field <code>.EventStore.Client.Messages.ResolvedIndexedEvent event = 2;</code>
     * @param \GRPC\EventStore\Client\ResolvedIndexedEvent $var
     * @return $this
     */
    public function setEvent($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Client\ResolvedIndexedEvent::class);
        $this->event = $var;

        return $this;
    }

    public function hasEvent()
    {
        return isset($this->event);
    }

    public function clearEvent()
    {
        unset($this->event);
    }

    /**
     * Generated from protobuf field <code>string error = 3;</code>
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Generated from protobuf field <code>string error = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setError($var)
    {
        GPBUtil::checkString($var, True);
        $this->error = $var;

        return $this;
    }

}

