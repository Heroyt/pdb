<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: persistent.proto

namespace GRPC\EventStore\PersistentSubscriptions\CreateReq;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>event_store.client.persistent_subscriptions.CreateReq.AllOptions</code>
 */
class AllOptions extends \Google\Protobuf\Internal\Message
{
    protected $all_option;
    protected $filter_option;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \GRPC\EventStore\PersistentSubscriptions\CreateReq\Position $position
     *     @type \GRPC\EventStore\Shared\PBEmpty $start
     *     @type \GRPC\EventStore\Shared\PBEmpty $end
     *     @type \GRPC\EventStore\PersistentSubscriptions\CreateReq\AllOptions\FilterOptions $filter
     *     @type \GRPC\EventStore\Shared\PBEmpty $no_filter
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Persistent::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.persistent_subscriptions.CreateReq.Position position = 1;</code>
     * @return \GRPC\EventStore\PersistentSubscriptions\CreateReq\Position|null
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
     * Generated from protobuf field <code>.event_store.client.persistent_subscriptions.CreateReq.Position position = 1;</code>
     * @param \GRPC\EventStore\PersistentSubscriptions\CreateReq\Position $var
     * @return $this
     */
    public function setPosition($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\PersistentSubscriptions\CreateReq\Position::class);
        $this->writeOneof(1, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty start = 2;</code>
     * @return \GRPC\EventStore\Shared\PBEmpty|null
     */
    public function getStart()
    {
        return $this->readOneof(2);
    }

    public function hasStart()
    {
        return $this->hasOneof(2);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty start = 2;</code>
     * @param \GRPC\EventStore\Shared\PBEmpty $var
     * @return $this
     */
    public function setStart($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Shared\PBEmpty::class);
        $this->writeOneof(2, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty end = 3;</code>
     * @return \GRPC\EventStore\Shared\PBEmpty|null
     */
    public function getEnd()
    {
        return $this->readOneof(3);
    }

    public function hasEnd()
    {
        return $this->hasOneof(3);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty end = 3;</code>
     * @param \GRPC\EventStore\Shared\PBEmpty $var
     * @return $this
     */
    public function setEnd($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Shared\PBEmpty::class);
        $this->writeOneof(3, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>.event_store.client.persistent_subscriptions.CreateReq.AllOptions.FilterOptions filter = 4;</code>
     * @return \GRPC\EventStore\PersistentSubscriptions\CreateReq\AllOptions\FilterOptions|null
     */
    public function getFilter()
    {
        return $this->readOneof(4);
    }

    public function hasFilter()
    {
        return $this->hasOneof(4);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.persistent_subscriptions.CreateReq.AllOptions.FilterOptions filter = 4;</code>
     * @param \GRPC\EventStore\PersistentSubscriptions\CreateReq\AllOptions\FilterOptions $var
     * @return $this
     */
    public function setFilter($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\PersistentSubscriptions\CreateReq\AllOptions\FilterOptions::class);
        $this->writeOneof(4, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty no_filter = 5;</code>
     * @return \GRPC\EventStore\Shared\PBEmpty|null
     */
    public function getNoFilter()
    {
        return $this->readOneof(5);
    }

    public function hasNoFilter()
    {
        return $this->hasOneof(5);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty no_filter = 5;</code>
     * @param \GRPC\EventStore\Shared\PBEmpty $var
     * @return $this
     */
    public function setNoFilter($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Shared\PBEmpty::class);
        $this->writeOneof(5, $var);

        return $this;
    }

    /**
     * @return string
     */
    public function getAllOption()
    {
        return $this->whichOneof("all_option");
    }

    /**
     * @return string
     */
    public function getFilterOption()
    {
        return $this->whichOneof("filter_option");
    }

}

