<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: persistent.proto

namespace GRPC\EventStore\PersistentSubscriptions;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>event_store.client.persistent_subscriptions.ListReq</code>
 */
class ListReq extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.event_store.client.persistent_subscriptions.ListReq.Options options = 1;</code>
     */
    protected $options = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \GRPC\EventStore\PersistentSubscriptions\ListReq\Options $options
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Persistent::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.persistent_subscriptions.ListReq.Options options = 1;</code>
     * @return \GRPC\EventStore\PersistentSubscriptions\ListReq\Options|null
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Generated from protobuf field <code>.event_store.client.persistent_subscriptions.ListReq.Options options = 1;</code>
     * @param \GRPC\EventStore\PersistentSubscriptions\ListReq\Options $var
     * @return $this
     */
    public function setOptions($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\PersistentSubscriptions\ListReq\Options::class);
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

}

