<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: streams.proto

namespace GRPC\EventStore\Streams;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>event_store.client.streams.BatchAppendReq</code>
 */
class BatchAppendReq extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.event_store.client.UUID correlation_id = 1;</code>
     */
    protected $correlation_id = null;
    /**
     * Generated from protobuf field <code>.event_store.client.streams.BatchAppendReq.Options options = 2;</code>
     */
    protected $options = null;
    /**
     * Generated from protobuf field <code>bool is_final = 4;</code>
     */
    protected $is_final = false;
    /**
     * Generated from protobuf field <code>repeated .event_store.client.streams.BatchAppendReq.ProposedMessage proposed_messages = 3;</code>
     */
    private $proposed_messages;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \GRPC\EventStore\Shared\UUID $correlation_id
     *     @type \GRPC\EventStore\Streams\BatchAppendReq\Options $options
     *     @type array<\GRPC\EventStore\Streams\BatchAppendReq\ProposedMessage>|\Google\Protobuf\Internal\RepeatedField $proposed_messages
     *     @type bool $is_final
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Streams::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.UUID correlation_id = 1;</code>
     * @return \GRPC\EventStore\Shared\UUID|null
     */
    public function getCorrelationId()
    {
        return $this->correlation_id;
    }

    /**
     * Generated from protobuf field <code>.event_store.client.UUID correlation_id = 1;</code>
     * @param \GRPC\EventStore\Shared\UUID $var
     * @return $this
     */
    public function setCorrelationId($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Shared\UUID::class);
        $this->correlation_id = $var;

        return $this;
    }

    public function hasCorrelationId()
    {
        return isset($this->correlation_id);
    }

    public function clearCorrelationId()
    {
        unset($this->correlation_id);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.streams.BatchAppendReq.Options options = 2;</code>
     * @return \GRPC\EventStore\Streams\BatchAppendReq\Options|null
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Generated from protobuf field <code>.event_store.client.streams.BatchAppendReq.Options options = 2;</code>
     * @param \GRPC\EventStore\Streams\BatchAppendReq\Options $var
     * @return $this
     */
    public function setOptions($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Streams\BatchAppendReq\Options::class);
        $this->options = $var;

        return $this;
    }

    public function hasOptions()
    {
        return isset($this->options);
    }

    public function clearOptions()
    {
        unset($this->options);
    }

    /**
     * Generated from protobuf field <code>repeated .event_store.client.streams.BatchAppendReq.ProposedMessage proposed_messages = 3;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getProposedMessages()
    {
        return $this->proposed_messages;
    }

    /**
     * Generated from protobuf field <code>repeated .event_store.client.streams.BatchAppendReq.ProposedMessage proposed_messages = 3;</code>
     * @param array<\GRPC\EventStore\Streams\BatchAppendReq\ProposedMessage>|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setProposedMessages($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \GRPC\EventStore\Streams\BatchAppendReq\ProposedMessage::class);
        $this->proposed_messages = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bool is_final = 4;</code>
     * @return bool
     */
    public function getIsFinal()
    {
        return $this->is_final;
    }

    /**
     * Generated from protobuf field <code>bool is_final = 4;</code>
     * @param bool $var
     * @return $this
     */
    public function setIsFinal($var)
    {
        GPBUtil::checkBool($var);
        $this->is_final = $var;

        return $this;
    }

}

