<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: ClientMessageDtos.proto

namespace GRPC\EventStore\Client;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>EventStore.Client.Messages.UpdatePersistentSubscriptionCompleted</code>
 */
class UpdatePersistentSubscriptionCompleted extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.EventStore.Client.Messages.UpdatePersistentSubscriptionCompleted.UpdatePersistentSubscriptionResult result = 1;</code>
     */
    protected $result = 0;
    /**
     * Generated from protobuf field <code>string reason = 2;</code>
     */
    protected $reason = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $result
     *     @type string $reason
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\ClientMessageDtos::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.EventStore.Client.Messages.UpdatePersistentSubscriptionCompleted.UpdatePersistentSubscriptionResult result = 1;</code>
     * @return int
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Generated from protobuf field <code>.EventStore.Client.Messages.UpdatePersistentSubscriptionCompleted.UpdatePersistentSubscriptionResult result = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setResult($var)
    {
        GPBUtil::checkEnum($var, \GRPC\EventStore\Client\UpdatePersistentSubscriptionCompleted\UpdatePersistentSubscriptionResult::class);
        $this->result = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string reason = 2;</code>
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Generated from protobuf field <code>string reason = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setReason($var)
    {
        GPBUtil::checkString($var, True);
        $this->reason = $var;

        return $this;
    }

}

