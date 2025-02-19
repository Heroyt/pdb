<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: ClientMessageDtos.proto

namespace GRPC\EventStore\Client;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>EventStore.Client.Messages.ConnectToPersistentSubscription</code>
 */
class ConnectToPersistentSubscription extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string subscription_id = 1;</code>
     */
    protected $subscription_id = '';
    /**
     * Generated from protobuf field <code>string event_stream_id = 2;</code>
     */
    protected $event_stream_id = '';
    /**
     * Generated from protobuf field <code>int32 allowed_in_flight_messages = 3;</code>
     */
    protected $allowed_in_flight_messages = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $subscription_id
     *     @type string $event_stream_id
     *     @type int $allowed_in_flight_messages
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\ClientMessageDtos::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string subscription_id = 1;</code>
     * @return string
     */
    public function getSubscriptionId()
    {
        return $this->subscription_id;
    }

    /**
     * Generated from protobuf field <code>string subscription_id = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setSubscriptionId($var)
    {
        GPBUtil::checkString($var, True);
        $this->subscription_id = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string event_stream_id = 2;</code>
     * @return string
     */
    public function getEventStreamId()
    {
        return $this->event_stream_id;
    }

    /**
     * Generated from protobuf field <code>string event_stream_id = 2;</code>
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
     * Generated from protobuf field <code>int32 allowed_in_flight_messages = 3;</code>
     * @return int
     */
    public function getAllowedInFlightMessages()
    {
        return $this->allowed_in_flight_messages;
    }

    /**
     * Generated from protobuf field <code>int32 allowed_in_flight_messages = 3;</code>
     * @param int $var
     * @return $this
     */
    public function setAllowedInFlightMessages($var)
    {
        GPBUtil::checkInt32($var);
        $this->allowed_in_flight_messages = $var;

        return $this;
    }

}

